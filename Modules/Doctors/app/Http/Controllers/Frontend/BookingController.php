<?php

namespace Modules\Doctors\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Doctor;
use App\Models\Schedule;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    /**
     * Show booking page with available time slots.
     */
    public function index($doctor_id)
    {
        $doctor = Doctor::with(['user', 'speciality', 'schedules'])->findOrFail($doctor_id);

        // Fetch doctor's off days for the next 30 days
        $offDates = \App\Models\DoctorOffDay::where('doctor_id', $doctor_id)
            ->where('off_date', '>=', today())
            ->where('off_date', '<=', now()->addDays(30))
            ->pluck('off_date')
            ->map(fn($d) => $d->format('Y-m-d'))
            ->toArray();

        // Get days doctor has schedule set
        $scheduledDays = $doctor->schedules->pluck('day')->unique()->toArray();

        // Generate available dates: scan next 30 days, collect up to 7 available ones
        $dates = [];
        for ($i = 0; $i < 30 && count($dates) < 7; $i++) {
            $date = now()->addDays($i);
            $dayName = strtolower($date->format('l'));
            $dateStr = $date->format('Y-m-d');

            // Skip if doctor has no schedule for this day of the week
            if (!in_array($dayName, $scheduledDays)) {
                continue;
            }

            // Skip if this specific date is an off day
            if (in_array($dateStr, $offDates)) {
                continue;
            }

            $dates[] = $date;
        }

        // Fetch booked slots for available dates only
        $dateStrings = array_map(fn($d) => $d->format('Y-m-d'), $dates);

        $bookedAppointments = Appointment::where('doctor_id', $doctor_id)
            ->whereIn('appointment_date', $dateStrings)
            ->whereIn('status', ['pending', 'confirmed'])
            ->get(['appointment_date', 'appointment_time', 'type']);

        // Separate booked slots by type so offline does NOT block online and vice versa
        $bookedSlotsOffline = [];
        $bookedSlotsOnline  = [];
        foreach ($bookedAppointments as $appt) {
            $dateKey = $appt->appointment_date->format('Y-m-d');
            $timeVal = $appt->appointment_time->format('H:i:s');
            if ($appt->type === 'online') {
                $bookedSlotsOnline[$dateKey][] = $timeVal;
            } else {
                $bookedSlotsOffline[$dateKey][] = $timeVal;
            }
        }

        return view('frontend.booking', compact('doctor', 'dates', 'bookedSlotsOffline', 'bookedSlotsOnline', 'offDates'));
    }

    /**
     * Process booking form and store details in session.
     */
    public function bookAppointment(Request $request, $doctor_id)
    {
        $request->validate([
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
            'type' => 'required|in:online,offline',
        ]);

        $doctor = Doctor::findOrFail($doctor_id);

        // Server-side double-booking prevention (type-aware)
        $alreadyBooked = Appointment::where('doctor_id', $doctor_id)
            ->where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->where('type', $request->type)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($alreadyBooked) {
            return redirect()->back()
                ->withErrors(['appointment_time' => 'This time slot is already booked. Please select a different time.'])
                ->withInput();
        }

        // ---- Bug Fix #1: Use correct fee column from schema ----
        $fee = match ($request->type) {
            'online' => $doctor->online_fee ?? $doctor->consultation_fee ?? 0,
            default  => $doctor->consultation_fee ?? 0,
        };

        $bookingDetails = [
            'doctor_id' => $doctor_id,
            'date' => $request->appointment_date,
            'time' => $request->appointment_time,
            'type' => $request->type,
            'fee' => $fee,
        ];

        // Store booking details in session
        session(['booking_details' => $bookingDetails]);

        // Also store as a fallback cookie (expires in 30 minutes)
        $cookie = cookie('booking_details', json_encode($bookingDetails), 30);

        return redirect()->route('checkout')->withCookie($cookie);
    }

    /**
     * Show checkout page.
     */
    public function checkout()
    {
        $booking = session('booking_details');
        if (!$booking) {
            $cookieData = request()->cookie('booking_details');
            $booking = $cookieData ? json_decode($cookieData, true) : null;
        }

        if (!$booking) {
            return redirect()->route('home');
        }

        $doctor = Doctor::with('user', 'speciality')->findOrFail($booking['doctor_id']);

        return view('frontend.checkout', compact('doctor', 'booking'));
    }

    /**
     * Process payment and create appointment.
     */
    public function processPayment(Request $request)
    {
        $booking = session('booking_details');
        if (!$booking) {
            $cookieData = $request->cookie('booking_details');
            $booking = $cookieData ? json_decode($cookieData, true) : null;
        }

        if (!$booking) {
            return redirect()->route('home');
        }

        $user = Auth::user();

        if (!$user) {
            // Guest Checkout — auto-create user & patient profile
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
            ]);

            // Check if user already exists
            if (User::where('email', $request->email)->exists()) {
                return redirect()->back()
                    ->withErrors(['email' => 'This email is already registered. Please login to continue.'])
                    ->withInput();
            }

            // Create User
            $user = User::create([
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'password' => Hash::make(Str::random(10)),
                'role' => 'patient',
            ]);

            // Create Patient Profile
            Patient::create([
                'user_id' => $user->id,
                'phone' => $request->phone,
                'address' => '',
            ]);

            // Login the user automatically
            Auth::login($user);
        }

        // Ensure user has patient profile
        if (!$user->patient) {
            Patient::create([
                'user_id' => $user->id,
                'address' => '',
            ]);
            $user->refresh();
        }

        // Double-booking check at creation time (type-aware)
        $alreadyBooked = Appointment::where('doctor_id', $booking['doctor_id'])
            ->where('appointment_date', $booking['date'])
            ->where('appointment_time', $booking['time'])
            ->where('type', $booking['type'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($alreadyBooked) {
            session()->forget('booking_details');
            \Illuminate\Support\Facades\Cookie::queue(\Illuminate\Support\Facades\Cookie::forget('booking_details'));
            return redirect()->route('home')
                ->with('error', 'Sorry, this time slot was just booked by someone else. Please try again.');
        }

        $meeting_link = null;
        $token_number = null;

        // Fetch Doctor for details
        $doctor = Doctor::with('user')->findOrFail($booking['doctor_id']);

        if ($booking['type'] === 'online') {
            // Generate Jitsi Meet link — no credentials needed!
            $videoService = new \App\Services\VideoCallService();
            $meeting_link = $videoService->createMeeting(
                $doctor->user->name,
                $booking['date'],
                $booking['time']
            );
        } elseif ($booking['type'] === 'offline') {
            // Generate Token Number
            $count = Appointment::where('doctor_id', $booking['doctor_id'])
                ->where('appointment_date', $booking['date'])
                ->where('type', 'offline')
                ->count();
            $token_number = 'TKN-' . ($count + 1);
        }

        $appointment = Appointment::create([
            'doctor_id' => $booking['doctor_id'],
            'patient_id' => $user->patient->id,
            'appointment_date' => $booking['date'],
            'appointment_time' => $booking['time'],
            'status' => 'pending',
            'type' => $booking['type'],
            'meeting_link' => $meeting_link,
            'token_number' => $token_number,
            'fee' => $booking['fee'],
            'reason' => 'Consultation',
        ]);

        if ($request->payment_method === 'sslcommerz') {
            $store_id = \App\Models\SiteSetting::get('sslcz_store_id', env('SSLCZ_STORE_ID', 'testbox'));
            $store_password = \App\Models\SiteSetting::get('sslcz_store_password', env('SSLCZ_STORE_PASSWORD', 'qwerty'));
            $is_testmode = \App\Models\SiteSetting::get('sslcz_testmode', '1') == '1';

            $post_data = array();
            $post_data['store_id'] = $store_id;
            $post_data['store_passwd'] = $store_password;
            $post_data['total_amount'] = $booking['fee'];
            $post_data['currency'] = "BDT";
            $post_data['tran_id'] = "APT-" . $appointment->id;
            $post_data['success_url'] = url('/sslcommerz/success');
            $post_data['fail_url'] = url('/sslcommerz/fail');
            $post_data['cancel_url'] = url('/sslcommerz/cancel');
            
            # CUSTOMER INFORMATION
            $post_data['cus_name'] = $user->name;
            $post_data['cus_email'] = $user->email;
            $post_data['cus_add1'] = "Dhaka";
            $post_data['cus_city'] = "Dhaka";
            $post_data['cus_postcode'] = "1000";
            $post_data['cus_country'] = "Bangladesh";
            $post_data['cus_phone'] = $request->phone ?? '01711111111';
            
            $post_data['shipping_method'] = "NO";
            $post_data['product_name'] = "Appointment Booking";
            $post_data['product_category'] = "Medical Service";
            $post_data['product_profile'] = "non-physical-goods";

            $apiUrl = $is_testmode ? "https://sandbox.sslcommerz.com/gwprocess/v4/api.php" : "https://securepay.sslcommerz.com/gwprocess/v4/api.php";
            
            $response = \Illuminate\Support\Facades\Http::asForm()->post($apiUrl, $post_data);
            $sslcz = $response->json();

            if (isset($sslcz['GatewayPageURL'])) {
                session()->forget('booking_details');
                \Illuminate\Support\Facades\Cookie::queue(\Illuminate\Support\Facades\Cookie::forget('booking_details'));
                return redirect($sslcz['GatewayPageURL']);
            } else {
                return back()->withErrors(['payment' => 'SSLCommerz Gateway Error: ' . ($sslcz['failedreason'] ?? 'Unknown error')]);
            }
        }

        // Clear session and cookie for other payment methods (if any)
        $appointment->update(['status' => 'confirmed']);
        session()->forget('booking_details');
        \Illuminate\Support\Facades\Cookie::queue(\Illuminate\Support\Facades\Cookie::forget('booking_details'));

        // Send Email Notification
        if ($appointment->patient && $appointment->patient->user) {
            \Illuminate\Support\Facades\Mail::to($appointment->patient->user->email)->send(new \App\Mail\AppointmentBookedMail($appointment));
        }

        $flashData = [
            'meeting_link' => $meeting_link,
            'token_number' => $token_number,
            'type' => $booking['type'],
            'doctor_name' => $doctor->user->name,
            'doctor_user_id' => $doctor->user_id,
            'date' => $booking['date'],
            'time' => $booking['time'],
        ];

        return redirect()->route('booking.success')->with($flashData);
    }
}

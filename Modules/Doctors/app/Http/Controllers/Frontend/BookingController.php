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
    /**
     * Show booking page with available time slots.
     */
    public function index($doctor_id)
    {
        $doctor = Doctor::with(['user', 'speciality', 'schedules'])->findOrFail($doctor_id);

        // Fetch doctor's off days from today onwards
        $offDates = \App\Models\DoctorOffDay::where('doctor_id', $doctor_id)
            ->where('off_date', '>=', today())
            ->pluck('off_date')
            ->map(fn($d) => $d->format('Y-m-d'))
            ->toArray();

        // Get days doctor has schedule set (e.g. ['monday', 'wednesday'])
        $scheduledDays = $doctor->schedules->pluck('day')->map(fn($d) => strtolower($d))->unique()->values()->toArray();

        // Years: Current year + next 2 years
        $currentYear = (int) date('Y');
        $availableYears = [$currentYear, $currentYear + 1, $currentYear + 2];

        // Pick initial available date (today or first available visiting date)
        $initialDate = null;
        for ($i = 0; $i < 60; $i++) {
            $checkDate = now()->addDays($i);
            $checkDayName = strtolower($checkDate->format('l'));
            $checkDateStr = $checkDate->format('Y-m-d');

            if (in_array($checkDayName, $scheduledDays) && !in_array($checkDateStr, $offDates)) {
                $initialDate = $checkDate;
                break;
            }
        }

        if (!$initialDate) {
            $initialDate = now();
        }

        $initialDateKey = $initialDate->format('Y-m-d');
        $initialSlots = $this->generateSlotsForDate($doctor, $initialDateKey);

        $dates = [$initialDate];
        $subdividedSlots = [
            'offline' => [$initialDateKey => $initialSlots['offline']],
            'online'  => [$initialDateKey => $initialSlots['online']],
        ];

        return view('frontend.booking', compact(
            'doctor',
            'dates',
            'subdividedSlots',
            'offDates',
            'scheduledDays',
            'availableYears',
            'initialDate'
        ));
    }

    /**
     * AJAX endpoint to fetch slots for a specific date.
     */
    public function getSlots(Request $request, $doctor_id)
    {
        $doctor = Doctor::with('schedules')->findOrFail($doctor_id);
        $dateStr = $request->query('date');

        if (!$dateStr || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateStr)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid date format. Expected YYYY-MM-DD.'
            ], 422);
        }

        $date = \Carbon\Carbon::parse($dateStr);
        $today = today();

        // Check if date is in past
        if ($date->lt($today)) {
            return response()->json([
                'status' => 'past_date',
                'message' => 'Cannot book appointments for past dates.',
                'slots' => ['offline' => [], 'online' => []]
            ]);
        }

        // Check off-day
        $isOffDay = \App\Models\DoctorOffDay::where('doctor_id', $doctor_id)
            ->where('off_date', $dateStr)
            ->exists();

        if ($isOffDay) {
            return response()->json([
                'status' => 'off_day',
                'message' => 'Doctor is unavailable on this date (marked as off-day).',
                'slots' => ['offline' => [], 'online' => []]
            ]);
        }

        // Check day schedule
        $dayName = strtolower($date->format('l'));
        $daySchedules = $doctor->schedules->where('day', $dayName);

        if ($daySchedules->isEmpty()) {
            return response()->json([
                'status' => 'no_schedule',
                'message' => 'Doctor does not have consultation hours on ' . ucfirst($dayName) . 's.',
                'slots' => ['offline' => [], 'online' => []]
            ]);
        }

        $slots = $this->generateSlotsForDate($doctor, $dateStr);

        return response()->json([
            'status' => 'success',
            'date' => $dateStr,
            'formatted_date' => $date->format('l, d M Y'),
            'day_name' => $date->format('l'),
            'slots' => $slots
        ]);
    }

    /**
     * Helper to subdivide schedules into slots for a specific date.
     */
    private function generateSlotsForDate($doctor, $dateStr)
    {
        $date = \Carbon\Carbon::parse($dateStr);
        $dayName = strtolower($date->format('l'));

        $bookedAppointments = Appointment::where('doctor_id', $doctor->id)
            ->where('appointment_date', $dateStr)
            ->whereIn('status', ['pending', 'confirmed'])
            ->get(['appointment_time', 'type']);

        $bookedOnline  = [];
        $bookedOffline = [];
        foreach ($bookedAppointments as $appt) {
            $timeVal = $appt->appointment_time->format('H:i');
            if ($appt->type === 'online') {
                $bookedOnline[] = $timeVal;
            } else {
                $bookedOffline[] = $timeVal;
            }
        }

        $subdividedSlots = [
            'offline' => [],
            'online'  => [],
        ];

        foreach (['offline', 'online'] as $type) {
            $daySchedules = $doctor->schedules->where('day', $dayName)->where('type', $type);

            foreach ($daySchedules as $sched) {
                if (!$sched->start_time || !$sched->end_time) {
                    continue;
                }

                $start = \Carbon\Carbon::parse($dateStr . ' ' . $sched->start_time);
                $end   = \Carbon\Carbon::parse($dateStr . ' ' . $sched->end_time);

                $timeKey = $start->format('H:i:s');
                $isBooked = false;

                // For online video, 1 appointment per slot
                if ($type === 'online') {
                    $isBooked = in_array($start->format('H:i'), $bookedOnline);
                }

                // If date is today, check if schedule end time has already passed
                if ($date->isToday() && $end->lt(now())) {
                    $isBooked = true;
                }

                $subdividedSlots[$type][] = [
                    'time_key'   => $start->format('H:i:s'),
                    'display'    => $start->format('g:i A') . ' - ' . $end->format('g:i A'),
                    'is_booked'  => $isBooked,
                ];
            }
        }

        return $subdividedSlots;
    }


    /**
     * Process booking form and store details in session (or confirm immediately if patient details are provided).
     */
    public function bookAppointment(Request $request, $doctor_id)
    {
        $request->validate([
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
            'type' => 'required|in:online,offline',
        ]);

        $doctor = Doctor::findOrFail($doctor_id);

        // Server-side double-booking prevention (for online video consultation)
        $alreadyBooked = false;
        if ($request->type === 'online') {
            $alreadyBooked = Appointment::where('doctor_id', $doctor_id)
                ->where('appointment_date', $request->appointment_date)
                ->where('appointment_time', $request->appointment_time)
                ->where('type', 'online')
                ->whereIn('status', ['pending', 'confirmed'])
                ->exists();
        }

        if ($alreadyBooked) {
            return redirect()->back()
                ->withErrors(['appointment_time' => 'This online consultation time is already booked. Please select a different time.'])
                ->withInput();
        }

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

        // Store booking details in session and cookie
        session(['booking_details' => $bookingDetails]);
        $cookie = cookie('booking_details', json_encode($bookingDetails), 30);

        // If patient submitted contact details directly on the 1-page booking form
        if ($request->filled('patient_name') && $request->filled('phone')) {
            return $this->executeBooking($request, $bookingDetails, $doctor);
        }

        return redirect()->route('checkout')->withCookie($cookie);
    }

    /**
     * Helper to find or create a user and attached patient profile (phone-first, no email block).
     */
    protected function findOrCreatePatientUser(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            $name = trim($request->input('patient_name', $request->input('first_name', '') . ' ' . $request->input('last_name', '')));
            if (empty($name)) {
                $name = 'Guest Patient';
            }

            $rawPhone = preg_replace('/[^0-9]/', '', $request->input('phone', ''));
            $email = $request->input('email');

            // 1. Try finding by provided email
            if (!empty($email)) {
                $user = User::where('email', $email)->first();
            }

            // 2. Try finding existing patient by phone
            if (!$user && !empty($rawPhone)) {
                $existingPatient = Patient::where('phone', $rawPhone)->latest()->first();
                if ($existingPatient && $existingPatient->user) {
                    $user = $existingPatient->user;
                }
            }

            // 3. Create new user if not found
            if (!$user) {
                $emailToUse = !empty($email) ? $email : 'patient_' . ($rawPhone ?: Str::random(8)) . '@abcsheba.com';
                
                // Ensure unique email
                if (User::where('email', $emailToUse)->exists()) {
                    $emailToUse = 'patient_' . ($rawPhone ?: Str::random(6)) . '_' . time() . '@abcsheba.com';
                }

                $passwordToSet = !empty($rawPhone) ? $rawPhone : Str::random(12);

                $user = User::create([
                    'name' => $name,
                    'email' => $emailToUse,
                    'password' => Hash::make($passwordToSet),
                    'role' => 'patient',
                ]);
            }

            // Auto-login so subsequent bookings and chat work seamlessly
            Auth::login($user);
        }

        // Ensure user has patient profile
        if (!$user->patient) {
            Patient::create([
                'user_id' => $user->id,
                'phone' => $request->input('phone', ''),
                'address' => '',
            ]);
            $user->refresh();
        } elseif ($request->filled('phone') && empty($user->patient->phone)) {
            $user->patient->update(['phone' => $request->input('phone')]);
        }

        return $user;
    }

    /**
     * Internal handler to execute appointment creation and dispatch payment or confirmation.
     */
    protected function executeBooking(Request $request, array $booking, Doctor $doctor)
    {
        $user = $this->findOrCreatePatientUser($request);

        // Double-booking check at execution time (for online video consultation)
        $alreadyBooked = false;
        if ($booking['type'] === 'online') {
            $alreadyBooked = Appointment::where('doctor_id', $booking['doctor_id'])
                ->where('appointment_date', $booking['date'])
                ->where('appointment_time', $booking['time'])
                ->where('type', 'online')
                ->whereIn('status', ['pending', 'confirmed'])
                ->exists();
        }

        if ($alreadyBooked) {
            session()->forget('booking_details');
            \Illuminate\Support\Facades\Cookie::queue(\Illuminate\Support\Facades\Cookie::forget('booking_details'));
            return redirect()->route('doctors.profile', $doctor->id)
                ->with('error', 'Sorry, this online slot was just booked by someone else. Please select another slot.');
        }

        $meeting_link = null;
        $token_number = null;

        if ($booking['type'] === 'online') {
            $videoService = new \App\Services\VideoCallService();
            $meeting_link = $videoService->createMeeting(
                $doctor->user->name ?? 'Doctor',
                $booking['date'],
                $booking['time']
            );
        } elseif ($booking['type'] === 'offline') {
            $count = Appointment::where('doctor_id', $booking['doctor_id'])
                ->where('appointment_date', $booking['date'])
                ->where('type', 'offline')
                ->count();
            $token_number = 'TKN-' . ($count + 1);
        }

        // Handle uploaded previous reports / prescriptions
        $uploadedReports = [];
        if ($request->hasFile('reports')) {
            $destinationPath = public_path('uploads/appointments/reports');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            foreach ($request->file('reports') as $file) {
                if ($file && $file->isValid()) {
                    $originalName = $file->getClientOriginalName();
                    $extension = strtolower($file->getClientOriginalExtension());
                    if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'pdf'])) {
                        $safeName = time() . '_' . Str::random(8) . '.' . $extension;
                        $file->move($destinationPath, $safeName);

                        $uploadedReports[] = [
                            'name' => $originalName,
                            'path' => 'uploads/appointments/reports/' . $safeName,
                            'type' => in_array($extension, ['jpg', 'jpeg', 'png', 'webp']) ? 'image' : 'pdf',
                            'size' => round(filesize($destinationPath . '/' . $safeName) / 1024, 1) . ' KB',
                        ];
                    }
                }
            }
        }

        $paymentMethod = $request->input('payment_method', ($booking['type'] === 'offline' ? 'pay_at_chamber' : 'bkash'));

        // Manual mobile banking validation for online consultation
        if ($booking['type'] === 'online' && in_array($paymentMethod, ['bkash', 'nagad', 'rocket'])) {
            $request->validate([
                'sender_number' => 'required|string|max:20',
                'transaction_id' => 'required|string|max:50',
            ], [
                'sender_number.required' => 'যে নম্বর থেকে টাকা পাঠিয়েছেন (Sender Number) তা দেওয়া আবশ্যক।',
                'transaction_id.required' => 'পেমেন্টের Transaction ID (TrxID) দেওয়া আবশ্যক।',
            ]);
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
            'payment_method' => $booking['type'] === 'offline' ? 'pay_at_chamber' : $paymentMethod,
            'payment_status' => $booking['type'] === 'offline' ? 'pay_at_chamber' : 'pending',
            'sender_number' => $request->input('sender_number'),
            'transaction_id' => $request->input('transaction_id'),
            'reason' => $request->input('reason', 'Consultation'),
            'reports' => !empty($uploadedReports) ? $uploadedReports : null,
        ]);

        // Online payment with SSLCommerz (if specifically selected)
        if ($paymentMethod === 'sslcommerz') {
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
            $post_data['cus_phone'] = $request->input('phone', '01711111111');
            
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

        // For chamber visits (pay_at_chamber), auto-confirm immediately
        if ($booking['type'] === 'offline') {
            $appointment->update(['status' => 'confirmed']);
        }

        session()->forget('booking_details');
        \Illuminate\Support\Facades\Cookie::queue(\Illuminate\Support\Facades\Cookie::forget('booking_details'));

        // Send Email Notification if email is valid
        if ($appointment->patient && $appointment->patient->user && filter_var($appointment->patient->user->email, FILTER_VALIDATE_EMAIL)) {
            try {
                \Illuminate\Support\Facades\Mail::to($appointment->patient->user->email)->send(new \App\Mail\AppointmentBookedMail($appointment));
            } catch (\Exception $e) {
                // Log and continue gracefully
                \Illuminate\Support\Facades\Log::warning('Appointment email could not be sent: ' . $e->getMessage());
            }
        }

        $flashData = [
            'appointment_id' => $appointment->id,
            'meeting_link' => $meeting_link,
            'token_number' => $token_number,
            'type' => $booking['type'],
            'doctor_name' => $doctor->user->name ?? 'Doctor',
            'doctor_user_id' => $doctor->user_id,
            'date' => $booking['date'],
            'time' => $booking['time'],
            'payment_method' => $appointment->payment_method,
            'payment_status' => $appointment->payment_status,
            'transaction_id' => $appointment->transaction_id,
            'sender_number' => $appointment->sender_number,
            'patient_name' => $user->name ?? $request->input('patient_name', 'Patient'),
            'patient_phone' => $request->input('phone', optional($user->patient)->phone),
            'clinic_name' => $doctor->clinic_name ?? 'Chamber',
            'clinic_address' => $doctor->clinic_address ?? '',
        ];

        return redirect()->route('booking.success')->with($flashData);
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
     * Process payment and create appointment from checkout page.
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

        $doctor = Doctor::with('user')->findOrFail($booking['doctor_id']);

        return $this->executeBooking($request, $booking, $doctor);
    }
}


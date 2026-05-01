<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
            ->get(['appointment_date', 'appointment_time']);

        $bookedSlots = [];
        foreach ($bookedAppointments as $appt) {
            $bookedSlots[$appt->appointment_date->format('Y-m-d')][] = $appt->appointment_time->format('H:i:s');
        }

        return view('frontend.booking', compact('doctor', 'dates', 'bookedSlots', 'offDates'));
    }

    public function bookAppointment(Request $request, $doctor_id)
    {
        $request->validate([
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
            'type' => 'required|in:online,offline',
        ]);

        $doctor = Doctor::findOrFail($doctor_id);

        // Server-side double-booking prevention
        $alreadyBooked = Appointment::where('doctor_id', $doctor_id)
            ->where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($alreadyBooked) {
            return redirect()->back()
                ->withErrors(['appointment_time' => 'This time slot is already booked. Please select a different time.'])
                ->withInput();
        }

        // Use correct fee columns from schema
        $fee = match ($request->type) {
            'online' => $doctor->online_fee ?? $doctor->consultation_fee ?? 0,
            default  => $doctor->consultation_fee ?? 0,
        };

        // Store booking details in session
        session([
            'booking_details' => [
                'doctor_id' => $doctor_id,
                'date' => $request->appointment_date,
                'time' => $request->appointment_time,
                'type' => $request->type,
                'fee' => $fee,
            ]
        ]);

        return redirect()->route('checkout');
    }

    public function checkout()
    {
        $booking = session('booking_details');
        if (!$booking) {
            return redirect()->route('home');
        }

        $doctor = Doctor::with('user', 'speciality')->findOrFail($booking['doctor_id']);

        return view('frontend.checkout', compact('doctor', 'booking'));
    }

    public function processPayment(Request $request)
    {
        $booking = session('booking_details');
        if (!$booking) {
            return redirect()->route('home');
        }

        $user = Auth::user();

        if (!$user) {
            // Guest Checkout Logic
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
            ]);

            // Check if user exists
            if (User::where('email', $request->email)->exists()) {
                return redirect()->back()->withErrors(['email' => 'This email is already registered. Please login to continue.'])->withInput();
            }

            // Create User
            $user = User::create([
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'password' => Hash::make(Str::random(10)), // Random password
                'role' => 'patient',
            ]);

            // Create Patient Profile
            Patient::create([
                'user_id' => $user->id,
                'phone' => $request->phone,
                'address' => '', // Optional
            ]);

            // Login the user automatically
            Auth::login($user);
        }

        // Ensure user has patient profile (if logged in but no profile)
        if (!$user->patient) {
            Patient::create([
                'user_id' => $user->id,
                'address' => '',
            ]);
            $user->refresh();
        }

        // Double-booking check at creation time (race condition guard)
        $alreadyBooked = Appointment::where('doctor_id', $booking['doctor_id'])
            ->where('appointment_date', $booking['date'])
            ->where('appointment_time', $booking['time'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($alreadyBooked) {
            session()->forget('booking_details');
            return redirect()->route('home')
                ->with('error', 'Sorry, this time slot was just booked by someone else. Please try again.');
        }

        $meeting_link = null;
        $token_number = null;

        // Fetch Doctor for details
        $doctor = Doctor::with('user')->findOrFail($booking['doctor_id']);

        if ($booking['type'] === 'online') {
            try {
                $meetService = new \App\Services\GoogleMeetService();
                $meeting_link = $meetService->createMeeting(
                    'Consultation with Dr. ' . $doctor->user->name,
                    $booking['date'] . ' ' . $booking['time']
                );
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Google Meet Error: ' . $e->getMessage());
                $meeting_link = null;
            }
        } elseif ($booking['type'] === 'offline') {
            // Generate Token Number
            $count = Appointment::where('doctor_id', $booking['doctor_id'])
                ->where('appointment_date', $booking['date'])
                ->where('type', 'offline')
                ->count();
            $token_number = 'TKN-' . ($count + 1);
        }

        Appointment::create([
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

        // Clear session
        session()->forget('booking_details');

        // Build flash data with optional warning for failed Meet link
        $flashData = [
            'meeting_link' => $meeting_link,
            'token_number' => $token_number,
            'type' => $booking['type'],
            'doctor_name' => $doctor->user->name,
            'date' => $booking['date'],
            'time' => $booking['time'],
        ];

        if ($booking['type'] === 'online' && !$meeting_link) {
            $flashData['warning'] = 'Your appointment is booked, but the video meeting link could not be generated. The doctor will share the link with you.';
        }

        return redirect()->route('booking.success')->with($flashData);
    }
}

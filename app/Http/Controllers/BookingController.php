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

        $offDates = \App\Models\DoctorOffDay::where('doctor_id', $doctor_id)
            ->where('off_date', '>=', today())
            ->pluck('off_date')
            ->map(fn($d) => $d->format('Y-m-d'))
            ->toArray();

        $scheduledDays = $doctor->schedules->pluck('day')->map(fn($d) => strtolower($d))->unique()->values()->toArray();

        $currentYear = (int) date('Y');
        $availableYears = [$currentYear, $currentYear + 1, $currentYear + 2];

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

        if ($date->lt($today)) {
            return response()->json([
                'status' => 'past_date',
                'message' => 'Cannot book appointments for past dates.',
                'slots' => ['offline' => [], 'online' => []]
            ]);
        }

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

    private function generateSlotsForDate($doctor, $dateStr)
    {
        $date = \Carbon\Carbon::parse($dateStr);
        $dayName = strtolower($date->format('l'));

        $bookedAppointments = Appointment::where('doctor_id', $doctor->id)
            ->where('appointment_date', $dateStr)
            ->whereIn('status', ['pending', 'confirmed'])
            ->get(['appointment_time', 'type']);

        $bookedOffline = [];
        $bookedOnline  = [];
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

                // Both offline and online use serial token queues within doctor's schedule hours
                // Slots are only marked booked if the time has already passed today
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

    public function bookAppointment(Request $request, $doctor_id)
    {
        $request->validate([
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
            'type' => 'required|in:online,offline',
        ]);

        $doctor = Doctor::findOrFail($doctor_id);

        // Check if selected time slot has already passed for today
        $dateObj = \Carbon\Carbon::parse($request->appointment_date);
        if ($dateObj->isToday()) {
            $timeParts = explode(':', $request->appointment_time);
            if (count($timeParts) >= 2) {
                // If schedule has passed
                $schedule = $doctor->schedules->where('day', strtolower($dateObj->format('l')))->where('type', $request->type)->first();
                if ($schedule && $schedule->end_time && \Carbon\Carbon::parse($dateObj->format('Y-m-d') . ' ' . $schedule->end_time)->lt(now())) {
                    return redirect()->back()
                        ->withErrors(['appointment_time' => 'This consultation schedule has already ended for today.'])
                        ->withInput();
                }
            }
        }

        $selectedClinicName = null;
        if ($request->type === 'offline') {
            $locations = $doctor->clinic_locations;
            $idx = $request->input('clinic_index', 0);
            $loc = $locations[$idx] ?? ($locations[0] ?? null);
            if ($loc) {
                $selectedClinicName = $loc['name'] ?? null;
                $fee = (!empty($loc['fee']) && is_numeric($loc['fee']) && (float)$loc['fee'] > 0)
                    ? (float)$loc['fee']
                    : (float)($doctor->consultation_fee ?? 0);
            } else {
                $fee = (float)($doctor->consultation_fee ?? 0);
            }
        } else {
            $fee = (float)($doctor->online_fee ?? $doctor->consultation_fee ?? 0);
        }

        $bookingDetails = [
            'doctor_id' => $doctor_id,
            'date' => $request->appointment_date,
            'time' => $request->appointment_time,
            'type' => $request->type,
            'fee' => $fee,
            'clinic_name' => $selectedClinicName,
        ];

        // Store booking details in session
        session(['booking_details' => $bookingDetails]);

        // Also store as a fallback cookie (expires in 30 minutes)
        $cookie = cookie('booking_details', json_encode($bookingDetails), 30);

        return redirect()->route('checkout')->withCookie($cookie);
    }

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

            // Create User with phone number as default password
            $rawPhone = preg_replace('/[^0-9]/', '', (string)$request->phone);
            $passwordToSet = !empty($rawPhone) ? $rawPhone : Str::random(10);

            $user = User::create([
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($passwordToSet),
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

        // Double-booking check removed because system is daily-based now.

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
            $count = Appointment::where('doctor_id', $booking['doctor_id'])
                ->where('appointment_date', $booking['date'])
                ->where('type', 'online')
                ->count();
            $token_number = 'ONL-' . ($count + 1);
        } elseif ($booking['type'] === 'offline') {
            // Generate Token Number
            $count = Appointment::where('doctor_id', $booking['doctor_id'])
                ->where('appointment_date', $booking['date'])
                ->where('type', 'offline')
                ->count();
            $token_number = 'TKN-' . ($count + 1);
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
            'status' => $booking['type'] === 'offline' ? 'confirmed' : 'pending',
            'type' => $booking['type'],
            'meeting_link' => $meeting_link,
            'token_number' => $token_number,
            'fee' => $booking['fee'],
            'payment_method' => $booking['type'] === 'offline' ? 'pay_at_chamber' : $paymentMethod,
            'payment_status' => $booking['type'] === 'offline' ? 'pay_at_chamber' : 'pending',
            'sender_number' => $request->input('sender_number'),
            'transaction_id' => $request->input('transaction_id'),
            'reason' => 'Consultation',
        ]);

        // Clear session and cookie
        session()->forget('booking_details');
        \Illuminate\Support\Facades\Cookie::queue(\Illuminate\Support\Facades\Cookie::forget('booking_details'));

        // Build flash data
        $flashData = [
            'appointment_id' => $appointment->id,
            'meeting_link' => $meeting_link,
            'token_number' => $token_number,
            'type' => $booking['type'],
            'doctor_name' => $doctor->user->name,
            'doctor_user_id' => $doctor->user_id,
            'date' => $booking['date'],
            'time' => $booking['time'],
            'payment_method' => $appointment->payment_method,
            'payment_status' => $appointment->payment_status,
            'transaction_id' => $appointment->transaction_id,
            'sender_number' => $appointment->sender_number,
            'patient_name' => $user->name,
            'patient_phone' => optional($user->patient)->phone,
        ];

        return redirect()->route('booking.success')->with($flashData);
    }
}

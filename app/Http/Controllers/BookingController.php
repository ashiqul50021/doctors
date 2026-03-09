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

        // Generate next 7 days dates
        $dates = [];
        for ($i = 0; $i < 7; $i++) {
            $dates[] = now()->addDays($i);
        }

        // Fetch booked slots
        $startDate = now()->format('Y-m-d');
        $endDate = now()->addDays(7)->format('Y-m-d');

        $bookedAppointments = Appointment::where('doctor_id', $doctor_id)
            ->whereBetween('appointment_date', [$startDate, $endDate])
            ->get(['appointment_date', 'appointment_time']);

        $bookedSlots = [];
        foreach ($bookedAppointments as $appt) {
            $bookedSlots[$appt->appointment_date->format('Y-m-d')][] = $appt->appointment_time->format('H:i:s'); // Store as H:i:s to match generation logic
        }

        return view('frontend.booking', compact('doctor', 'dates', 'bookedSlots'));
    }

    public function bookAppointment(Request $request, $doctor_id)
    {
        $request->validate([
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
            'type' => 'required|in:online,offline',
        ]);

        $doctor = Doctor::findOrFail($doctor_id);

        // Store booking details in session
        session([
            'booking_details' => [
                'doctor_id' => $doctor_id,
                'date' => $request->appointment_date,
                'time' => $request->appointment_time,
                'type' => $request->type,
                'fee' => $doctor->pricing === 'custom_price' ? $doctor->custom_price : 0
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
                // Fallback if Google API fails or credentials missing
                \Illuminate\Support\Facades\Log::error('Google Meet Error: ' . $e->getMessage());
                $meeting_link = null;
            }
        } elseif ($booking['type'] === 'offline') {
            // Generate Token Number: "DOC-{DoctorID}-{Count+1}"
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
            'status' => 'confirmed', // Assuming instant confirmation for now
            'type' => $booking['type'],
            'meeting_link' => $meeting_link,
            'token_number' => $token_number,
            'fee' => $booking['fee'],
            'reason' => 'Consultation', // Default
        ]);

        // Clear session
        session()->forget('booking_details');

        return redirect()->route('booking.success')->with([
            'meeting_link' => $meeting_link,
            'token_number' => $token_number,
            'type' => $booking['type'],
            'doctor_name' => $doctor->user->name,
            'date' => $booking['date'],
            'time' => $booking['time']
        ]);
    }
}

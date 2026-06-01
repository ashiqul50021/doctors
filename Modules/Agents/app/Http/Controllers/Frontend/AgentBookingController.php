<?php

namespace Modules\Agents\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Doctor;
use App\Models\Speciality;
use App\Models\Schedule;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Patient;
use Modules\Agents\Models\AgentTransaction;

class AgentBookingController extends Controller
{
    public function index(Request $request)
    {
        $agent = Auth::user()->agent;
        
        if (!$agent->can_book_appointments) {
            return redirect()->route('agent.dashboard')->with('error', 'You do not have permission to book appointments.');
        }

        $query = Doctor::with(['user', 'speciality'])->where('status', 'approved');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('speciality')) {
            $query->where('speciality_id', $request->speciality);
        }

        $doctors = $query->paginate(9);
        $specialities = Speciality::all();

        return view('agents::frontend.booking.index', compact('doctors', 'specialities'));
    }

    public function booking($doctorId)
    {
        $agent = Auth::user()->agent;
        
        if (!$agent->can_book_appointments) {
            return redirect()->route('agent.dashboard')->with('error', 'You do not have permission to book appointments.');
        }

        $doctor = Doctor::with(['user', 'speciality', 'schedules'])->findOrFail($doctorId);

        // Fetch doctor's off days for the next 30 days
        $offDates = \App\Models\DoctorOffDay::where('doctor_id', $doctorId)
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

            if (!in_array($dayName, $scheduledDays)) {
                continue;
            }

            if (in_array($dateStr, $offDates)) {
                continue;
            }

            $dates[] = $date;
        }

        $dateStrings = array_map(fn($d) => $d->format('Y-m-d'), $dates);

        $bookedAppointments = Appointment::where('doctor_id', $doctorId)
            ->whereIn('appointment_date', $dateStrings)
            ->whereIn('status', ['pending', 'confirmed'])
            ->get(['appointment_date', 'appointment_time']);

        $bookedSlots = [];
        foreach ($bookedAppointments as $appt) {
            $bookedSlots[$appt->appointment_date->format('Y-m-d')][] = $appt->appointment_time->format('H:i:s');
        }

        return view('agents::frontend.booking.slots', compact('doctor', 'dates', 'bookedSlots'));
    }

    public function submit(Request $request, $doctorId)
    {
        $agent = Auth::user()->agent;
        
        if (!$agent->can_book_appointments) {
            return redirect()->route('agent.dashboard')->with('error', 'You do not have permission to book appointments.');
        }

        $request->validate([
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'appointment_time' => ['required'],
            'type' => ['required', 'in:online,offline'],
            'patient_name' => ['required', 'string', 'max:255'],
            'patient_email' => ['required', 'email', 'max:255'],
            'patient_phone' => ['required', 'string', 'max:15'],
            'reason' => ['nullable', 'string'],
        ]);

        $doctor = Doctor::findOrFail($doctorId);

        // Check double booking
        $alreadyBooked = Appointment::where('doctor_id', $doctorId)
            ->where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($alreadyBooked) {
            return back()->withErrors(['appointment_time' => 'This slot is already booked. Please choose another slot.'])->withInput();
        }

        DB::beginTransaction();
        try {
            // Find or create Patient user
            $patientUser = User::where('email', $request->patient_email)->first();

            if (!$patientUser) {
                $patientUser = User::create([
                    'name' => $request->patient_name,
                    'email' => $request->patient_email,
                    'password' => Hash::make(Str::random(12)),
                    'role' => 'patient',
                ]);
            }

            $patient = Patient::where('user_id', $patientUser->id)->first();
            if (!$patient) {
                $patient = Patient::create([
                    'user_id' => $patientUser->id,
                    'phone' => $request->patient_phone,
                    'address' => '',
                ]);
            }

            $meetingLink = null;
            $tokenNumber = null;

            if ($request->type === 'online') {
                $videoService = new \App\Services\VideoCallService();
                $meetingLink = $videoService->createMeeting(
                    $doctor->user->name,
                    $request->appointment_date,
                    $request->appointment_time
                );
            } else {
                $count = Appointment::where('doctor_id', $doctorId)
                    ->where('appointment_date', $request->appointment_date)
                    ->where('type', 'offline')
                    ->count();
                $tokenNumber = 'TKN-' . ($count + 1);
            }

            $fee = $request->type === 'online'
                ? ($doctor->online_fee ?? $doctor->consultation_fee ?? 0)
                : ($doctor->consultation_fee ?? 0);

            // Create Appointment
            $appointment = Appointment::create([
                'doctor_id' => $doctor->id,
                'patient_id' => $patient->id,
                'agent_id' => $agent->id,
                'appointment_date' => $request->appointment_date,
                'appointment_time' => $request->appointment_time,
                'status' => 'confirmed', // Instantly confirmed by agent booking
                'type' => $request->type,
                'meeting_link' => $meetingLink,
                'token_number' => $tokenNumber,
                'fee' => $fee,
                'reason' => $request->reason ?? 'Agent Booking',
            ]);

            // Calculate Commission
            $commission = $agent->booking_commission_rate;

            // Credit Agent Wallet
            $agent->increment('wallet_balance', $commission);

            // Log Transaction
            AgentTransaction::create([
                'agent_id' => $agent->id,
                'type' => 'commission_booking',
                'amount' => $commission,
                'description' => 'Commission of ৳' . number_format($commission, 2) . ' credited for Booking #' . $appointment->id . ' (Patient: ' . $request->patient_name . ')',
                'reference_id' => $appointment->id,
                'status' => 'completed',
            ]);

            DB::commit();
            return redirect()->route('agent.dashboard')->with('success', 'Appointment booked successfully! Commission of ৳' . number_format($commission, 2) . ' has been credited to your wallet.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to book appointment: ' . $e->getMessage())->withInput();
        }
    }
}

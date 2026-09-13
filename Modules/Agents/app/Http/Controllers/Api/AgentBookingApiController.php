<?php

namespace Modules\Agents\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Doctor;
use App\Models\Appointment;
use App\Models\DoctorSchedule;
use Modules\Agents\Models\AgentTransaction;

class AgentBookingApiController extends Controller
{
    public function doctors(Request $request)
    {
        $query = Doctor::with('user', 'speciality')->where('status', 'active');

        if ($request->has('speciality_id')) {
            $query->where('speciality_id', $request->speciality_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $doctors = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $doctors
        ]);
    }

    public function slots(Request $request, $doctorId)
    {
        $doctor = Doctor::findOrFail($doctorId);
        $schedules = DoctorSchedule::where('doctor_id', $doctorId)->get();

        return response()->json([
            'success' => true,
            'doctor' => [
                'id' => $doctor->id,
                'name' => $doctor->user ? $doctor->user->name : '',
                'consultation_fee' => (float) $doctor->consultation_fee,
            ],
            'schedules' => $schedules
        ]);
    }

    public function submitBooking(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'patient_name' => 'required|string|max:255',
            'patient_phone' => 'required|string|max:20',
            'booking_date' => 'required|date',
            'time_slot' => 'required|string',
            'problem' => 'nullable|string',
        ]);

        $agent = $request->user()->agent;
        $doctor = Doctor::findOrFail($request->doctor_id);

        $bookingFee = $doctor->consultation_fee ?? 500;
        $commissionRate = (float) \App\Models\SiteSetting::get('agent_booking_commission_percent', 10.00);
        $commissionAmount = ($bookingFee * $commissionRate) / 100;

        $appointment = \Illuminate\Support\Facades\DB::transaction(function () use ($agent, $doctor, $bookingFee, $commissionAmount, $request) {
            $app = Appointment::create([
                'agent_id' => $agent->id,
                'doctor_id' => $doctor->id,
                'patient_name' => $request->patient_name,
                'patient_phone' => $request->patient_phone,
                'booking_date' => $request->booking_date,
                'time_slot' => $request->time_slot,
                'problem' => $request->problem,
                'fee' => $bookingFee,
                'status' => 'pending',
            ]);

            // Add commission to agent's wallet
            $agent->increment('wallet_balance', $commissionAmount);

            AgentTransaction::create([
                'agent_id' => $agent->id,
                'type' => 'commission_booking',
                'amount' => $commissionAmount,
                'description' => "Commission for doctor booking #{$app->id} ({$doctor->user->name})",
                'status' => 'approved',
                'reference_id' => $app->id,
            ]);

            return $app;
        });

        return response()->json([
            'success' => true,
            'message' => 'Appointment booked successfully on behalf of patient.',
            'data' => [
                'appointment_id' => $appointment->id,
                'commission_earned' => $commissionAmount,
            ]
        ]);
    }

    public function bookings(Request $request)
    {
        $agent = $request->user()->agent;

        $bookings = Appointment::with('doctor.user')
            ->where('agent_id', $agent->id)
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $bookings
        ]);
    }
}

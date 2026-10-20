<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Prescription;
use App\Services\VideoCallService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ConsultationController extends Controller
{
    /**
     * In-App Live Video Consultation Room & Waiting Room
     */
    public function room($id)
    {
        $appointment = Appointment::with(['doctor.user', 'doctor.speciality', 'patient.user'])
            ->findOrFail($id);

        $user = Auth::user();
        $isDoctor = ($user->doctor && $user->doctor->id === $appointment->doctor_id);
        $isPatient = ($user->patient && $user->patient->id === $appointment->patient_id);
        $isAdmin = ($user->role === 'admin' || !empty($user->is_admin));

        // Security check
        if (!$isDoctor && !$isPatient && !$isAdmin) {
            return redirect()->route('home')->with('error', 'You are not authorized to access this consultation room.');
        }

        // If doctor entered, set active online serving appointment in cache and confirm if pending
        if ($isDoctor) {
            Cache::put("doctor_{$appointment->doctor_id}_online_serving_appt_id", $appointment->id, now()->endOfDay());
            if ($appointment->status === 'pending') {
                $appointment->status = 'confirmed';
                $appointment->save();
            }
        }

        $servingApptId = Cache::get("doctor_{$appointment->doctor_id}_online_serving_appt_id");
        
        // If doctor or admin, always let them in; if patient, they are serving if doctor called them or if servingApptId == this appointment
        $isServing = $isDoctor || $isAdmin || ($servingApptId == $appointment->id);

        $servingAppt = $servingApptId ? Appointment::find($servingApptId) : null;
        $servingToken = $servingAppt?->token_number;

        // Count pending/confirmed patients ahead of this patient for today
        $aheadCount = 0;
        if ($isPatient && !$isServing) {
            $aheadCount = Appointment::where('doctor_id', $appointment->doctor_id)
                ->whereDate('appointment_date', $appointment->appointment_date)
                ->where('type', 'online')
                ->whereIn('status', ['pending', 'confirmed'])
                ->where('id', '<', $appointment->id)
                ->when($servingApptId, fn($q) => $q->where('id', '>', $servingApptId))
                ->count();
        }

        $videoService = new VideoCallService();
        $roomName = $appointment->meeting_link ? $videoService->getRoomName($appointment->meeting_link) : 'room-' . $appointment->id;

        $displayName = $isDoctor
            ? ('Dr. ' . ($appointment->doctor->user->name ?? 'Doctor'))
            : ($appointment->patient->user->name ?? $user->name ?? 'Patient');

        $prescription = Prescription::with('items')->where('appointment_id', $appointment->id)->first();

        // Next online patient in line for doctor's quick call button
        $nextOnlineAppt = null;
        if ($isDoctor) {
            $nextOnlineAppt = Appointment::where('doctor_id', $appointment->doctor_id)
                ->whereDate('appointment_date', $appointment->appointment_date)
                ->where('type', 'online')
                ->whereIn('status', ['pending', 'confirmed'])
                ->where('id', '!=', $appointment->id)
                ->orderBy('id', 'asc')
                ->first();
        }

        return view('frontend.consultation-room', compact(
            'appointment',
            'isDoctor',
            'isPatient',
            'isAdmin',
            'roomName',
            'displayName',
            'prescription',
            'isServing',
            'servingToken',
            'aheadCount',
            'nextOnlineAppt'
        ));
    }

    /**
     * AJAX endpoint polled by waiting room to detect when doctor calls patient
     */
    public function checkStatus($id)
    {
        $appointment = Appointment::findOrFail($id);

        $servingApptId = Cache::get("doctor_{$appointment->doctor_id}_online_serving_appt_id");
        $isServing = ($servingApptId == $appointment->id);

        $servingAppt = $servingApptId ? Appointment::find($servingApptId) : null;

        $aheadCount = Appointment::where('doctor_id', $appointment->doctor_id)
            ->whereDate('appointment_date', $appointment->appointment_date)
            ->where('type', 'online')
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('id', '<', $appointment->id)
            ->when($servingApptId, fn($q) => $q->where('id', '>', $servingApptId))
            ->count();

        return response()->json([
            'success' => true,
            'status' => $appointment->status,
            'is_serving' => $isServing,
            'serving_token' => $servingAppt?->token_number ?? null,
            'my_token' => $appointment->token_number,
            'ahead_count' => $aheadCount,
        ]);
    }

    /**
     * Doctor calls next online patient directly from consultation room
     */
    public function callNextOnline(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);
        $user = Auth::user();

        if (!$user->doctor || $appointment->doctor_id !== $user->doctor->id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        // Mark current completed if requested
        if ($request->input('complete_current', 1)) {
            $appointment->status = 'completed';
            $appointment->save();
        }

        $nextAppt = Appointment::where('doctor_id', $appointment->doctor_id)
            ->whereDate('appointment_date', $appointment->appointment_date)
            ->where('type', 'online')
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('id', '!=', $appointment->id)
            ->orderBy('id', 'asc')
            ->first();

        if ($nextAppt) {
            Cache::put("doctor_{$appointment->doctor_id}_online_serving_appt_id", $nextAppt->id, now()->endOfDay());
            if ($nextAppt->status === 'pending') {
                $nextAppt->status = 'confirmed';
                $nextAppt->save();
            }
            return redirect()->route('appointment.consultation', $nextAppt->id)
                ->with('success', '🔔 কল করা হয়েছে: ' . ($nextAppt->token_number ?? 'পরবর্তী রোগী'));
        }

        Cache::forget("doctor_{$appointment->doctor_id}_online_serving_appt_id");
        return redirect()->route('doctors.dashboard')->with('success', 'আজকের অনলাইন সিরিয়ালের সকল রোগী সম্পন্ন হয়েছে।');
    }

    /**
     * Mark appointment completed via AJAX from inside the consultation room
     */
    public function complete(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);
        $user = Auth::user();

        if (!$user->doctor || $appointment->doctor_id !== $user->doctor->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $appointment->status = 'completed';
        $appointment->save();

        // Clear active serving tokens if it was this appointment
        $onlineServing = Cache::get("doctor_{$appointment->doctor_id}_online_serving_appt_id");
        if ($onlineServing == $appointment->id) {
            Cache::forget("doctor_{$appointment->doctor_id}_online_serving_appt_id");
        }
        $offlineServing = Cache::get("doctor_{$appointment->doctor_id}_serving_appt_id");
        if ($offlineServing == $appointment->id) {
            Cache::forget("doctor_{$appointment->doctor_id}_serving_appt_id");
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Consultation marked as completed successfully!',
                'status' => 'completed',
            ]);
        }

        return redirect()->back()->with('success', 'Consultation marked as completed.');
    }
}

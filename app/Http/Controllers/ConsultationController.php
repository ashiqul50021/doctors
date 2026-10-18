<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Prescription;
use App\Services\VideoCallService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConsultationController extends Controller
{
    /**
     * In-App Live Video Consultation Room
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

        $videoService = new VideoCallService();
        $roomName = $appointment->meeting_link ? $videoService->getRoomName($appointment->meeting_link) : 'room-' . $appointment->id;

        $displayName = $isDoctor
            ? ('Dr. ' . ($appointment->doctor->user->name ?? 'Doctor'))
            : ($appointment->patient->user->name ?? $user->name ?? 'Patient');

        $prescription = Prescription::with('items')->where('appointment_id', $appointment->id)->first();

        return view('frontend.consultation-room', compact(
            'appointment',
            'isDoctor',
            'isPatient',
            'isAdmin',
            'roomName',
            'displayName',
            'prescription'
        ));
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

        // Clear active serving token if it was this appointment
        $servingApptId = \Illuminate\Support\Facades\Cache::get("doctor_{$appointment->doctor_id}_serving_appt_id");
        if ($servingApptId == $appointment->id) {
            \Illuminate\Support\Facades\Cache::forget("doctor_{$appointment->doctor_id}_serving_appt_id");
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

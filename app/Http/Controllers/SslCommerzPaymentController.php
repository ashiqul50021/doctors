<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Mail\AppointmentBookedMail;
use Illuminate\Support\Facades\Mail;

class SslCommerzPaymentController extends Controller
{
    public function success(Request $request)
    {
        $tran_id = $request->input('tran_id');

        if (!$tran_id) {
            return redirect()->route('home')->with('error', 'Invalid Transaction');
        }

        $appointment_id = str_replace('APT-', '', $tran_id);
        $appointment = Appointment::with(['patient.user', 'doctor.user'])->find($appointment_id);

        if (!$appointment) {
            return redirect()->route('home')->with('error', 'Appointment not found');
        }

        if ($appointment->status == 'pending') {
            $appointment->update(['status' => 'confirmed']);
            
            // Send Email Notification
            if ($appointment->patient && $appointment->patient->user) {
                Mail::to($appointment->patient->user->email)->send(new AppointmentBookedMail($appointment));
            }
            
            $doctor = Doctor::with('user')->find($appointment->doctor_id);

            $flashData = [
                'meeting_link' => $appointment->meeting_link,
                'token_number' => $appointment->token_number,
                'type' => $appointment->type,
                'doctor_name' => $doctor->user->name ?? 'Doctor',
                'date' => $appointment->appointment_date,
                'time' => $appointment->appointment_time,
            ];

            return redirect()->route('booking.success')->with($flashData)->with('success', 'Payment successful and appointment confirmed.');
        } else if ($appointment->status == 'confirmed') {
             $doctor = Doctor::with('user')->find($appointment->doctor_id);

            $flashData = [
                'meeting_link' => $appointment->meeting_link,
                'token_number' => $appointment->token_number,
                'type' => $appointment->type,
                'doctor_name' => $doctor->user->name ?? 'Doctor',
                'date' => $appointment->appointment_date,
                'time' => $appointment->appointment_time,
            ];

            return redirect()->route('booking.success')->with($flashData)->with('success', 'Payment already verified.');
        }

        return redirect()->route('home')->with('error', 'Invalid Appointment Status');
    }

    public function fail(Request $request)
    {
        $tran_id = $request->input('tran_id');
        
        if ($tran_id) {
            $appointment_id = str_replace('APT-', '', $tran_id);
            $appointment = Appointment::find($appointment_id);
            
            if($appointment) {
                $appointment->update(['status' => 'cancelled']);
            }
        }

        return redirect()->route('home')->with('error', 'Payment Failed');
    }

    public function cancel(Request $request)
    {
        $tran_id = $request->input('tran_id');
        
        if ($tran_id) {
            $appointment_id = str_replace('APT-', '', $tran_id);
            $appointment = Appointment::find($appointment_id);
            
            if($appointment) {
                $appointment->update(['status' => 'cancelled']);
            }
        }

        return redirect()->route('home')->with('error', 'Payment Cancelled');
    }

    public function ipn(Request $request)
    {
        $tran_id = $request->input('tran_id');
        $status = $request->input('status');

        if($tran_id) {
            $appointment_id = str_replace('APT-', '', $tran_id);
            $appointment = Appointment::with(['patient.user', 'doctor.user'])->find($appointment_id);

            if ($appointment && $status == 'VALID' && $appointment->status == 'pending') {
                $appointment->update(['status' => 'confirmed']);
                
                // Send Email Notification
                if ($appointment->patient && $appointment->patient->user) {
                    Mail::to($appointment->patient->user->email)->send(new AppointmentBookedMail($appointment));
                }
            }
        }
        return response()->json(['message' => 'IPN received']);
    }
}

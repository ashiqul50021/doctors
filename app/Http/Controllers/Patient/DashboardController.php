<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Appointment;
use App\Models\Prescription;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $patient = Auth::user()->patient;
        
        $appointments = Appointment::with('doctor.user')
            ->where('patient_id', $patient->id)
            ->orderBy('appointment_date', 'desc')
            ->get();
            
        $prescriptions = Prescription::with('doctor.user')
            ->where('patient_id', $patient->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('frontend.patient-dashboard', compact('appointments', 'prescriptions'));
    }
}

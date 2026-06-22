<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Doctor;

class DoctorController extends Controller
{
    public function show($id)
    {
        $doctor = Doctor::with(['user', 'speciality', 'reviews.patient.user', 'schedules'])
            ->where('id', $id)
            ->orWhere('slug', $id)
            ->firstOrFail();

        return view('frontend.doctor-profile', compact('doctor'));
    }
}

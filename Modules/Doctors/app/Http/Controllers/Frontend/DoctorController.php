<?php

namespace Modules\Doctors\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Doctors\Models\Doctor;

class DoctorController extends Controller
{
    public function show($id)
    {
        $doctor = Doctor::with(['user', 'speciality', 'reviews.patient.user', 'schedules'])
            ->where('id', $id)
            ->orWhere('slug', $id)
            ->firstOrFail();

        return view('doctors::frontend.doctor-profile', compact('doctor'));
    }
}

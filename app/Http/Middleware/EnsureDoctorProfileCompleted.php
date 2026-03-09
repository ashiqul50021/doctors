<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureDoctorProfileCompleted
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'doctor') {
            return $next($request);
        }

        $doctor = $user->doctor;
        if (!$doctor) {
            return redirect()->route('home')->with('error', 'Doctor profile not found.');
        }

        if (!$doctor->isProfileComplete()) {
            return redirect()->route('doctors.profile.settings')
                ->with('warning', 'Please complete your profile to activate your doctor account.');
        }

        return $next($request);
    }
}

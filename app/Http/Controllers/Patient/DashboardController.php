<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Prescription;
use App\Models\Patient;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    public function index()
    {
        $patient = Auth::user()->patient;
        if (!$patient) {
            $patient = Patient::create([
                'user_id' => Auth::id(),
                'address' => '',
            ]);
        }
        
        $appointments = Appointment::with(['doctor.user', 'doctor.speciality'])
            ->where('patient_id', $patient->id)
            ->orderBy('appointment_date', 'desc')
            ->get();
            
        $prescriptions = Prescription::with('doctor.user')
            ->where('patient_id', $patient->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('frontend.patient-dashboard', compact('appointments', 'prescriptions'));
    }

    public function profileSettings()
    {
        $user = Auth::user();
        $patient = $user->patient;
        if (!$patient) {
            $patient = Patient::create([
                'user_id' => $user->id,
                'address' => '',
            ]);
        }

        return view('frontend.profile-settings', compact('user', 'patient'));
    }

    public function updateProfileSettings(Request $request)
    {
        $user = Auth::user();
        $patient = $user->patient;
        if (!$patient) {
            $patient = Patient::create([
                'user_id' => $user->id,
                'address' => '',
            ]);
        }

        $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date'],
            'blood_group' => ['nullable', Rule::in(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'])],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'zip_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:100'],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:2048'],
        ]);

        $fullName = trim($request->first_name . ' ' . ($request->last_name ?? ''));
        $user->update([
            'name' => $fullName !== '' ? $fullName : $user->name,
            'email' => $request->email,
        ]);

        $profileImagePath = $patient->profile_image;
        if ($request->hasFile('profile_image')) {
            if ($patient->profile_image) {
                ImageService::delete($patient->profile_image);
            }
            $profileImagePath = ImageService::upload($request->file('profile_image'), 'patients');
        }

        $patient->update([
            'phone' => $request->phone,
            'date_of_birth' => $request->date_of_birth,
            'blood_group' => $request->blood_group,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'zip_code' => $request->zip_code,
            'country' => $request->country,
            'profile_image' => $profileImagePath,
        ]);

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    public function changePassword()
    {
        return view('frontend.change-password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->back()->with('success', 'Password updated successfully!');
    }

    public function favourites()
    {
        $patient = Auth::user()->patient;
        if (!$patient) {
            $patient = Patient::create([
                'user_id' => Auth::id(),
                'address' => '',
            ]);
        }
        
        $doctors = $patient->favouriteDoctors()->with(['user', 'speciality', 'district'])->get();
        return view('frontend.favourites', compact('doctors'));
    }

    public function toggleFavourite($doctorId)
    {
        $doctor = \App\Models\Doctor::find($doctorId);
        if (!$doctor) {
            return response()->json([
                'status' => 'error',
                'message' => 'Doctor not found.'
            ], 404);
        }

        $patient = Auth::user()->patient;
        if (!$patient) {
            $patient = Patient::create([
                'user_id' => Auth::id(),
                'address' => '',
            ]);
        }

        $result = $patient->favouriteDoctors()->toggle($doctorId);
        $status = count($result['attached']) > 0 ? 'added' : 'removed';

        return response()->json([
            'status' => 'success',
            'action' => $status,
            'message' => $status === 'added' 
                ? 'Doctor added to favourites successfully.' 
                : 'Doctor removed from favourites successfully.'
        ]);
    }

    public function viewInvoice($id)
    {
        $appointment = Appointment::with(['doctor.user', 'patient.user', 'doctor.speciality'])->findOrFail($id);

        $user = Auth::user();
        if ($user->role === 'patient') {
            if (!$appointment->patient || $appointment->patient->user_id !== $user->id) {
                abort(403, 'Unauthorized action.');
            }
        } elseif ($user->role === 'doctor') {
            if (!$appointment->doctor || $appointment->doctor->user_id !== $user->id) {
                abort(403, 'Unauthorized action.');
            }
        } else {
            abort(403, 'Unauthorized action.');
        }

        $siteSettings = \App\Models\SiteSetting::pluck('value', 'key')->toArray();

        return view('frontend.invoice-view', compact('appointment', 'siteSettings'));
    }
}

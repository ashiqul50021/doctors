<?php

namespace Modules\Doctors\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use App\Services\ImageService;
use Modules\Doctors\Models\Doctor;
use Modules\Doctors\Models\Speciality;
use App\Models\User;

class DoctorController extends Controller
{
    public function index()
    {
        $doctors = Doctor::with(['user', 'speciality'])->latest()->get();
        return view('doctors::backend.doctors.index', compact('doctors'));
    }

    public function create()
    {
        $specialities = Speciality::where('is_active', true)->get();
        return view('doctors::backend.doctors.create', compact('specialities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'speciality_id' => 'required|exists:specialities,id',
            'qualification' => 'required|string',
            'consultation_fee' => 'nullable|numeric',
            'experience_years' => 'nullable|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = ImageService::upload($request->file('image'), 'doctors');
        }

        Doctor::create([
            'user_id' => $user->id,
            'speciality_id' => $request->speciality_id,
            'qualification' => $request->qualification,
            'bio' => $request->bio,
            'clinic_name' => $request->clinic_name,
            'clinic_address' => $request->clinic_address,
            'consultation_fee' => $request->consultation_fee ?? 0,
            'experience_years' => $request->experience_years ?? 0,
            'profile_image' => $imagePath,
            'status' => 'approved',
            'is_featured' => $request->has('is_featured'),
        ]);

        return redirect()->route('doctors.admin.doctors.index')->with('success', 'Doctor created successfully.');
    }

    public function show(Doctor $doctor)
    {
        return view('doctors::backend.doctors.show', compact('doctor'));
    }

    public function edit(Doctor $doctor)
    {
        $specialities = Speciality::where('is_active', true)->get();
        return view('doctors::backend.doctors.edit', compact('doctor', 'specialities'));
    }

    public function update(Request $request, Doctor $doctor)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'speciality_id' => 'required|exists:specialities,id',
            'qualification' => 'required|string',
            'consultation_fee' => 'nullable|numeric',
            'experience_years' => 'nullable|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $doctor->user->update([
            'name' => $request->name,
        ]);

        $data = [
            'speciality_id' => $request->speciality_id,
            'qualification' => $request->qualification,
            'bio' => $request->bio,
            'clinic_name' => $request->clinic_name,
            'clinic_address' => $request->clinic_address,
            'consultation_fee' => $request->consultation_fee ?? 0,
            'experience_years' => $request->experience_years ?? 0,
            'status' => $request->status ?? $doctor->status,
            'is_featured' => $request->has('is_featured'),
        ];

        if ($request->hasFile('image')) {
            ImageService::delete($doctor->profile_image);
            $data['profile_image'] = ImageService::upload($request->file('image'), 'doctors');
        }

        $doctor->update($data);

        return redirect()->route('doctors.admin.doctors.index')->with('success', 'Doctor updated successfully.');
    }

    public function destroy(Doctor $doctor)
    {
        ImageService::delete($doctor->profile_image);
        $doctor->user->delete();

        return redirect()->route('doctors.admin.doctors.index')->with('success', 'Doctor deleted successfully.');
    }
}

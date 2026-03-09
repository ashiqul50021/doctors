<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\User;
use App\Models\Speciality;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Services\ImageService;
use Illuminate\Support\Str;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $doctors = Doctor::with(['user', 'speciality'])->latest()->get();
        return view('admin.doctors.index', compact('doctors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $specialities = Speciality::where('is_active', true)->get();
        return view('admin.doctors.create', compact('specialities'));
    }

    /**
     * Store a newly created resource in storage.
     */
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
            // Assuming we might have a role column later or just use doctor table existence
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
            'status' => 'approved', // Admin created doctors are approved by default
            'is_featured' => $request->has('is_featured'),
        ]);

        return redirect()->route('admin.doctors.index')->with('success', 'Doctor created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Doctor $doctor)
    {
        return view('admin.doctors.show', compact('doctor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Doctor $doctor)
    {
        $specialities = Speciality::where('is_active', true)->get();
        return view('admin.doctors.edit', compact('doctor', 'specialities'));
    }

    /**
     * Update the specified resource in storage.
     */
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
            if ($doctor->profile_image) {
                ImageService::delete($doctor->profile_image);
            }
            $data['profile_image'] = ImageService::upload($request->file('image'), 'doctors');
        }

        $doctor->update($data);

        return redirect()->route('admin.doctors.index')->with('success', 'Doctor updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Doctor $doctor)
    {
        if ($doctor->profile_image) {
            ImageService::delete($doctor->profile_image);
        }
        $doctor->user->delete(); // This cascades to doctor
        // or $doctor->delete(); if user should remain. But usually user is deleted.
        // Migration has onDelete cascade on user_id, but here I'm deleting user.

        return redirect()->route('admin.doctors.index')->with('success', 'Doctor deleted successfully.');
    }
}

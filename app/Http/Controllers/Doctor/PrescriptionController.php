<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Appointment;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use Illuminate\Support\Facades\Auth;

class PrescriptionController extends Controller
{
    public function create($appointment_id)
    {
        $doctor = Auth::user()->doctor;
        $appointment = Appointment::with('patient.user')->where('doctor_id', $doctor->id)->findOrFail($appointment_id);

        $existingPrescription = Prescription::where('appointment_id', $appointment_id)->first();
        if ($existingPrescription) {
            return redirect()->route('doctors.edit.prescription', $existingPrescription->id);
        }

        return view('frontend.add-prescription', compact('appointment', 'doctor'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'symptoms' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'notes' => 'nullable|string',
            'medicine_name.*' => 'nullable|string',
            'quantity.*' => 'nullable|string',
            'days.*' => 'nullable|string',
        ]);

        $doctor = Auth::user()->doctor;
        $appointment = Appointment::where('doctor_id', $doctor->id)->findOrFail($request->appointment_id);

        // Double check
        $existingPrescription = Prescription::where('appointment_id', $appointment->id)->first();
        if ($existingPrescription) {
            return redirect()->route('doctors.edit.prescription', $existingPrescription->id);
        }

        $prescription = Prescription::create([
            'doctor_id' => $doctor->id,
            'patient_id' => $appointment->patient_id,
            'appointment_id' => $appointment->id,
            'symptoms' => $request->symptoms,
            'diagnosis' => $request->diagnosis,
            'notes' => $request->notes,
        ]);

        if ($request->has('medicine_name')) {
            foreach ($request->medicine_name as $index => $medicine) {
                if (!empty($medicine)) {
                    PrescriptionItem::create([
                        'prescription_id' => $prescription->id,
                        'medicine_name' => $medicine,
                        'quantity' => $request->quantity[$index] ?? null,
                        'days' => $request->days[$index] ?? null,
                        'morning' => isset($request->morning[$index]) ? true : false,
                        'afternoon' => isset($request->afternoon[$index]) ? true : false,
                        'evening' => isset($request->evening[$index]) ? true : false,
                        'night' => isset($request->night[$index]) ? true : false,
                    ]);
                }
            }
        }

        // Complete the appointment when a prescription is given
        $appointment->update(['status' => 'completed']);

        return redirect()->route('doctors.dashboard')->with('success', 'Prescription added successfully!');
    }

    public function show($id)
    {
        $user = Auth::user();
        $query = Prescription::with(['items', 'patient.user', 'doctor.user']);

        if ($user->role === 'doctor') {
            $query->where('doctor_id', $user->doctor->id);
        } elseif ($user->role === 'patient') {
            $query->where('patient_id', $user->patient->id);
        }

        $prescription = $query->findOrFail($id);

        return view('frontend.prescription-view', compact('prescription'));
    }

    public function edit($id)
    {
        $doctor = Auth::user()->doctor;
        $prescription = Prescription::with(['items', 'patient.user', 'appointment'])->where('doctor_id', $doctor->id)->findOrFail($id);
        
        return view('frontend.edit-prescription', compact('prescription', 'doctor'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'symptoms' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'notes' => 'nullable|string',
            'medicine_name.*' => 'nullable|string',
            'quantity.*' => 'nullable|string',
            'days.*' => 'nullable|string',
        ]);

        $doctor = Auth::user()->doctor;
        $prescription = Prescription::where('doctor_id', $doctor->id)->findOrFail($id);

        $prescription->update([
            'symptoms' => $request->symptoms,
            'diagnosis' => $request->diagnosis,
            'notes' => $request->notes,
        ]);

        // Delete old items and add new ones (easiest way to handle dynamic arrays)
        $prescription->items()->delete();

        if ($request->has('medicine_name')) {
            foreach ($request->medicine_name as $index => $medicine) {
                if (!empty($medicine)) {
                    PrescriptionItem::create([
                        'prescription_id' => $prescription->id,
                        'medicine_name' => $medicine,
                        'quantity' => $request->quantity[$index] ?? null,
                        'days' => $request->days[$index] ?? null,
                        'morning' => isset($request->morning[$index]) ? true : false,
                        'afternoon' => isset($request->afternoon[$index]) ? true : false,
                        'evening' => isset($request->evening[$index]) ? true : false,
                        'night' => isset($request->night[$index]) ? true : false,
                    ]);
                }
            }
        }

        return redirect()->route('doctors.dashboard')->with('success', 'Prescription updated successfully!');
    }
}

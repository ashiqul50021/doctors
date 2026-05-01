<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Review;
use App\Models\Schedule;
use App\Models\DoctorOffDay;
use App\Models\Area;
use App\Services\ImageService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    /**
     * Get the authenticated doctor or redirect.
     */
    private function getDoctor()
    {
        $doctor = Auth::user()->doctor;
        if (!$doctor) {
            abort(redirect()->route('home')->with('error', 'Doctor profile not found.'));
        }
        if ($doctor->isProfileComplete() && $doctor->status !== 'approved') {
            $doctor->update(['status' => 'approved']);
        }
        $doctor->load(['user', 'speciality']);
        return $doctor;
    }

    /**
     * Dashboard
     */
    public function index()
    {
        $doctor = $this->getDoctor();

        $totalPatients = Appointment::where('doctor_id', $doctor->id)
            ->distinct('patient_id')->count('patient_id');

        $todayPatients = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', today())
            ->distinct('patient_id')->count('patient_id');

        $totalAppointments = Appointment::where('doctor_id', $doctor->id)->count();

        $upcomingAppointments = Appointment::with(['patient.user'])
            ->where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', '>=', today())
            ->orderBy('appointment_date', 'asc')
            ->orderBy('appointment_time', 'asc')
            ->get();

        $todayAppointments = Appointment::with(['patient.user'])
            ->where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', today())
            ->orderBy('appointment_time', 'asc')
            ->get();

        return view('frontend.doctor-dashboard', compact(
            'doctor', 'totalPatients', 'todayPatients',
            'totalAppointments', 'upcomingAppointments', 'todayAppointments'
        ));
    }

    /**
     * Appointments page
     */
    public function appointments()
    {
        $doctor = $this->getDoctor();

        $appointments = Appointment::with(['patient.user'])
            ->where('doctor_id', $doctor->id)
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->paginate(10);

        return view('frontend.appointments', compact('doctor', 'appointments'));
    }

    /**
     * My Patients page
     */
    public function myPatients()
    {
        $doctor = $this->getDoctor();

        $patientIds = Appointment::where('doctor_id', $doctor->id)
            ->distinct()->pluck('patient_id');

        $patients = Patient::with('user')
            ->whereIn('id', $patientIds)
            ->paginate(10);

        return view('frontend.my-patients', compact('doctor', 'patients'));
    }

    /**
     * Reviews page
     */
    public function reviews()
    {
        $doctor = $this->getDoctor();

        $reviews = Review::with(['patient.user', 'appointment'])
            ->where('doctor_id', $doctor->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('frontend.reviews', compact('doctor', 'reviews'));
    }

    /**
     * Invoices page
     */
    public function invoices()
    {
        $doctor = $this->getDoctor();

        $invoices = Appointment::with(['patient.user'])
            ->where('doctor_id', $doctor->id)
            ->whereIn('status', ['completed', 'confirmed'])
            ->orderBy('appointment_date', 'desc')
            ->paginate(10);

        return view('frontend.invoices', compact('doctor', 'invoices'));
    }

    /**
     * Social Media page
     */
    public function socialMedia()
    {
        $doctor = $this->getDoctor();
        return view('frontend.social-media', compact('doctor'));
    }

    /**
     * Update Social Media links
     */
    public function updateSocialMedia(Request $request)
    {
        $doctor = $this->getDoctor();

        $doctor->update([
            'website' => $request->website,
            'facebook' => $request->facebook,
            'linkedin' => $request->linkedin,
        ]);

        return redirect()->back()->with('success', 'Social media links updated successfully!');
    }

    /**
     * Change Password page
     */
    public function changePassword()
    {
        $doctor = $this->getDoctor();
        return view('frontend.doctor-change-password', compact('doctor'));
    }

    /**
     * Update Password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        return redirect()->back()->with('success', 'Password changed successfully!');
    }

    /**
     * Profile Settings page
     */
    public function profileSettings()
    {
        $doctor = $this->getDoctor();
        $specialities = \App\Models\Speciality::orderBy('name')->get();
        $districts = \App\Models\District::orderBy('name')->get();
        $areas = collect();

        if (!empty($doctor->district_id)) {
            $areas = Area::where('district_id', $doctor->district_id)->orderBy('name')->get();
        }

        return view('frontend.doctor-profile-settings', compact('doctor', 'specialities', 'districts', 'areas'));
    }

    /**
     * Update profile settings page
     */
    public function updateProfileSettings(Request $request)
    {
        $doctor = $this->getDoctor();
        $user = Auth::user();

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'date_of_birth' => ['nullable', 'date'],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:2048'],
            'qualification' => ['nullable', 'string', 'max:255'],
            'speciality_id' => ['nullable', 'exists:specialities,id'],
            'registration_number' => ['nullable', 'string', 'max:255'],
            'registration_date' => ['nullable', 'date'],
            'experience_years' => ['nullable', 'integer', 'min:0', 'max:80'],
            'bio' => ['nullable', 'string'],
            'clinic_names' => ['nullable', 'array', 'max:10'],
            'clinic_names.*' => ['nullable', 'string', 'max:120'],
            'clinic_addresses' => ['nullable', 'array', 'max:10'],
            'clinic_addresses.*' => ['nullable', 'string', 'max:180'],
            'district_id' => ['nullable', 'exists:districts,id'],
            'area_id' => [
                'nullable',
                Rule::exists('areas', 'id')->where(function ($query) use ($request) {
                    if ($request->filled('district_id')) {
                        $query->where('district_id', $request->district_id);
                    }
                }),
            ],
            'consultation_fee' => ['nullable', 'numeric', 'min:0'],
            'online_consultation' => ['nullable', 'boolean'],
            'online_fee' => ['nullable', 'numeric', 'min:0'],
            'home_visit' => ['nullable', 'boolean'],
            'home_visit_fee' => ['nullable', 'numeric', 'min:0'],
            'website' => ['nullable', 'url', 'max:255'],
            'facebook' => ['nullable', 'url', 'max:255'],
            'linkedin' => ['nullable', 'url', 'max:255'],
            'languages' => ['nullable', 'string'],
            'services' => ['nullable', 'string'],
            'education' => ['nullable', 'string'],
            'awards' => ['nullable', 'string'],
        ]);

        $fullName = trim(($validated['first_name'] ?? '') . ' ' . ($validated['last_name'] ?? ''));
        $user->update([
            'name' => $fullName !== '' ? $fullName : $user->name,
        ]);

        if ($request->hasFile('profile_image')) {
            if ($doctor->profile_image) {
                ImageService::delete($doctor->profile_image);
            }
            $validated['profile_image'] = ImageService::upload($request->file('profile_image'), 'doctors');
        }

        [$clinicNames, $clinicAddresses] = $this->toJsonClinicPairs(
            $validated['clinic_names'] ?? [],
            $validated['clinic_addresses'] ?? []
        );

        $doctor->update([
            'phone' => $validated['phone'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'profile_image' => $validated['profile_image'] ?? $doctor->profile_image,
            'qualification' => $validated['qualification'] ?? null,
            'speciality_id' => $validated['speciality_id'] ?? null,
            'registration_number' => $validated['registration_number'] ?? null,
            'registration_date' => $validated['registration_date'] ?? null,
            'experience_years' => $validated['experience_years'] ?? 0,
            'bio' => $validated['bio'] ?? null,
            'clinic_name' => $clinicNames,
            'clinic_address' => $clinicAddresses,
            'district_id' => $validated['district_id'] ?? null,
            'area_id' => $validated['area_id'] ?? null,
            'consultation_fee' => $validated['consultation_fee'] ?? 0,
            'online_consultation' => (bool) $request->boolean('online_consultation'),
            'online_fee' => $validated['online_fee'] ?? null,
            'home_visit' => (bool) $request->boolean('home_visit'),
            'home_visit_fee' => $validated['home_visit_fee'] ?? null,
            'website' => $validated['website'] ?? null,
            'facebook' => $validated['facebook'] ?? null,
            'linkedin' => $validated['linkedin'] ?? null,
            'languages' => $this->toJsonList($validated['languages'] ?? null),
            'services' => $this->toJsonList($validated['services'] ?? null),
            'education' => $this->toJsonLines($validated['education'] ?? null),
            'awards' => $this->toJsonLines($validated['awards'] ?? null),
        ]);

        $doctor->refresh();

        if ($doctor->isProfileComplete()) {
            if ($doctor->status !== 'approved') {
                $doctor->update(['status' => 'approved']);
            }

            return redirect()->route('doctors.dashboard')
                ->with('success', 'Profile updated successfully. Your doctor account is now active.');
        }

        return redirect()->route('doctors.profile.settings')
            ->with('warning', 'Profile saved, but some required fields are still missing.');
    }

    private function toJsonClinicPairs(array $names, array $addresses): array
    {
        $names = array_values(array_map(function ($name) {
            return is_string($name) ? trim($name) : '';
        }, $names));
        $addresses = array_values(array_map(function ($address) {
            return is_string($address) ? trim($address) : '';
        }, $addresses));

        $count = max(count($names), count($addresses));
        $cleanNames = [];
        $cleanAddresses = [];

        for ($i = 0; $i < $count; $i++) {
            $name = $names[$i] ?? '';
            $address = $addresses[$i] ?? '';

            if ($name === '' || $address === '') {
                continue;
            }

            $cleanNames[] = $name;
            $cleanAddresses[] = $address;
        }

        return [
            empty($cleanNames) ? null : json_encode($cleanNames),
            empty($cleanAddresses) ? null : json_encode($cleanAddresses),
        ];
    }

    private function toJsonList(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $items = array_values(array_filter(array_map('trim', explode(',', $value))));
        return empty($items) ? null : json_encode($items);
    }

    private function toJsonLines(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $lines = preg_split('/\r\n|\r|\n/', trim($value));
        $items = array_values(array_filter(array_map('trim', $lines)));
        return empty($items) ? null : json_encode($items);
    }

    /**
     * Accept Appointment
     */
    public function acceptAppointment($id)
    {
        $appointment = Appointment::findOrFail($id);

        if ($appointment->doctor_id !== Auth::user()->doctor->id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $appointment->status = 'confirmed';
        $appointment->save();

        return redirect()->back()->with('success', 'Appointment confirmed successfully.');
    }

    /**
     * Cancel Appointment
     */
    public function cancelAppointment($id)
    {
        $appointment = Appointment::findOrFail($id);

        if ($appointment->doctor_id !== Auth::user()->doctor->id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $appointment->status = 'cancelled';
        $appointment->save();

        return redirect()->back()->with('success', 'Appointment cancelled successfully.');
    }

    /**
     * Complete Appointment
     */
    public function completeAppointment($id)
    {
        $appointment = Appointment::findOrFail($id);

        if ($appointment->doctor_id !== Auth::user()->doctor->id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        if ($appointment->status !== 'confirmed') {
            return redirect()->back()->with('error', 'Only confirmed appointments can be marked as completed.');
        }

        $appointment->status = 'completed';
        $appointment->save();

        return redirect()->back()->with('success', 'Appointment marked as completed.');
    }

    /**
     * Off Days management — add a specific date as off day
     */
    public function addOffDay(Request $request)
    {
        $request->validate([
            'off_date' => 'required|date|after_or_equal:today',
            'reason' => 'nullable|string|max:255',
        ]);

        $doctor = $this->getDoctor();

        // Check if already marked as off
        $exists = DoctorOffDay::where('doctor_id', $doctor->id)
            ->where('off_date', $request->off_date)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('warning', 'This date is already marked as off day.');
        }

        DoctorOffDay::create([
            'doctor_id' => $doctor->id,
            'off_date' => $request->off_date,
            'reason' => $request->reason,
        ]);

        return redirect()->back()->with('success', 'Off day added successfully for ' . \Carbon\Carbon::parse($request->off_date)->format('d M Y') . '.');
    }

    /**
     * Remove a specific off day
     */
    public function removeOffDay($id)
    {
        $doctor = $this->getDoctor();

        $offDay = DoctorOffDay::where('id', $id)
            ->where('doctor_id', $doctor->id)
            ->firstOrFail();

        $offDay->delete();

        return redirect()->back()->with('success', 'Off day removed successfully.');
    }
}

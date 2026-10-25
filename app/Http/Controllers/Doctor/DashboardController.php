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
    private function getDoctor(bool $requireApproved = true)
    {
        $doctor = Auth::user()->doctor;
        if (!$doctor) {
            abort(redirect()->route('home')->with('error', 'Doctor profile not found.'));
        }
        if ($requireApproved && $doctor->status !== 'approved') {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            abort(redirect()->route('login')->with('warning', 'Your account is pending admin approval.'));
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

        // Live Chamber Queue State
        $servingApptId = \Illuminate\Support\Facades\Cache::get("doctor_{$doctor->id}_serving_appt_id");
        $currentlyServing = $servingApptId ? $todayAppointments->firstWhere('id', $servingApptId) : null;
        
        // If the serving appointment was cancelled or completed, reset
        if ($currentlyServing && in_array($currentlyServing->status, ['cancelled', 'completed'])) {
            $currentlyServing = null;
        }

        // Determine next in line (offline chamber patient waiting)
        $nextInLine = $todayAppointments
            ->where('type', 'offline')
            ->whereIn('status', ['pending', 'confirmed'])
            ->when($currentlyServing, fn($coll) => $coll->where('id', '!=', $currentlyServing->id))
            ->first();

        // Live Online Telemedicine Queue State
        $onlineServingApptId = \Illuminate\Support\Facades\Cache::get("doctor_{$doctor->id}_online_serving_appt_id");
        $onlineCurrentlyServing = $onlineServingApptId ? $todayAppointments->firstWhere('id', $onlineServingApptId) : null;
        if ($onlineCurrentlyServing && in_array($onlineCurrentlyServing->status, ['cancelled', 'completed'])) {
            $onlineCurrentlyServing = null;
        }

        $onlineNextInLine = $todayAppointments
            ->where('type', 'online')
            ->whereIn('status', ['pending', 'confirmed'])
            ->when($onlineCurrentlyServing, fn($coll) => $coll->where('id', '!=', $onlineCurrentlyServing->id))
            ->first();

        return view('frontend.doctor-dashboard', compact(
            'doctor', 'totalPatients', 'todayPatients',
            'totalAppointments', 'upcomingAppointments', 'todayAppointments',
            'currentlyServing', 'nextInLine', 'onlineCurrentlyServing', 'onlineNextInLine'
        ));
    }

    /**
     * Appointments page — online & offline separated, with date filter
     */
    public function appointments(Request $request)
    {
        $doctor = $this->getDoctor();

        $filterDate = $request->get('filter_date');

        $baseQuery = Appointment::with(['patient.user'])
            ->where('doctor_id', $doctor->id);

        if ($filterDate) {
            $baseQuery->whereDate('appointment_date', $filterDate);
        }

        $onlineAppointments = (clone $baseQuery)
            ->where('type', 'online')
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'asc')
            ->paginate(10, ['*'], 'online_page');

        $offlineAppointments = (clone $baseQuery)
            ->where('type', 'offline')
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'asc')
            ->paginate(10, ['*'], 'offline_page');

        return view('frontend.appointments', compact(
            'doctor', 'onlineAppointments', 'offlineAppointments', 'filterDate'
        ));
    }

    /**
     * Print appointments by date with serial numbers
     */
    public function printAppointments(Request $request)
    {
        $request->validate([
            'print_date' => 'required|date',
            'print_type' => 'nullable|in:online,offline,all',
        ]);

        $doctor = $this->getDoctor();
        $printDate = $request->get('print_date');
        $printType = $request->get('print_type', 'all');

        $query = Appointment::with(['patient.user', 'patient'])
            ->where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', $printDate);

        if ($printType !== 'all') {
            $query->where('type', $printType);
        }

        $appointments = $query->orderBy('appointment_time', 'asc')->get();

        return view('frontend.print-appointments', compact(
            'doctor', 'appointments', 'printDate', 'printType'
        ));
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
        $doctor = $this->getDoctor(false);
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
        $doctor = $this->getDoctor(false);
        $user = Auth::user();

        // Normalize phone number to standard format (e.g. 017XXXXXXXX)
        if ($request->filled('phone')) {
            $cleanPhone = preg_replace('/[^0-9]/', '', (string)$request->phone);
            if (str_starts_with($cleanPhone, '880')) {
                $cleanPhone = substr($cleanPhone, 3);
            }
            if (!str_starts_with($cleanPhone, '0') && strlen($cleanPhone) === 10) {
                $cleanPhone = '0' . $cleanPhone;
            }
            $request->merge(['phone' => $cleanPhone]);
        }

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'date_of_birth' => ['nullable', 'date'],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:10240'],
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
            'rating_option' => ['nullable', 'string', 'in:price_free,custom_price'],
            'custom_rating_count' => ['nullable', 'numeric', 'min:0'],
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
            'custom_sections' => ['nullable', 'array'],
            'custom_sections.*.type' => ['nullable', 'string', 'in:standard,image_only'],
            'custom_sections.*.title' => ['nullable', 'string', 'max:255'],
            'custom_sections.*.description' => ['nullable', 'string'],
            'custom_sections.*.existing_image' => ['nullable', 'string'],
            'custom_sections.*.image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:10240'],
        ]);

        $fullName = trim(($validated['first_name'] ?? '') . ' ' . ($validated['last_name'] ?? ''));
        $user->update([
            'name' => $fullName !== '' ? $fullName : $user->name,
        ]);

        if ($request->hasFile('profile_image')) {
            if ($doctor->profile_image) {
                ImageService::delete($doctor->profile_image);
            }
            $validated['profile_image'] = ImageService::upload($request->file('profile_image'), 'doctors', 80, 400);
        }

        [$clinicNames, $clinicAddresses] = $this->toJsonClinicPairs(
            $validated['clinic_names'] ?? [],
            $validated['clinic_addresses'] ?? []
        );

        $updateData = [
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
            'pricing' => ($request->rating_option === 'price_free') ? 'free' : 'custom',
            'custom_price' => ($request->rating_option === 'custom_price') ? $request->custom_rating_count : null,
            'consultation_fee' => ($request->rating_option === 'price_free') ? 0 : ($request->custom_rating_count ?? $validated['consultation_fee'] ?? 0),
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
        ];

        // Process Custom Sections if submitted from profile settings
        if ($request->has('custom_sections') || $request->has('custom_sections_submitted')) {
            $customSections = [];
            $rawSections = $request->input('custom_sections', []);
            if (is_array($rawSections)) {
                foreach ($rawSections as $index => $sectionData) {
                    $secType = in_array($sectionData['type'] ?? '', ['standard', 'image_only']) 
                        ? $sectionData['type'] 
                        : 'standard';
                    $secTitle = isset($sectionData['title']) ? trim((string) $sectionData['title']) : '';
                    $secDesc = isset($sectionData['description']) ? trim((string) $sectionData['description']) : '';
                    $secImage = $sectionData['existing_image'] ?? null;

                    // Handle newly uploaded section image
                    if ($request->hasFile("custom_sections.{$index}.image")) {
                        $uploadedFile = $request->file("custom_sections.{$index}.image");
                        if ($uploadedFile && $uploadedFile->isValid()) {
                            if ($secImage && str_starts_with($secImage, 'uploads/doctors/sections/')) {
                                ImageService::delete($secImage);
                            }
                            $secImage = ImageService::upload($uploadedFile, 'doctors/sections', 80, 1200);
                        }
                    }

                    // Keep section based on type
                    if ($secType === 'image_only') {
                        if (!empty($secImage) || $secTitle !== '') {
                            $customSections[] = [
                                'type' => 'image_only',
                                'title' => $secTitle,
                                'description' => null,
                                'image' => $secImage,
                            ];
                        }
                    } else {
                        if ($secTitle !== '' || $secDesc !== '' || !empty($secImage)) {
                            $customSections[] = [
                                'type' => 'standard',
                                'title' => $secTitle,
                                'description' => $secDesc,
                                'image' => $secImage,
                            ];
                        }
                    }
                }
            }
            $updateData['custom_sections'] = $customSections;
        }

        $doctor->update($updateData);

        $doctor->refresh();

        if ($doctor->isProfileComplete()) {
            if ($doctor->status === 'approved') {
                return redirect()->route('doctors.dashboard')
                    ->with('success', 'Profile updated successfully. Your doctor account is now active.');
            }

            return redirect()->route('doctors.profile.settings')
                ->with('info', 'Profile updated successfully! Your profile is complete and pending admin approval.');
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
        if ($appointment->payment_status === 'pending') {
            $appointment->payment_status = 'paid';
        }
        $appointment->save();

        // Send Email Notification if email is valid
        if ($appointment->patient && $appointment->patient->user && filter_var($appointment->patient->user->email, FILTER_VALIDATE_EMAIL)) {
            try {
                \Illuminate\Support\Facades\Mail::to($appointment->patient->user->email)->send(new \App\Mail\AppointmentBookedMail($appointment));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Appointment accept email could not be sent: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Appointment confirmed and payment verified successfully.');
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

        // Clear active serving token if it was this appointment
        $servingApptId = \Illuminate\Support\Facades\Cache::get("doctor_{$appointment->doctor_id}_serving_appt_id");
        if ($servingApptId == $appointment->id) {
            \Illuminate\Support\Facades\Cache::forget("doctor_{$appointment->doctor_id}_serving_appt_id");
        }

        return redirect()->back()->with('success', 'Appointment marked as completed.');
    }

    /**
     * Live Chamber Queue: Call Next Patient
     */
    public function callNextPatient(Request $request)
    {
        $doctor = $this->getDoctor();

        $type = request('type', 'offline');

        if ($type === 'online') {
            // Find today's online appointments
            $todayAppointments = Appointment::where('doctor_id', $doctor->id)
                ->whereDate('appointment_date', today())
                ->where('type', 'online')
                ->orderBy('id', 'asc')
                ->get();

            $servingApptId = \Illuminate\Support\Facades\Cache::get("doctor_{$doctor->id}_online_serving_appt_id");

            // 1. If someone is currently serving, mark them completed
            if ($servingApptId) {
                $currentAppt = $todayAppointments->firstWhere('id', $servingApptId);
                if ($currentAppt && in_array($currentAppt->status, ['pending', 'confirmed'])) {
                    $currentAppt->status = 'completed';
                    $currentAppt->save();
                }
            }

            // 2. Find the next pending/confirmed patient
            $nextAppt = $todayAppointments
                ->whereIn('status', ['pending', 'confirmed'])
                ->when($servingApptId, fn($coll) => $coll->where('id', '!=', $servingApptId))
                ->first();

            if ($nextAppt) {
                if ($nextAppt->status === 'pending') {
                    $nextAppt->status = 'confirmed';
                    $nextAppt->save();
                }

                \Illuminate\Support\Facades\Cache::put("doctor_{$doctor->id}_online_serving_appt_id", $nextAppt->id, now()->endOfDay());

                return redirect()->route('appointment.consultation', $nextAppt->id);
            }

            // No more patients waiting
            \Illuminate\Support\Facades\Cache::forget("doctor_{$doctor->id}_online_serving_appt_id");

            return redirect()->back()->with('info', 'আজকের অনলাইন সিরিয়ালের সকল রোগী সম্পন্ন হয়েছে।');
        }

        // Find today's offline appointments
        $todayAppointments = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', today())
            ->where('type', 'offline')
            ->orderBy('appointment_time', 'asc')
            ->get();

        $servingApptId = \Illuminate\Support\Facades\Cache::get("doctor_{$doctor->id}_serving_appt_id");

        // 1. If someone is currently serving, mark them completed
        if ($servingApptId) {
            $currentAppt = $todayAppointments->firstWhere('id', $servingApptId);
            if ($currentAppt && in_array($currentAppt->status, ['pending', 'confirmed'])) {
                $currentAppt->status = 'completed';
                $currentAppt->save();
            }
        }

        // 2. Find the next pending/confirmed patient
        $nextAppt = $todayAppointments
            ->whereIn('status', ['pending', 'confirmed'])
            ->when($servingApptId, fn($coll) => $coll->where('id', '!=', $servingApptId))
            ->first();

        if ($nextAppt) {
            // Confirm the appointment if it was pending
            if ($nextAppt->status === 'pending') {
                $nextAppt->status = 'confirmed';
                $nextAppt->save();
            }

            \Illuminate\Support\Facades\Cache::put("doctor_{$doctor->id}_serving_appt_id", $nextAppt->id, now()->endOfDay());

            $tokenMsg = $nextAppt->token_number ? " (" . $nextAppt->token_number . ")" : "";
            $patientName = optional(optional($nextAppt->patient)->user)->name ?? 'Patient';

            return redirect()->back()->with('success', "🔔 চেম্বারে ডাকা হয়েছে: {$patientName}{$tokenMsg}।");
        }

        // No more patients waiting
        \Illuminate\Support\Facades\Cache::forget("doctor_{$doctor->id}_serving_appt_id");

        return redirect()->back()->with('info', 'আজকের চেম্বারের সকল রোগীর অ্যাপয়েন্টমেন্ট সম্পন্ন হয়েছে।');
    }

    /**
     * Live Chamber & Online Queue: Call Specific Patient Out-of-Order
     */
    public function callSpecificPatient($id)
    {
        $doctor = $this->getDoctor();
        $appointment = Appointment::where('doctor_id', $doctor->id)->findOrFail($id);

        if ($appointment->status === 'pending') {
            $appointment->status = 'confirmed';
            $appointment->save();
        }

        if ($appointment->type === 'online') {
            \Illuminate\Support\Facades\Cache::put("doctor_{$doctor->id}_online_serving_appt_id", $appointment->id, now()->endOfDay());
            return redirect()->route('appointment.consultation', $appointment->id);
        }

        \Illuminate\Support\Facades\Cache::put("doctor_{$doctor->id}_serving_appt_id", $appointment->id, now()->endOfDay());

        $tokenMsg = $appointment->token_number ? " (" . $appointment->token_number . ")" : "";
        $patientName = optional(optional($appointment->patient)->user)->name ?? 'Patient';

        return redirect()->back()->with('success', "🔔 চেম্বারে ডাকা হয়েছে: {$patientName}{$tokenMsg}");
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

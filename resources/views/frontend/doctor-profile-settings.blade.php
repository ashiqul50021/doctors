@extends('layouts.app')

@section('title', 'Profile Settings - abcsheba')

@section('content')
@php
    $fullName = trim($doctor->user->name ?? '');
    // Remove "Dr." or "Dr" prefix cleanly for first name parsing
    $cleanName = preg_replace('/^Dr\.?\s+/i', '', $fullName);
    $nameParts = preg_split('/\s+/', $cleanName, 2);
    $firstName = old('first_name', $nameParts[0] ?? '');
    $lastName = old('last_name', $nameParts[1] ?? '');

    $requiredFieldsList = [
        'phone' => 'Phone Number',
        'gender' => 'Gender',
        'date_of_birth' => 'Date of Birth',
        'speciality_id' => 'Speciality',
        'qualification' => 'Qualification',
        'registration_number' => 'BMDC Registration No',
        'registration_date' => 'Registration Date',
        'bio' => 'Biography',
        'clinic_name' => 'Clinic Name',
        'clinic_address' => 'Clinic Address',
        'district_id' => 'District',
        'area_id' => 'Area / Police Station',
    ];

    $completedCount = 0;
    $missingFields = [];
    foreach ($requiredFieldsList as $field => $label) {
        if (!blank($doctor->{$field})) {
            $completedCount++;
        } else {
            $missingFields[] = $label;
        }
    }
    $totalCount = count($requiredFieldsList);
    $completionPercent = round(($completedCount / $totalCount) * 100);

    $services = old('services', implode(', ', json_decode($doctor->services ?? '[]', true) ?: []));
    $languages = old('languages', implode(', ', json_decode($doctor->languages ?? '[]', true) ?: []));
    $education = old('education', implode("\n", json_decode($doctor->education ?? '[]', true) ?: []));
    $awards = old('awards', implode("\n", json_decode($doctor->awards ?? '[]', true) ?: []));
    $clinicNames = old('clinic_names', $doctor->clinic_names ?? []);
    $clinicAddresses = old('clinic_addresses', $doctor->clinic_addresses ?? []);
    $clinicNames = is_array($clinicNames) ? $clinicNames : [];
    $clinicAddresses = is_array($clinicAddresses) ? $clinicAddresses : [];
    $clinicRows = [];
    $clinicCount = max(count($clinicNames), count($clinicAddresses), 1);
    for ($i = 0; $i < $clinicCount; $i++) {
        $clinicRows[] = [
            'name' => $clinicNames[$i] ?? '',
            'address' => $clinicAddresses[$i] ?? '',
        ];
    }

    $profileImage = asset('assets/img/doctors/doctor-thumb-02.jpg');
    if (!empty($doctor->profile_image)) {
        $profileImage = (str_starts_with($doctor->profile_image, 'uploads/') || str_starts_with($doctor->profile_image, 'assets/'))
            ? asset($doctor->profile_image)
            : asset('storage/' . $doctor->profile_image);
    }
@endphp

<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-md-5 col-lg-4 col-xl-3 theiaStickySidebar">
                @include('frontend.includes.doctor-sidebar')
            </div>

            <div class="col-md-7 col-lg-8 col-xl-9">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if(session('info'))
                    <div class="alert alert-info">{{ session('info') }}</div>
                @endif

                @if(session('warning'))
                    <div class="alert alert-warning">{{ session('warning') }}</div>
                @endif

                @if($doctor->status === 'pending')
                    <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #f0f4fd 0%, #e8effd 100%); border-left: 4px solid #2958B7 !important;">
                        <div class="card-body p-3 p-md-4">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user-clock fs-4 me-2" style="color: #2958B7;"></i>
                                    <div>
                                        <h5 class="mb-0 fw-bold text-dark">
                                            Account Status: <span class="badge bg-warning text-dark px-2 py-1">Pending Approval</span>
                                        </h5>
                                        <small class="text-muted">
                                            @if($doctor->isProfileComplete())
                                                Your profile details are complete. Waiting for admin review & approval.
                                            @else
                                                Please complete all required (<span class="text-danger">*</span>) fields to activate your doctor profile.
                                            @endif
                                        </small>
                                    </div>
                                </div>
                                <span class="badge text-white fs-6 px-3 py-2" style="background-color: {{ $completionPercent == 100 ? '#10b981' : '#2958B7' }};">
                                    {{ $completionPercent }}% Completed
                                </span>
                            </div>

                            <div class="progress my-2" style="height: 8px; border-radius: 4px; background-color: #cbd5e1;">
                                <div class="progress-bar" 
                                     role="progressbar" 
                                     style="width: {{ $completionPercent }}%; background-color: {{ $completionPercent == 100 ? '#10b981' : '#2958B7' }};" 
                                     aria-valuenow="{{ $completionPercent }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100"></div>
                            </div>

                            @if(!empty($missingFields))
                                <div class="mt-2 text-muted small">
                                    <strong class="text-dark"><i class="fas fa-exclamation-circle text-danger me-1"></i>Required fields remaining:</strong> 
                                    <span>{{ implode(', ', array_slice($missingFields, 0, 5)) }}{{ count($missingFields) > 5 ? ' and ' . (count($missingFields) - 5) . ' more...' : '' }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @elseif($doctor->status === 'rejected')
                    <div class="alert alert-danger d-flex align-items-center mb-3" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <div>Your doctor profile application was <strong>rejected</strong>. Please update your details or contact support.</div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('doctors.profile.settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <p class="text-muted mb-3">
                        <span class="text-danger">*</span> marked fields are required to complete and activate your profile.
                    </p>

                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Basic Information</h4>
                            <div class="row form-row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <div class="change-avatar">
                                            <div class="profile-img">
                                                <img id="profile-preview" src="{{ $profileImage }}" alt="User Image">
                                            </div>
                                            <div class="upload-img">
                                                <div class="change-photo-btn" onclick="document.getElementById('profile_image_input').click();" style="background-color: #2958B7 !important;">
                                                    <span><i class="fa fa-upload"></i> Upload Photo</span>
                                                    <input type="file" class="upload d-none" id="profile_image_input" name="profile_image" accept="image/*">
                                                </div>
                                                <small class="form-text text-muted d-block mt-2">Allowed JPG, PNG, GIF or WebP. Auto compressed.</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>Username</label>
                                        <input type="text" class="form-control" readonly value="{{ $doctor->slug }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>Email</label>
                                        <input type="email" class="form-control" readonly value="{{ $doctor->user->email }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>First Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="first_name" value="{{ $firstName }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>Last Name</label>
                                        <input type="text" class="form-control" name="last_name" value="{{ $lastName }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>Phone Number <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light text-muted fw-semibold border-end-0" style="border-radius: 6px 0 0 6px; border-color: #d1d5db;">+880</span>
                                            <input type="tel" class="form-control border-start-0" name="phone" required
                                                value="{{ old('phone', preg_replace('/^(880|0)/', '', $doctor->phone)) }}" placeholder="1XXXXXXXXX" maxlength="11"
                                                style="border-radius: 0 6px 6px 0;"
                                                oninput="this.value = this.value.replace(/[^0-9]/g, ''); if(this.value.startsWith('880')) this.value = this.value.substring(3); if(this.value.startsWith('0')) this.value = this.value.substring(1);">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>Gender <span class="text-danger">*</span></label>
                                        <select class="form-control" name="gender" required>
                                            <option value="">Select</option>
                                            <option value="male" {{ old('gender', $doctor->gender) === 'male' ? 'selected' : '' }}>Male</option>
                                            <option value="female" {{ old('gender', $doctor->gender) === 'female' ? 'selected' : '' }}>Female</option>
                                            <option value="other" {{ old('gender', $doctor->gender) === 'other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>Date of Birth <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="date_of_birth" id="date_of_birth"
                                            value="{{ old('date_of_birth', $doctor->date_of_birth) }}"
                                            max="{{ date('Y-m-d', strtotime('-18 years')) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>Experience (Years)</label>
                                        <input type="number" min="0" max="70" class="form-control" name="experience_years" value="{{ old('experience_years', $doctor->experience_years) }}" placeholder="e.g. 5">
                                    </div>
                                </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Professional Details</h4>
                            <div class="row form-row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>Qualification <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="qualification" value="{{ old('qualification', $doctor->qualification) }}" placeholder="MBBS, MD" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>Speciality <span class="text-danger">*</span></label>
                                        <select class="form-control" name="speciality_id" required>
                                            <option value="">Select Speciality</option>
                                            @foreach($specialities as $speciality)
                                                <option value="{{ $speciality->id }}" {{ (string) old('speciality_id', $doctor->speciality_id) === (string) $speciality->id ? 'selected' : '' }}>
                                                    {{ $speciality->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>Registration Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="registration_number" value="{{ old('registration_number', $doctor->registration_number) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>Registration Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="registration_date" value="{{ old('registration_date', $doctor->registration_date) }}" max="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3 mb-0">
                                        <label>Biography <span class="text-danger">*</span></label>
                                        <textarea class="form-control" rows="8" name="bio" style="min-height: 200px; resize: vertical;" required>{{ old('bio', $doctor->bio) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Clinic and Location</h4>
                            <div class="row form-row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>Consultation Fee</label>
                                        <input type="number" step="0.01" min="0" class="form-control" name="consultation_fee" value="{{ old('consultation_fee', $doctor->consultation_fee) }}">
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="mb-0">Chamber Details <span class="text-danger">*</span></label>
                                            <button type="button" class="btn btn-sm btn-outline-primary" id="add-clinic-row">
                                                Add More
                                            </button>
                                        </div>
                                        <div id="clinic-rows">
                                            @foreach($clinicRows as $index => $clinicRow)
                                                <div class="clinic-row border rounded p-3 mb-2">
                                                    <div class="row form-row align-items-end">
                                                        <div class="col-md-5">
                                                            <label>Clinic Name</label>
                                                            <input type="text" class="form-control" name="clinic_names[]" value="{{ $clinicRow['name'] }}" placeholder="e.g. Dhanmondi Chamber" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label>Clinic Address</label>
                                                            <input type="text" class="form-control" name="clinic_addresses[]" value="{{ $clinicRow['address'] }}" placeholder="e.g. Road 27, Dhanmondi, Dhaka" required>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <button type="button" class="btn btn-outline-danger w-100 remove-clinic-row">&times;</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>District <span class="text-danger">*</span></label>
                                        <select class="form-control" name="district_id" id="district_id" required>
                                            <option value="">Select District</option>
                                            @foreach($districts as $district)
                                                <option value="{{ $district->id }}" {{ (string) old('district_id', $doctor->district_id) === (string) $district->id ? 'selected' : '' }}>
                                                    {{ $district->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>Area <span class="text-danger">*</span></label>
                                        <select class="form-control" name="area_id" id="area_id" required>
                                            <option value="">Select Area</option>
                                            @foreach($areas as $area)
                                                <option value="{{ $area->id }}" {{ (string) old('area_id', $doctor->area_id) === (string) $area->id ? 'selected' : '' }}>
                                                    {{ $area->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3 form-check mt-2">
                                        <input class="form-check-input" type="checkbox" id="online_consultation" name="online_consultation" value="1" {{ old('online_consultation', $doctor->online_consultation) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="online_consultation">Online Consultation Available</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>Online Fee</label>
                                        <input type="number" step="0.01" min="0" class="form-control" name="online_fee" value="{{ old('online_fee', $doctor->online_fee) }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3 form-check mt-2">
                                        <input class="form-check-input" type="checkbox" id="home_visit" name="home_visit" value="1" {{ old('home_visit', $doctor->home_visit) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="home_visit">Home Visit Available</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>Home Visit Fee</label>
                                        <input type="number" step="0.01" min="0" class="form-control" name="home_visit_fee" value="{{ old('home_visit_fee', $doctor->home_visit_fee) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Other Details</h4>
                            <div class="row form-row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>Languages (comma separated)</label>
                                        <input type="text" class="form-control" name="languages" value="{{ $languages }}" placeholder="English, Bangla">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>Services (comma separated)</label>
                                        <input type="text" class="form-control" name="services" value="{{ $services }}" placeholder="General checkup, Follow-up">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>Website URL</label>
                                        <input type="url" class="form-control" name="website" value="{{ old('website', $doctor->website) }}" placeholder="https://example.com">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>Facebook URL</label>
                                        <input type="url" class="form-control" name="facebook" value="{{ old('facebook', $doctor->facebook) }}" placeholder="https://facebook.com/your-page">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>LinkedIn URL</label>
                                        <input type="url" class="form-control" name="linkedin" value="{{ old('linkedin', $doctor->linkedin) }}" placeholder="https://linkedin.com/in/username">
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label>Education (one per line)</label>
                                        <textarea class="form-control" rows="3" name="education">{{ $education }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3 mb-0">
                                        <label>Awards (one per line)</label>
                                        <textarea class="form-control" rows="3" name="awards">{{ $awards }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Custom Profile Sections -->
                    <div class="card shadow-sm border-0 mb-4" id="custom-sections-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                <div>
                                    <h4 class="card-title mb-1"><i class="fas fa-layer-group text-primary me-2"></i> Custom Profile Sections</h4>
                                    <p class="text-muted small mb-0">Add custom sections with text & image, or showcase banners/posters with an image-only section.</p>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-standard-section-btn">
                                        <i class="fas fa-file-alt me-1"></i> + Text & Image
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-info" id="add-image-section-btn">
                                        <i class="fas fa-image me-1"></i> + Image Only
                                    </button>
                                </div>
                            </div>

                            <input type="hidden" name="custom_sections_submitted" value="1">

                            <div id="custom-sections-container">
                                @php
                                    $existingSections = old('custom_sections', $doctor->custom_sections ?? []);
                                    $existingSections = is_array($existingSections) ? $existingSections : [];
                                @endphp

                                @forelse($existingSections as $index => $section)
                                    @php
                                        $secType = $section['type'] ?? 'standard';
                                        $secTitle = $section['title'] ?? '';
                                        $secDesc = $section['description'] ?? '';
                                        $secImage = $section['image'] ?? ($section['existing_image'] ?? null);
                                    @endphp

                                    @if($secType === 'image_only')
                                        <div class="custom-section-item border rounded-3 p-3 mb-3 bg-light position-relative" data-index="{{ $index }}" data-type="image_only">
                                            <input type="hidden" name="custom_sections[{{ $index }}][type]" value="image_only">
                                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                                <span class="badge bg-info text-dark section-badge px-3 py-2 fw-semibold">Photo Section #{{ $loop->iteration }}</span>
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-custom-section-btn">
                                                    <i class="fas fa-trash-alt me-1"></i> Remove
                                                </button>
                                            </div>

                                            <div class="row g-3 align-items-center">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Photo Title / Caption (Optional)</label>
                                                        <input type="text" class="form-control" name="custom_sections[{{ $index }}][title]" 
                                                               value="{{ $secTitle }}" placeholder="e.g. Chamber Visiting Hours Poster / Degree Certificate">
                                                    </div>
                                                    <div class="p-3 bg-white rounded border">
                                                        <p class="text-muted small mb-0">
                                                            <i class="fas fa-info-circle text-info me-1"></i> <strong>Image Only Section:</strong> This section displays your uploaded photo in full-width banner style on your profile details page.
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Upload Image <span class="text-danger">*</span></label>
                                                    <input type="hidden" name="custom_sections[{{ $index }}][existing_image]" value="{{ $secImage }}">
                                                    <input type="file" class="form-control section-image-input mb-2" 
                                                           name="custom_sections[{{ $index }}][image]" accept="image/*">
                                                    
                                                    <div class="section-image-preview-wrapper border rounded text-center p-2 bg-white" style="min-height: 120px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                                        @if(!empty($secImage))
                                                            <img src="{{ asset($secImage) }}" alt="Preview" class="img-fluid rounded section-preview-img" style="max-height: 130px; object-fit: contain;">
                                                        @else
                                                            <div class="text-muted small empty-preview-placeholder">
                                                                <i class="far fa-image fa-2x d-block mb-1 text-secondary"></i>
                                                                <span>No image selected</span>
                                                            </div>
                                                            <img src="" alt="Preview" class="img-fluid rounded section-preview-img d-none" style="max-height: 130px; object-fit: contain;">
                                                        @endif
                                                    </div>
                                                    <small class="text-muted d-block mt-1">Recommended: JPG, PNG, WebP (Max 10MB)</small>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="custom-section-item border rounded-3 p-3 mb-3 bg-light position-relative" data-index="{{ $index }}" data-type="standard">
                                            <input type="hidden" name="custom_sections[{{ $index }}][type]" value="standard">
                                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                                <span class="badge bg-primary section-badge px-3 py-2 fw-semibold">Section #{{ $loop->iteration }}</span>
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-custom-section-btn">
                                                    <i class="fas fa-trash-alt me-1"></i> Remove
                                                </button>
                                            </div>

                                            <div class="row g-3">
                                                <div class="col-md-8">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Section Title</label>
                                                        <input type="text" class="form-control" name="custom_sections[{{ $index }}][title]" 
                                                               value="{{ $secTitle }}" placeholder="e.g. Modern Laser Treatment Chamber">
                                                    </div>
                                                    <div class="mb-0">
                                                        <label class="form-label fw-semibold">Description / Details</label>
                                                        <textarea class="form-control" rows="4" name="custom_sections[{{ $index }}][description]" 
                                                                  placeholder="Provide full description, highlights, features, or instructions...">{{ $secDesc }}</textarea>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label fw-semibold">Section Image (Optional)</label>
                                                    <input type="hidden" name="custom_sections[{{ $index }}][existing_image]" value="{{ $secImage }}">
                                                    <input type="file" class="form-control section-image-input mb-2" 
                                                           name="custom_sections[{{ $index }}][image]" accept="image/*">
                                                    
                                                    <div class="section-image-preview-wrapper border rounded text-center p-2 bg-white" style="min-height: 120px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                                        @if(!empty($secImage))
                                                            <img src="{{ asset($secImage) }}" alt="Preview" class="img-fluid rounded section-preview-img" style="max-height: 110px; object-fit: cover;">
                                                        @else
                                                            <div class="text-muted small empty-preview-placeholder">
                                                                <i class="far fa-image fa-2x d-block mb-1 text-secondary"></i>
                                                                <span>No image selected</span>
                                                            </div>
                                                            <img src="" alt="Preview" class="img-fluid rounded section-preview-img d-none" style="max-height: 110px; object-fit: cover;">
                                                        @endif
                                                    </div>
                                                    <small class="text-muted d-block mt-1">Recommended: JPG, PNG, WebP (Max 10MB)</small>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @empty
                                    <div id="no-custom-sections-alert" class="alert alert-light border text-center py-4 mb-0">
                                        <i class="fas fa-layer-group text-muted fa-3x mb-2 d-block"></i>
                                        <p class="text-muted mb-3">No custom sections added yet.</p>
                                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                                            <button type="button" class="btn btn-outline-primary btn-sm" id="btn-add-first-standard">
                                                <i class="fas fa-file-alt me-1"></i> Add Text & Image Section
                                            </button>
                                            <button type="button" class="btn btn-outline-info btn-sm text-dark" id="btn-add-first-image">
                                                <i class="fas fa-image me-1"></i> Add Image Only Section
                                            </button>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="submit-section submit-btn-bottom">
                        <button type="submit" class="btn text-white px-5 py-2 fw-semibold" style="background-color: #2958B7; border-color: #2958B7; border-radius: 6px;">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const districtSelect = document.getElementById('district_id');
        const areaSelect = document.getElementById('area_id');
        const selectedArea = '{{ old('area_id', $doctor->area_id) }}';
        const clinicRows = document.getElementById('clinic-rows');
        const addClinicRowBtn = document.getElementById('add-clinic-row');
        const profileImageInput = document.getElementById('profile_image_input');
        const profilePreview = document.getElementById('profile-preview');

        // Date of Birth Synchronization
        const dobInput = document.getElementById('date_of_birth');
        const dobDay = document.getElementById('dob_day');
        const dobMonth = document.getElementById('dob_month');
        const dobYear = document.getElementById('dob_year');

        if (dobInput && dobDay && dobMonth && dobYear) {
            function updateDOB() {
                const day = dobDay.value;
                const month = dobMonth.value;
                const year = dobYear.value;
                if (day && month && year) {
                    dobInput.value = `${year}-${month}-${day}`;
                } else {
                    dobInput.value = '';
                }
            }

            // Initialize selects from existing date value
            const initialDate = dobInput.value;
            if (initialDate) {
                const parts = initialDate.split('-');
                if (parts.length === 3) {
                    dobYear.value = parts[0];
                    dobMonth.value = parts[1];
                    dobDay.value = parts[2];
                }
            }

            // Listen for change events on selects
            dobDay.addEventListener('change', updateDOB);
            dobMonth.addEventListener('change', updateDOB);
            dobYear.addEventListener('change', updateDOB);
        }

        function updateRemoveButtons() {
            if (!clinicRows) {
                return;
            }

            const rows = clinicRows.querySelectorAll('.clinic-row');
            rows.forEach((row, index) => {
                const removeBtn = row.querySelector('.remove-clinic-row');
                if (!removeBtn) {
                    return;
                }
                removeBtn.disabled = rows.length === 1 && index === 0;
            });
        }

        if (clinicRows) {
            clinicRows.addEventListener('click', function (event) {
                if (!event.target.classList.contains('remove-clinic-row')) {
                    return;
                }

                const rows = clinicRows.querySelectorAll('.clinic-row');
                if (rows.length <= 1) {
                    return;
                }

                event.target.closest('.clinic-row').remove();
                updateRemoveButtons();
            });
            updateRemoveButtons();
        }

        if (addClinicRowBtn && clinicRows) {
            addClinicRowBtn.addEventListener('click', function () {
                const row = document.createElement('div');
                row.className = 'clinic-row border rounded p-3 mb-2';
                row.innerHTML = `
                    <div class="row form-row align-items-end">
                        <div class="col-md-5">
                            <label>Clinic Name</label>
                            <input type="text" class="form-control" name="clinic_names[]" placeholder="e.g. Dhanmondi Chamber" required>
                        </div>
                        <div class="col-md-6">
                            <label>Clinic Address</label>
                            <input type="text" class="form-control" name="clinic_addresses[]" placeholder="e.g. Road 27, Dhanmondi, Dhaka" required>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-outline-danger w-100 remove-clinic-row">&times;</button>
                        </div>
                    </div>
                `;
                clinicRows.appendChild(row);
                updateRemoveButtons();
            });
        }

        if (profileImageInput && profilePreview) {
            profileImageInput.addEventListener('change', function (event) {
                const file = event.target.files && event.target.files[0];
                if (!file || !file.type.startsWith('image/')) {
                    return;
                }
                profilePreview.src = URL.createObjectURL(file);
            });
        }

        if (districtSelect && areaSelect) {
            districtSelect.addEventListener('change', function () {
                const districtId = this.value;
                areaSelect.innerHTML = '<option value="">Select Area</option>';

                if (!districtId) {
                    return;
                }

                fetch(`/api/areas/${districtId}`)
                    .then(response => response.json())
                    .then(areas => {
                        (areas || []).forEach(area => {
                            const option = document.createElement('option');
                            option.value = area.id;
                            option.textContent = area.name;
                            if (selectedArea && String(selectedArea) === String(area.id)) {
                                option.selected = true;
                            }
                            areaSelect.appendChild(option);
                        });
                    })
                    .catch(() => {
                        areaSelect.innerHTML = '<option value="">Select Area</option>';
                    });
            });
        }

        // Custom Sections Repeater Logic
        const customSectionsContainer = document.getElementById('custom-sections-container');
        const addStandardSectionBtn = document.getElementById('add-standard-section-btn');
        const addImageSectionBtn = document.getElementById('add-image-section-btn');

        function updateSectionBadges() {
            if (!customSectionsContainer) return;
            const items = customSectionsContainer.querySelectorAll('.custom-section-item');
            items.forEach((item, idx) => {
                const badge = item.querySelector('.section-badge');
                const isImageOnly = item.getAttribute('data-type') === 'image_only';
                if (badge) {
                    if (isImageOnly) {
                        badge.textContent = 'Photo Section #' + (idx + 1);
                        badge.className = 'badge bg-info text-dark section-badge px-3 py-2 fw-semibold';
                    } else {
                        badge.textContent = 'Section #' + (idx + 1);
                        badge.className = 'badge bg-primary section-badge px-3 py-2 fw-semibold';
                    }
                }
            });

            const noAlert = document.getElementById('no-custom-sections-alert');
            if (items.length === 0) {
                if (!noAlert) {
                    const alertDiv = document.createElement('div');
                    alertDiv.id = 'no-custom-sections-alert';
                    alertDiv.className = 'alert alert-light border text-center py-4 mb-0';
                    alertDiv.innerHTML = `
                        <i class="fas fa-layer-group text-muted fa-3x mb-2 d-block"></i>
                        <p class="text-muted mb-3">No custom sections added yet.</p>
                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="btn-add-first-standard">
                                <i class="fas fa-file-alt me-1"></i> Add Text & Image Section
                            </button>
                            <button type="button" class="btn btn-outline-info btn-sm text-dark" id="btn-add-first-image">
                                <i class="fas fa-image me-1"></i> Add Image Only Section
                            </button>
                        </div>
                    `;
                    customSectionsContainer.appendChild(alertDiv);
                }
            } else if (noAlert) {
                noAlert.remove();
            }
        }

        function createNewSection(type) {
            if (!customSectionsContainer) return;
            const noAlert = document.getElementById('no-custom-sections-alert');
            if (noAlert) noAlert.remove();

            const secType = (type === 'image_only') ? 'image_only' : 'standard';
            const index = Date.now();
            const currentCount = customSectionsContainer.querySelectorAll('.custom-section-item').length + 1;

            const sectionDiv = document.createElement('div');
            sectionDiv.className = 'custom-section-item border rounded-3 p-3 mb-3 bg-light position-relative';
            sectionDiv.setAttribute('data-index', index);
            sectionDiv.setAttribute('data-type', secType);

            if (secType === 'image_only') {
                sectionDiv.innerHTML = `
                    <input type="hidden" name="custom_sections[` + index + `][type]" value="image_only">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <span class="badge bg-info text-dark section-badge px-3 py-2 fw-semibold">Photo Section #` + currentCount + `</span>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-custom-section-btn">
                            <i class="fas fa-trash-alt me-1"></i> Remove
                        </button>
                    </div>

                    <div class="row g-3 align-items-center">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Photo Title / Caption (Optional)</label>
                                <input type="text" class="form-control" name="custom_sections[` + index + `][title]" placeholder="e.g. Visiting Hours Poster / Degree Certificate">
                            </div>
                            <div class="p-3 bg-white rounded border">
                                <p class="text-muted small mb-0">
                                    <i class="fas fa-info-circle text-info me-1"></i> <strong>Image Only Section:</strong> This section displays your uploaded photo in full-width banner style on your profile details page.
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Upload Image <span class="text-danger">*</span></label>
                            <input type="hidden" name="custom_sections[` + index + `][existing_image]" value="">
                            <input type="file" class="form-control section-image-input mb-2" name="custom_sections[` + index + `][image]" accept="image/*">
                            
                            <div class="section-image-preview-wrapper border rounded text-center p-2 bg-white" style="min-height: 120px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                <div class="text-muted small empty-preview-placeholder">
                                    <i class="far fa-image fa-2x d-block mb-1 text-secondary"></i>
                                    <span>No image selected</span>
                                </div>
                                <img src="" alt="Preview" class="img-fluid rounded section-preview-img d-none" style="max-height: 130px; object-fit: contain;">
                            </div>
                            <small class="text-muted d-block mt-1">Recommended: JPG, PNG, WebP (Max 10MB)</small>
                        </div>
                    </div>
                `;
            } else {
                sectionDiv.innerHTML = `
                    <input type="hidden" name="custom_sections[` + index + `][type]" value="standard">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <span class="badge bg-primary section-badge px-3 py-2 fw-semibold">Section #` + currentCount + `</span>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-custom-section-btn">
                            <i class="fas fa-trash-alt me-1"></i> Remove
                        </button>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Section Title</label>
                                <input type="text" class="form-control" name="custom_sections[` + index + `][title]" placeholder="e.g. Modern Laser Treatment Chamber">
                            </div>
                            <div class="mb-0">
                                <label class="form-label fw-semibold">Description / Details</label>
                                <textarea class="form-control" rows="4" name="custom_sections[` + index + `][description]" placeholder="Provide full description, highlights, features, or instructions..."></textarea>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Section Image (Optional)</label>
                            <input type="hidden" name="custom_sections[` + index + `][existing_image]" value="">
                            <input type="file" class="form-control section-image-input mb-2" name="custom_sections[` + index + `][image]" accept="image/*">
                            
                            <div class="section-image-preview-wrapper border rounded text-center p-2 bg-white" style="min-height: 120px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                <div class="text-muted small empty-preview-placeholder">
                                    <i class="far fa-image fa-2x d-block mb-1 text-secondary"></i>
                                    <span>No image selected</span>
                                </div>
                                <img src="" alt="Preview" class="img-fluid rounded section-preview-img d-none" style="max-height: 110px; object-fit: cover;">
                            </div>
                            <small class="text-muted d-block mt-1">Recommended: JPG, PNG, WebP (Max 10MB)</small>
                        </div>
                    </div>
                `;
            }

            customSectionsContainer.appendChild(sectionDiv);
            updateSectionBadges();
        }

        if (addStandardSectionBtn) {
            addStandardSectionBtn.addEventListener('click', function () {
                createNewSection('standard');
            });
        }

        if (addImageSectionBtn) {
            addImageSectionBtn.addEventListener('click', function () {
                createNewSection('image_only');
            });
        }

        document.addEventListener('click', function (e) {
            if (e.target && (e.target.id === 'btn-add-first-standard' || e.target.closest('#btn-add-first-standard'))) {
                createNewSection('standard');
            }

            if (e.target && (e.target.id === 'btn-add-first-image' || e.target.closest('#btn-add-first-image'))) {
                createNewSection('image_only');
            }

            if (e.target && (e.target.classList.contains('remove-custom-section-btn') || e.target.closest('.remove-custom-section-btn'))) {
                const btn = e.target.classList.contains('remove-custom-section-btn') ? e.target : e.target.closest('.remove-custom-section-btn');
                const item = btn.closest('.custom-section-item');
                if (item) {
                    item.remove();
                    updateSectionBadges();
                }
            }
        });

        // Delegate file preview for section images
        if (customSectionsContainer) {
            customSectionsContainer.addEventListener('change', function (e) {
                if (e.target && e.target.classList.contains('section-image-input')) {
                    const input = e.target;
                    const file = input.files && input.files[0];
                    const col = input.closest('[class*="col-"]');
                    if (!col) return;
                    const wrapper = col.querySelector('.section-image-preview-wrapper');
                    if (!wrapper) return;
                    const placeholder = wrapper.querySelector('.empty-preview-placeholder');
                    const img = wrapper.querySelector('.section-preview-img');

                    if (file && file.type.startsWith('image/')) {
                        if (img) {
                            img.src = URL.createObjectURL(file);
                            img.classList.remove('d-none');
                        }
                        if (placeholder) {
                            placeholder.classList.add('d-none');
                        }
                    }
                }
            });
        }
    });
</script>
@endpush

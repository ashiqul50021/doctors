@extends('layouts.app')

@php
    $rawName = $doctor->user->name ?? 'Doctor';
    $displayName = preg_match('/\bdr\.?\s+/i', $rawName) ? $rawName : 'Dr. ' . $rawName;
    $initialType = old('type', 'offline');
    $initialChamberFee = $doctor->clinic_locations[0]['fee'] ?? $doctor->consultation_fee ?? 0;
    $initialFee = $initialType === 'online' 
        ? ($doctor->online_fee ?? $doctor->consultation_fee ?? 0) 
        : $initialChamberFee;
    $selectedOldDate = old('appointment_date', '');
    $selectedOldTime = old('appointment_time', '');
@endphp

@section('title', 'Book Appointment - ' . $displayName . ' | ' . ($siteSettings['site_name'] ?? 'abcsheba'))

@section('content')

    <!-- Custom Theme-Matched Booking Styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/booking-modern.css') }}?v={{ @filemtime(base_path('assets/css/booking-modern.css')) }}">

    <div class="booking-page-wrapper">

        <!-- Breadcrumb Navigation -->
        <div class="booking-breadcrumb-nav">
            <div class="container">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="fas fa-home me-1"></i> Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('doctors.search') }}">Doctors</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('doctors.profile', $doctor->id) }}">{{ $displayName }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Book Appointment</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="container py-4">

            {{-- Alerts --}}
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert" style="border-radius: 10px;">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-circle text-danger me-2 fs-5"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert" style="border-radius: 10px;">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-exclamation-triangle text-danger me-2 fs-5 mt-1"></i>
                        <ul class="mb-0 ps-2">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Doctor hero: same top part as the doctor profile page -->
            @php
                $bkChambers = collect($doctor->clinic_locations)->pluck('name')->filter()->implode(', ');
                $bkLocation = trim(($doctor->area->name ?? '') . ', ' . ($doctor->district->name ?? ''), ', ');
                $bkOnline = optional($doctor->user)->isOnline();
            @endphp
            <div class="bk-hero">
                <div class="bk-hero-photo">
                    <a href="{{ route('doctors.profile', $doctor->id) }}">
                        <img src="{{ $doctor->profile_image ? asset($doctor->profile_image) : asset('assets/img/doctors/doctor-thumb-02.jpg') }}"
                             alt="{{ $displayName }}"
                             onerror="this.src='{{ asset('assets/img/doctors/doctor-thumb-02.jpg') }}'">
                    </a>
                </div>
                <div class="bk-hero-body">
                    <h3 class="bk-hero-name"><a href="{{ route('doctors.profile', $doctor->id) }}">{{ $displayName }}</a></h3>
                    @if($doctor->qualification)
                        <p class="bk-hero-qual">{{ $doctor->qualification }}</p>
                    @endif
                    <div class="bk-hero-chips">
                        @if($doctor->speciality)
                            <span class="bk-chip">
                                @if($doctor->speciality->image)
                                    <img src="{{ asset($doctor->speciality->image) }}" alt="">
                                @else
                                    <i class="fas fa-stethoscope"></i>
                                @endif
                                {{ $doctor->speciality->name }}
                            </span>
                        @endif
                        <span class="bk-chip {{ $bkOnline ? 'online' : '' }}">{{ $bkOnline ? 'Online Now' : 'Offline' }}</span>
                    </div>
                    @if($doctor->short_designation)
                        <p class="bk-hero-desig"><i class="fas fa-briefcase-medical me-1"></i> {{ $doctor->short_designation }}</p>
                    @endif
                    @if($bkChambers !== '')
                        <div class="bk-hero-line"><i class="fas fa-clinic-medical"></i><strong>Chamber:</strong> {{ $bkChambers }}</div>
                    @endif
                    @if($bkLocation !== '')
                        <div class="bk-hero-line"><i class="fas fa-map-marker-alt"></i><strong>Location:</strong> {{ $bkLocation }}</div>
                    @endif
                    <div class="bk-hero-fees">
                        <span class="bk-fee"><i class="fas fa-building"></i> Chamber Fee: <strong>৳{{ number_format((float) $initialChamberFee, 0) }}</strong></span>
                        @if(($doctor->online_fee ?? 0) > 0)
                            <span class="bk-fee online"><i class="fas fa-video"></i> Video Fee: <strong>৳{{ number_format((float) $doctor->online_fee, 0) }}</strong></span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Booking Main Form -->
            <form action="{{ route('booking.submit', $doctor->id) }}" method="POST" id="mainBookingForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="appointment_date" id="appointment_date" value="{{ old('appointment_date') }}">
                <input type="hidden" name="appointment_time" id="appointment_time" value="{{ old('appointment_time') }}">

                <div class="row g-4">
                    <!-- Left Column: Step 1 (Mode), Step 2 (Date & Slot), Step 3 (Patient Info) -->
                    <div class="col-lg-8">

                        <!-- Step 1: Consultation Mode -->
                        <div class="booking-section-card">
                            <div class="section-card-header">
                                <div class="step-indicator">1</div>
                                <div>
                                    <h5 class="section-heading mb-0">Choose Consultation Mode</h5>
                                </div>
                            </div>

                            <div class="section-card-body">
                                <div class="mode-selector-grid">
                                    <!-- Chamber Visit Option -->
                                    <label class="mode-card {{ $initialType === 'offline' ? 'is-active' : '' }}" for="type_offline" id="label_type_offline">
                                        <input type="radio" name="type" id="type_offline" value="offline"
                                               {{ $initialType === 'offline' ? 'checked' : '' }}
                                               data-fee="{{ $doctor->consultation_fee ?? 0 }}"
                                               data-type-label="In-Clinic Chamber Visit"
                                               class="mode-radio">
                                        <div class="mode-card-content">
                                            <div class="mode-icon-box mode-icon-chamber">
                                                <i class="fas fa-hospital-user"></i>
                                            </div>
                                            <div class="mode-text-box">
                                                <div class="d-flex align-items-center justify-content-between mb-1">
                                                    <span class="mode-title">In-Clinic Chamber Visit</span>
                                                    <span class="mode-price" id="chamber-mode-price">৳{{ number_format((float) $initialChamberFee, 0) }}</span>
                                                </div>
                                                <p class="mode-desc mb-0">
                                                    {{ $doctor->clinic_name ?: 'Doctor Chamber' }} • Pay fee at clinic • Serial token issued
                                                </p>
                                            </div>
                                            <div class="mode-check-mark">
                                                <i class="fas fa-check"></i>
                                            </div>
                                        </div>
                                    </label>

                                    <!-- Video Call Option -->
                                    @if(($doctor->online_fee ?? 0) > 0)
                                        <label class="mode-card {{ $initialType === 'online' ? 'is-active' : '' }}" for="type_online" id="label_type_online">
                                            <input type="radio" name="type" id="type_online" value="online"
                                                   {{ $initialType === 'online' ? 'checked' : '' }}
                                                   data-fee="{{ $doctor->online_fee ?? $doctor->consultation_fee ?? 0 }}"
                                                   data-type-label="Online Video Consultation"
                                                   class="mode-radio">
                                            <div class="mode-card-content">
                                                <div class="mode-icon-box mode-icon-video">
                                                    <i class="fas fa-video"></i>
                                                </div>
                                                <div class="mode-text-box">
                                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                                        <span class="mode-title">Online Video Consultation</span>
                                                        <span class="mode-price">৳{{ number_format((float) ($doctor->online_fee ?? 0), 0) }}</span>
                                                    </div>
                                                    <p class="mode-desc mb-0">
                                                        HD Video Call • Join from mobile/laptop • Instant meeting link via SMS
                                                    </p>
                                                </div>
                                                <div class="mode-check-mark">
                                                    <i class="fas fa-check"></i>
                                                </div>
                                            </div>
                                        </label>
                                    @endif
                                </div>

                                @if(count($doctor->clinic_locations) > 1)
                                    <div class="chamber-selector-box mt-3 p-3 border rounded-3 bg-light" id="chamberSelectorWrapper" style="{{ $initialType === 'offline' ? '' : 'display: none;' }}">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="fw-bold text-dark fs-6"><i class="fas fa-hospital-alt text-primary me-2"></i>Select Consultation Chamber</span>
                                            <span class="text-muted small">Choose your preferred clinic</span>
                                        </div>
                                        <div class="row g-2">
                                            @foreach($doctor->clinic_locations as $cIdx => $loc)
                                                <div class="col-md-6">
                                                    <label class="chamber-card-label border rounded-3 p-3 d-flex justify-content-between align-items-center w-100 mb-0 bg-white shadow-sm" style="cursor: pointer; transition: all 0.2s;">
                                                        <div class="d-flex align-items-center">
                                                            <input type="radio" name="clinic_index" value="{{ $cIdx }}" class="me-2 chamber-radio" {{ (string) old('clinic_index', 0) === (string) $cIdx ? 'checked' : '' }} data-fee="{{ $loc['fee'] }}" data-clinic-name="{{ $loc['name'] }}" data-clinic-address="{{ $loc['address'] }}">
                                                            <div>
                                                                <strong class="d-block text-dark">{{ $loc['name'] }}</strong>
                                                                <small class="text-muted d-block"><i class="fas fa-map-marker-alt text-danger me-1"></i>{{ $loc['address'] }}</small>
                                                            </div>
                                                        </div>
                                                        <span class="badge bg-primary text-white ms-2 px-2.5 py-1.5" style="font-size: 13px;">
                                                            ৳{{ number_format((float) $loc['fee'], 0) }}
                                                        </span>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @elseif(count($doctor->clinic_locations) === 1)
                                    <input type="hidden" name="clinic_index" value="0" class="chamber-radio" data-fee="{{ $doctor->clinic_locations[0]['fee'] }}" data-clinic-name="{{ $doctor->clinic_locations[0]['name'] }}" data-clinic-address="{{ $doctor->clinic_locations[0]['address'] }}">
                                @endif
                            </div>
                        </div>

                        <!-- Step 2: Date & Available Slots (Year -> Month -> Day -> Slots) -->
                        <div class="booking-section-card">
                            <div class="section-card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="step-indicator">2</div>
                                    <div>
                                        <h5 class="section-heading mb-0">Select Date & Time Slot</h5>
                                    </div>
                                </div>

                                <div class="slot-legend d-flex align-items-center gap-3">
                                    <span class="legend-item"><span class="legend-dot available"></span> Available</span>
                                    <span class="legend-item"><span class="legend-dot selected"></span> Selected</span>
                                    <span class="legend-item"><span class="legend-dot booked"></span> Booked</span>
                                </div>
                            </div>

                            <div class="section-card-body">
                                
                                <!-- 1-3. Year / Month / Day: compact dropdown boxes -->
                                <div class="selection-level">
                                    <div class="dt-select-row">
                                        <div class="dt-dropdown" id="yearDropdown">
                                            <span class="dt-label">Year</span>
                                            <button type="button" class="dt-trigger" onclick="toggleDtDropdown(event, 'yearDropdown')">
                                                <span id="yearTriggerText">{{ $initialDate->year }}</span>
                                                <i class="fas fa-chevron-down"></i>
                                            </button>
                                            <div class="dt-menu" id="yearSelector">
                                                @foreach($availableYears as $y)
                                                    <button type="button"
                                                            class="year-pill-btn dt-option {{ $y == $initialDate->year ? 'active' : '' }}"
                                                            data-year="{{ $y }}"
                                                            onclick="selectYear({{ $y }})">{{ $y }}</button>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="dt-dropdown" id="monthDropdown">
                                            <span class="dt-label">Month</span>
                                            <button type="button" class="dt-trigger" onclick="toggleDtDropdown(event, 'monthDropdown')">
                                                <span id="monthTriggerText">{{ strtoupper($initialDate->format('M')) }}</span>
                                                <i class="fas fa-chevron-down"></i>
                                            </button>
                                            <div class="dt-menu" id="monthSelector">
                                                <!-- Rendered dynamically by JS -->
                                            </div>
                                        </div>

                                        <div class="dt-dropdown" id="dayDropdown">
                                            <span class="dt-label">Day</span>
                                            <button type="button" class="dt-trigger" onclick="toggleDtDropdown(event, 'dayDropdown')">
                                                <span id="dayTriggerText">{{ $initialDate->format('d D') }}</span>
                                                <i class="fas fa-chevron-down"></i>
                                            </button>
                                            <div class="dt-menu dt-menu-days" id="dayStripScroller">
                                                <!-- Rendered dynamically by JS -->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-muted small mt-2" id="doctorVisitingDaysNotice">
                                        <i class="fas fa-calendar-day text-primary me-1"></i> Chamber days: <strong>{{ count($scheduledDays) > 0 ? implode(', ', array_map('ucfirst', $scheduledDays)) : 'Check schedule' }}</strong>
                                    </div>
                                </div>

                                <!-- 4. Time Slot Dropdown Area -->
                                <div class="selection-level mt-3">
                                    <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-1">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="level-badge"><i class="far fa-clock me-1"></i> Time Slot</span>
                                            <span class="level-title">Select Available Time</span>
                                        </div>
                                    </div>

                                    <!-- Loading Spinner -->
                                    <div id="slotsLoadingSpinner" class="text-center py-3" style="display: none;">
                                        <div class="spinner-border text-primary spinner-border-sm me-2" role="status">
                                            <span class="visually-hidden">Loading slots...</span>
                                        </div>
                                        <span class="text-muted small">Checking doctor's available slots...</span>
                                    </div>

                                    <!-- Slots Dropdown Container -->
                                    <div id="dynamicSlotsGridContainer">
                                        <!-- Slot dropdown injected here by JS -->
                                    </div>

                                    <!-- Empty / Off-Day Notice -->
                                    <div id="slotsEmptyNotice" class="text-center py-4" style="display: none;">
                                        <i class="far fa-calendar-times text-muted fs-3 mb-2 d-block"></i>
                                        <h6 class="text-dark fw-bold mb-1" id="slotsEmptyTitle">No Available Slots on This Day</h6>
                                        <p class="text-muted small mb-0" id="slotsEmptyDesc">Doctor is unavailable on this date. Please pick another date above.</p>
                                    </div>
                                </div>

                                <!-- Selected Slot Callout Notification -->
                                <div id="selectedSlotIndicator" class="selected-slot-callout mt-4" style="display: none;">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="callout-icon">
                                            <i class="fas fa-calendar-check"></i>
                                        </div>
                                        <div>
                                            <div class="callout-title">
                                                <span id="selectedSlotDateDisplay"></span>
                                                <span class="callout-bullet">•</span>
                                                <span id="selectedSlotTimeDisplay" class="text-primary fw-bold"></span>
                                            </div>
                                            <div class="callout-subtitle" id="selectedSlotModeSubtitle">
                                                In-Clinic Chamber Visit at {{ $doctor->clinic_name ?: 'Doctor Chamber' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="callout-badge">
                                        <i class="fas fa-check-circle me-1"></i> Slot Selected
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Step 3: Patient Information Form -->
                        <div class="booking-section-card">
                            <div class="section-card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="step-indicator">3</div>
                                    <div>
                                        <h5 class="section-heading mb-0">Patient Information</h5>
                                    </div>
                                </div>

                                @guest
                                    <span class="user-status-chip guest">
                                        <i class="fas fa-bolt text-warning me-1"></i> Fast Guest Booking (No Account Required)
                                    </span>
                                @endguest
                            </div>

                            <div class="section-card-body">
                                <div class="row g-3">
                                    <!-- Full Name -->
                                    <div class="col-md-6">
                                        <label class="custom-form-label" for="patient_name">
                                            Patient Full Name <span class="text-danger">*</span>
                                        </label>
                                        <div class="custom-input-group">
                                            <span class="input-icon"><i class="far fa-user"></i></span>
                                            <input type="text"
                                                   name="patient_name"
                                                   id="patient_name"
                                                   class="form-control custom-input"
                                                   placeholder="e.g. John Doe"
                                                   value="{{ old('patient_name', Auth::check() ? Auth::user()->name : '') }}"
                                                   required>
                                        </div>
                                    </div>

                                    <!-- Phone Number -->
                                    <div class="col-md-6">
                                        <label class="custom-form-label" for="patient_phone">
                                            Phone Number <span class="text-danger">*</span>
                                        </label>
                                        <div class="custom-input-group">
                                            <span class="input-icon"><i class="fas fa-phone-alt"></i></span>
                                            <input type="tel"
                                                   name="phone"
                                                   id="patient_phone"
                                                   class="form-control custom-input"
                                                   placeholder="01XXXXXXXXX"
                                                   value="{{ old('phone', Auth::check() && Auth::user()->patient ? Auth::user()->patient->phone : '') }}"
                                                   required>
                                        </div>
                                        <div class="form-helper-text">
                                            <i class="fas fa-shield-alt text-primary me-1"></i> Booking confirmation SMS and token will be sent to this number.
                                        </div>
                                    </div>

                                    <!-- Email Address (Optional) -->
                                    <div class="col-md-6">
                                        <label class="custom-form-label" for="patient_email">
                                            Email Address <span class="text-muted fw-normal">(Optional)</span>
                                        </label>
                                        <div class="custom-input-group">
                                            <span class="input-icon"><i class="far fa-envelope"></i></span>
                                            <input type="email"
                                                   name="email"
                                                   id="patient_email"
                                                   class="form-control custom-input"
                                                   placeholder="patient@example.com"
                                                   value="{{ old('email', Auth::check() ? Auth::user()->email : '') }}">
                                        </div>
                                        <div class="form-helper-text">Video consultation link and digital prescription will be sent to this email.</div>
                                    </div>

                                    <!-- Reason / Health Concern -->
                                    <div class="col-md-6">
                                        <label class="custom-form-label" for="reason">
                                            Health Concern / Reason <span class="text-muted fw-normal">(Optional)</span>
                                        </label>
                                        <div class="custom-input-group">
                                            <span class="input-icon"><i class="far fa-comment-medical"></i></span>
                                            <input type="text"
                                                   name="reason"
                                                   id="reason"
                                                   class="form-control custom-input"
                                                   placeholder="e.g. Chest pain, Headache, Regular checkup"
                                                   value="{{ old('reason') }}">
                                        </div>
                                    </div>

                                    <!-- Attach Previous Prescriptions / Reports -->
                                    <div class="col-12 mt-3">
                                        <label class="custom-form-label d-flex align-items-center justify-content-between mb-2">
                                            <span>
                                                <i class="fas fa-file-medical text-primary me-1"></i> 
                                                Previous Prescription or Medical Reports <span class="text-muted fw-normal">(Optional)</span>
                                            </span>
                                            <span class="badge bg-light text-muted border">Max 5MB (JPG, PNG, PDF)</span>
                                        </label>
                                        
                                        <div class="report-dropzone">
                                            <input type="file" name="reports[]" id="report_files" class="d-none" multiple accept=".jpg,.jpeg,.png,.webp,.pdf">
                                            <label for="report_files" class="dropzone-label">
                                                <div class="dropzone-icon">
                                                    <i class="fas fa-cloud-upload-alt"></i>
                                                </div>
                                                <div class="dropzone-text">
                                                    <strong>Click to attach prescription or report images / PDF</strong>
                                                    <span>Doctor can review your previous reports before consultation</span>
                                                </div>
                                            </label>
                                            <div id="file_preview_list" class="file-preview-grid mt-3"></div>
                                        </div>
                                    </div>
                                </div>

                                @guest
                                    <div class="guest-reassurance-box mt-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                            <span class="small text-muted">
                                                <i class="fas fa-info-circle text-primary me-1"></i> 
                                                Already have an account? 
                                                <a href="{{ route('login') }}" class="text-primary fw-semibold">Login</a>
                                            </span>
                                        </div>
                                    </div>
                                @endguest
                            </div>
                        </div>

                    </div>

                    <!-- Right Column: Live Sticky Summary Sidebar -->
                    <div class="col-lg-4">
                        <div class="booking-summary-column">
                            <div class="booking-summary-card">
                                <div class="summary-card-header">
                                    <i class="fas fa-file-invoice-dollar me-2"></i>
                                    <span>Appointment Summary</span>
                                </div>

                                <div class="summary-card-body">
                                    <!-- Doctor Mini Profile -->
                                    <div class="summary-doctor-mini">
                                        <img src="{{ $doctor->profile_image ? asset($doctor->profile_image) : asset('assets/img/doctors/doctor-thumb-02.jpg') }}"
                                             alt="{{ $displayName }}"
                                             onerror="this.src='{{ asset('assets/img/doctors/doctor-thumb-02.jpg') }}'"
                                             class="mini-doctor-avatar">
                                        <div class="mini-doctor-info">
                                            <h6 class="mb-0 text-dark fw-bold">{{ $displayName }}</h6>
                                            <span class="text-muted small">{{ $doctor->speciality->name ?? 'Specialist' }}</span>
                                        </div>
                                    </div>

                                    <div class="summary-divider"></div>

                                    <!-- Summary Rows -->
                                    <div class="summary-row">
                                        <span class="row-label"><i class="fas fa-stethoscope me-1 text-muted"></i> Consultation</span>
                                        <span class="row-value badge bg-primary text-white px-2 py-1" id="summaryApptType">
                                            {{ $initialType === 'online' ? 'Video Consultation' : 'In-Clinic Visit' }}
                                        </span>
                                    </div>

                                    <div class="summary-row">
                                        <span class="row-label"><i class="far fa-calendar-alt me-1 text-muted"></i> Date</span>
                                        <span class="row-value fw-semibold text-dark" id="summaryDate">Select a slot</span>
                                    </div>

                                    <div class="summary-row">
                                        <span class="row-label"><i class="far fa-clock me-1 text-muted"></i> Time Slot</span>
                                        <span class="row-value fw-semibold text-primary" id="summaryTime">-- : --</span>
                                    </div>

                                    <div class="summary-row">
                                        <span class="row-label"><i class="fas fa-map-marker-alt me-1 text-muted"></i> Location</span>
                                        <span class="row-value text-end text-muted small" id="summaryLocation">
                                            {{ $doctor->clinic_name ?: 'Chamber' }}
                                        </span>
                                    </div>

                                    <div class="summary-divider"></div>

                                    <!-- Fee Breakdown -->
                                    <div class="summary-row">
                                        <span class="row-label">Doctor Fee</span>
                                        <span class="row-value" id="summaryFee">৳{{ number_format((float) $initialFee, 0) }}</span>
                                    </div>

                                    <div class="summary-row">
                                        <span class="row-label">Platform Fee</span>
                                        <span class="row-value text-primary fw-semibold">FREE (৳0)</span>
                                    </div>

                                    <div class="summary-row total-row">
                                        <span class="row-label">Total Payable</span>
                                        <span class="row-value total-amount" id="summaryTotal">
                                            ৳{{ number_format((float) $initialFee, 0) }}
                                        </span>
                                    </div>

                                    <!-- Payment Mode Note (Chamber vs Online) -->
                                    <div id="paymentModeNote" class="payment-instruction-box chamber-note mt-3" style="{{ $initialType === 'offline' ? '' : 'display: none;' }}">
                                        <div class="instruction-icon"><i class="fas fa-hand-holding-usd"></i></div>
                                        <div class="instruction-text">
                                            <strong>Pay at Chamber</strong>
                                            <span>Pay consultation fee directly at the chamber. No advance fee required.</span>
                                        </div>
                                    </div>

                                    <div id="onlinePaymentOptions" class="payment-instruction-box online-note mt-3 flex-column align-items-stretch" style="{{ $initialType === 'online' ? 'display: block;' : 'display: none;' }}; padding: 16px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px;">
                                        <!-- Slot Selection Reminder Alert -->
                                        <div id="paymentSlotReminder" class="alert alert-warning py-2 px-3 small mb-3 d-flex align-items-center" style="border-radius: 6px;">
                                            <i class="fas fa-exclamation-triangle me-2 text-warning fs-5 flex-shrink-0"></i>
                                            <div style="font-size: 11px; line-height: 1.4;">
                                                <strong>Please select a time slot first:</strong> Confirm appointment time from schedule before sending money to this number.
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center mb-2 pb-2 border-bottom">
                                            <i class="fas fa-mobile-alt text-primary fs-5 me-2"></i>
                                            <div>
                                                <strong class="d-block text-dark" style="font-size: 14px;">Online Payment</strong>
                                                <small class="text-muted" style="font-size: 12px;">Pay by bKash / Nagad (manual) or SSLCommerz (card, mobile banking)</small>
                                            </div>
                                        </div>

                                        <!-- Select Payment Method Radios -->
                                        <div class="mb-2">
                                            <label class="form-label small fw-bold text-muted mb-1" style="font-size: 12px;">Choose Payment Method:</label>
                                            <div class="d-flex gap-2">
                                                <label class="btn btn-sm btn-outline-danger flex-fill text-center py-2 payment-radio-btn active" id="btnRadioBkash" style="border-radius: 4px; font-weight: 500; font-size: 12px; cursor: pointer;">
                                                    <input type="radio" name="payment_method" value="bkash" class="d-none" checked onchange="switchManualPayment('bkash')">
                                                    <i class="fas fa-wallet me-1"></i> bKash
                                                </label>
                                                <label class="btn btn-sm btn-outline-warning flex-fill text-center py-2 payment-radio-btn" id="btnRadioNagad" style="border-radius: 4px; font-weight: 500; font-size: 12px; cursor: pointer;">
                                                    <input type="radio" name="payment_method" value="nagad" class="d-none" onchange="switchManualPayment('nagad')">
                                                    <i class="fas fa-wallet me-1"></i> Nagad
                                                </label>
                                                @if(!empty($siteSettings['manual_rocket_number']))
                                                <label class="btn btn-sm btn-outline-primary flex-fill text-center py-2 payment-radio-btn" id="btnRadioRocket" style="border-radius: 4px; font-weight: 500; font-size: 12px; cursor: pointer;">
                                                    <input type="radio" name="payment_method" value="rocket" class="d-none" onchange="switchManualPayment('rocket')">
                                                    <i class="fas fa-wallet me-1"></i> Rocket
                                                </label>
                                                @endif
                                                <label class="btn btn-sm btn-outline-success flex-fill text-center py-2 payment-radio-btn" id="btnRadioSslcommerz" style="border-radius: 4px; font-weight: 500; font-size: 12px; cursor: pointer;">
                                                    <input type="radio" name="payment_method" value="sslcommerz" class="d-none" onchange="switchManualPayment('sslcommerz')">
                                                    <i class="fas fa-credit-card me-1"></i> SSLCommerz
                                                </label>
                                            </div>
                                        </div>

                                        <!-- SSLCommerz: redirect to the secure gateway, no manual details needed -->
                                        <div id="sslczInfoBox" class="p-2 mb-2 rounded" style="display: none; background: #F0FDF4; border: 1px dashed #86EFAC; font-size: 12px; line-height: 1.5;">
                                            <i class="fas fa-lock text-success me-1"></i>
                                            After you confirm, you will be taken to the secure <strong>SSLCommerz</strong> page to pay by card, bKash, Nagad, Rocket or internet banking. Your appointment is confirmed once the payment succeeds.
                                        </div>

                                        <div id="manualPaymentFields">
                                        <!-- Payment Account Number Display -->
                                        <div class="p-2 mb-2 rounded" style="background: #F0FDF4; border: 1px dashed #86EFAC; font-size: 13px;">
                                            <div id="bkashInfoBox">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="text-danger fw-bold"><i class="fas fa-arrow-circle-right me-1"></i> bKash Number:</span>
                                                    <span class="badge bg-danger text-white">{{ $siteSettings['manual_bkash_type'] ?? 'Personal (Send Money)' }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mt-1">
                                                    <strong class="text-dark fs-6" id="bkashNumText">{{ $siteSettings['manual_bkash_number'] ?? '01711111111' }}</strong>
                                                    <button type="button" class="btn btn-sm btn-light py-0 px-2 border" onclick="copyTextToClipboard('{{ $siteSettings['manual_bkash_number'] ?? '01711111111' }}', this)" title="Copy Number">
                                                        <i class="far fa-copy text-primary"></i> <span class="small">Copy</span>
                                                    </button>
                                                </div>
                                            </div>

                                            <div id="nagadInfoBox" style="display: none;">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="text-warning fw-bold"><i class="fas fa-arrow-circle-right me-1"></i> Nagad Number:</span>
                                                    <span class="badge bg-warning text-dark">{{ $siteSettings['manual_nagad_type'] ?? 'Personal (Send Money)' }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mt-1">
                                                    <strong class="text-dark fs-6" id="nagadNumText">{{ $siteSettings['manual_nagad_number'] ?? '01811111111' }}</strong>
                                                    <button type="button" class="btn btn-sm btn-light py-0 px-2 border" onclick="copyTextToClipboard('{{ $siteSettings['manual_nagad_number'] ?? '01811111111' }}', this)" title="Copy Number">
                                                        <i class="far fa-copy text-primary"></i> <span class="small">Copy</span>
                                                    </button>
                                                </div>
                                            </div>

                                            @if(!empty($siteSettings['manual_rocket_number']))
                                            <div id="rocketInfoBox" style="display: none;">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="text-primary fw-bold"><i class="fas fa-arrow-circle-right me-1"></i> Rocket Number:</span>
                                                    <span class="badge bg-primary text-white">{{ $siteSettings['manual_rocket_type'] ?? 'Personal (Send Money)' }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mt-1">
                                                    <strong class="text-dark fs-6" id="rocketNumText">{{ $siteSettings['manual_rocket_number'] }}</strong>
                                                    <button type="button" class="btn btn-sm btn-light py-0 px-2 border" onclick="copyTextToClipboard('{{ $siteSettings['manual_rocket_number'] }}', this)" title="Copy Number">
                                                        <i class="far fa-copy text-primary"></i> <span class="small">Copy</span>
                                                    </button>
                                                </div>
                                            </div>
                                            @endif

                                            <div class="text-muted mt-2 small" style="font-size: 11px; line-height: 1.4;">
                                                <i class="fas fa-info-circle text-primary me-1"></i> {{ $siteSettings['manual_payment_instruction'] ?? 'Send Money to the above number and enter your Sender Number & TrxID below.' }}
                                            </div>
                                        </div>

                                        <!-- Inputs for Sender Number and TrxID -->
                                        <div class="row g-2">
                                            <div class="col-12 col-sm-6">
                                                <label class="form-label small fw-semibold text-dark mb-1" style="font-size: 12px;">
                                                    Sender Number <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control form-control-sm" name="sender_number" id="sender_number" placeholder="01XXXXXXXXX" value="{{ old('sender_number') }}" style="border-radius: 4px;">
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <label class="form-label small fw-semibold text-dark mb-1" style="font-size: 12px;">
                                                    Transaction ID (TrxID) <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control form-control-sm" name="transaction_id" id="transaction_id" placeholder="e.g. 9J3K8L2P" value="{{ old('transaction_id') }}" style="border-radius: 4px;">
                                            </div>
                                        </div>
                                        </div><!-- /manualPaymentFields -->
                                    </div>

                                    <!-- Submit Action Button -->
                                    <div class="mt-4">
                                        <button type="submit" id="btnConfirmBooking" class="btn btn-booking-action w-100" disabled>
                                            <i class="fas fa-calendar-check me-2"></i>
                                            <span id="submitBtnText">Please Select a Time Slot</span>
                                        </button>
                                    </div>

                                    <!-- Reassurance Badges -->
                                    <div class="summary-trust-badges mt-4">
                                        <div class="trust-badge-item">
                                            <i class="fas fa-check-circle text-primary"></i>
                                            <span>100% Verified Specialist Doctor</span>
                                        </div>
                                        <div class="trust-badge-item">
                                            <i class="fas fa-comment-dots text-primary"></i>
                                            <span>Instant Serial Token & SMS Alert</span>
                                        </div>
                                        <div class="trust-badge-item">
                                            <i class="fas fa-shield-alt text-secondary"></i>
                                            <span>Safe & Confidential Medical Data</span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </form>

        </div>
    </div>

    <!-- Booking Interactive Logic -->
    <style>
        .bk-hero {
            display: flex; gap: 20px; align-items: flex-start;
            background: linear-gradient(135deg, #eff6ff 0%, #ffffff 60%);
            border: 1px solid #e5eaf3; border-radius: 18px; padding: 18px; margin-bottom: 16px;
            box-shadow: 0 6px 20px rgba(15, 23, 42, .05);
        }
        .bk-hero-photo img {
            width: 130px; height: 130px; object-fit: cover; object-position: top center;
            border-radius: 18px; border: 4px solid #fff; box-shadow: 0 6px 18px rgba(15, 23, 42, .12);
        }
        .bk-hero-body { flex: 1; min-width: 0; }
        .bk-hero-name { font-size: 24px; font-weight: 800; margin: 0 0 2px; line-height: 1.2; }
        .bk-hero-name a { color: #0f172a; text-decoration: none; }
        .bk-hero-qual { margin: 0 0 8px; color: #334155; font-weight: 600; font-size: 14px; }
        .bk-hero-chips { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 8px; }
        .bk-chip {
            display: inline-flex; align-items: center; gap: 6px; border: 1px solid #dbe5f3; background: #fff;
            color: #1e293b; border-radius: 999px; padding: 5px 12px; font-size: 13px; font-weight: 700;
        }
        .bk-chip img { width: 18px; height: 18px; }
        .bk-chip.online { background: #dcfce7; border-color: #86efac; color: #166534; }
        .bk-hero-desig { margin: 0 0 6px; color: #1e3a8a; font-weight: 700; font-size: 15px; }
        .bk-hero-line { font-size: 14px; color: #334155; margin-bottom: 3px; }
        .bk-hero-line i { color: #2958B7; width: 18px; }
        .bk-hero-line strong { color: #0f172a; margin-right: 4px; }
        .bk-hero-fees { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px; }
        .bk-fee {
            display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 10px;
            background: #fff; border: 1px solid #e5eaf3; font-size: 13px; color: #475569;
        }
        .bk-fee i { color: #64748b; }
        .bk-fee strong { color: #0f172a; font-size: 15px; }
        .bk-fee.online { background: #eff6ff; border-color: #bfdbfe; }
        .bk-fee.online i, .bk-fee.online strong { color: #1d4ed8; }
        @media (max-width: 767.98px) {
            .bk-hero { flex-direction: column; align-items: stretch; gap: 10px; padding: 14px; }
            .bk-hero-photo { text-align: center; }
            .bk-hero-photo img { width: 120px; height: 120px; }
            .bk-hero-name { font-size: 22px; }
        }

        .dt-select-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
        .dt-dropdown { position: relative; }
        .dt-dropdown .dt-label { display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 4px; }
        .dt-trigger {
            width: 100%; display: flex; align-items: center; justify-content: space-between; gap: 6px;
            padding: 10px 12px; background: #fff; border: 1.5px solid #cbd5e1; border-radius: 10px;
            font-weight: 700; font-size: 14px; color: #0f172a; cursor: pointer; transition: all .2s;
        }
        .dt-trigger i { font-size: 11px; color: #94a3b8; transition: transform .2s; }
        .dt-dropdown.is-open .dt-trigger { border-color: #1d4ed8; box-shadow: 0 4px 14px rgba(29, 78, 216, .12); }
        .dt-dropdown.is-open .dt-trigger i { transform: rotate(180deg); color: #1d4ed8; }
        .dt-menu {
            display: none; position: absolute; top: calc(100% + 6px); left: 0; min-width: 100%; z-index: 30;
            background: #fff; border: 1.5px solid #cbd5e1; border-radius: 12px; padding: 6px;
            box-shadow: 0 16px 36px rgba(15, 23, 42, .16); max-height: 260px; overflow-y: auto;
        }
        .dt-dropdown.is-open .dt-menu { display: block; }
        #dayDropdown .dt-menu { left: auto; right: 0; min-width: 160px; }
        .dt-menu .dt-option {
            display: flex; align-items: center; gap: 8px; width: 100%; text-align: left;
            padding: 8px 10px; margin: 0; border: 0; border-radius: 8px; background: transparent;
            font-size: 13px; font-weight: 600; color: #1e293b; cursor: pointer; white-space: nowrap;
        }
        .dt-menu .dt-option:hover { background: #eff6ff; color: #1d4ed8; }
        .dt-menu .dt-option.active { background: #1d4ed8; color: #fff; }
        .dt-menu .dt-option.disabled { color: #94a3b8; cursor: not-allowed; background: transparent; }
        .dt-menu .dt-option .day-date-number { min-width: 20px; font-weight: 700; }
        .dt-menu .dt-option .day-status-badge { margin-left: auto; font-size: 10px; font-weight: 600; color: #94a3b8; }
        .dt-menu .dt-option.active .day-status-badge { color: #dbeafe; }
        #slotMenuGrid { grid-template-columns: repeat(3, 1fr); gap: 8px; }
        #slotMenuGrid .dropdown-slot-item { padding: 8px 6px; justify-content: center; font-size: 12px; }
        @media (max-width: 575px) {
            .dt-select-row { gap: 6px; }
            .dt-trigger { padding: 9px 8px; font-size: 13px; }
        }
    </style>
    <script>
        const doctorId = {{ $doctor->id }};
        const getSlotsUrl = "{{ route('booking.slots', $doctor->id) }}";
        const availableYears = @json($availableYears);
        const scheduledDays = @json($scheduledDays);
        const offDates = @json($offDates);
        const clinicName = "{{ addslashes($doctor->clinic_name ?: 'Chamber') }}";
        const offlineFee = "{{ $doctor->consultation_fee ?? 0 }}";
        const onlineFee = "{{ $doctor->online_fee ?? $doctor->consultation_fee ?? 0 }}";

        // Preloaded initial data
        const initialDateStr = "{{ $initialDate->format('Y-m-d') }}";
        const initialFormattedDate = "{{ $initialDate->format('l, d/m/Y') }}";
        const preloadedSlots = @json($subdividedSlots);

        // Slots cache dictionary: { "2026-10-18": { offline: [...], online: [...] } }
        const slotsCache = {};
        if (preloadedSlots && initialDateStr) {
            slotsCache[initialDateStr] = {
                offline: preloadedSlots.offline ? (preloadedSlots.offline[initialDateStr] || []) : [],
                online: preloadedSlots.online ? (preloadedSlots.online[initialDateStr] || []) : []
            };
        }

        // Selection states
        let selectedYear = {{ $initialDate->year }};
        let selectedMonth = {{ $initialDate->month }}; // 1 to 12
        let selectedDate = "{{ $selectedOldDate ?: $initialDate->format('Y-m-d') }}";
        let selectedTime = "{{ $selectedOldTime }}";
        let currentType = "{{ $initialType }}";

        // Calendar constants
        const todayDateStr = "{{ date('Y-m-d') }}";
        const todayYear = {{ (int) date('Y') }};
        const todayMonth = {{ (int) date('n') }};

        const monthNames = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];
        const monthShortNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        const dayShortNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

        // 1. Year Selection
        function selectYear(year) {
            selectedYear = parseInt(year);

            document.querySelectorAll('.year-pill-btn').forEach(btn => {
                btn.classList.toggle('active', parseInt(btn.getAttribute('data-year')) === selectedYear);
            });
            const yearTrigger = document.getElementById('yearTriggerText');
            if (yearTrigger) yearTrigger.textContent = selectedYear;
            closeDtDropdowns();

            // If current year is selected and current month was in the past, reset to todayMonth
            if (selectedYear === todayYear && selectedMonth < todayMonth) {
                selectedMonth = todayMonth;
            }

            renderMonths();
            renderDays();
        }

        // 2. Month Rendering & Selection
        function renderMonths() {
            const scroller = document.getElementById('monthSelector');
            if (!scroller) return;

            scroller.innerHTML = '';
            const badge = document.getElementById('selectedMonthNameBadge');
            if (badge) {
                badge.textContent = `${monthNames[selectedMonth - 1]} ${selectedYear}`;
            }

            const monthTrigger = document.getElementById('monthTriggerText');
            if (monthTrigger) monthTrigger.textContent = monthShortNames[selectedMonth - 1].toUpperCase();

            // Every upcoming month across all available years (e.g. Nov 2026, Dec 2026, Jan 2027 ...)
            availableYears.forEach(yr => {
                yr = parseInt(yr);
                for (let m = 1; m <= 12; m++) {
                    if (yr < todayYear || (yr === todayYear && m < todayMonth)) continue; // past months are not shown
                    const isActive = (selectedYear === yr && selectedMonth === m);

                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = `month-pill-btn dt-option ${isActive ? 'active' : ''}`;
                    btn.setAttribute('data-month', m);
                    btn.setAttribute('data-year', yr);
                    btn.innerHTML = `${monthShortNames[m - 1]} <span class="ms-auto small opacity-75">${yr}</span>`;
                    btn.onclick = () => selectMonth(m, yr);

                    scroller.appendChild(btn);
                }
            });
        }

        function selectMonth(m, yr) {
            selectedMonth = parseInt(m);
            if (yr && parseInt(yr) !== selectedYear) {
                // Picking a month of another year also switches the Year box
                selectedYear = parseInt(yr);
                document.querySelectorAll('.year-pill-btn').forEach(btn => {
                    btn.classList.toggle('active', parseInt(btn.getAttribute('data-year')) === selectedYear);
                });
                const yearTrigger = document.getElementById('yearTriggerText');
                if (yearTrigger) yearTrigger.textContent = selectedYear;
            }
            closeDtDropdowns();
            renderMonths();
            renderDays();
        }

        // 3. Day Rendering & Selection
        function renderDays() {
            const scroller = document.getElementById('dayStripScroller');
            if (!scroller) return;

            scroller.innerHTML = '';
            const daysInMonth = new Date(selectedYear, selectedMonth, 0).getDate();

            let foundCurrentSelected = false;
            let firstSelectable = null;

            for (let d = 1; d <= daysInMonth; d++) {
                const dateStr = `${selectedYear}-${String(selectedMonth).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
                const dateObj = new Date(selectedYear, selectedMonth - 1, d);
                const weekdayIndex = dateObj.getDay();
                const weekdayName = dateObj.toLocaleDateString('en-US', { weekday: 'long' }).toLowerCase();
                const shortDay = dayShortNames[weekdayIndex];

                const isPast = (dateStr < todayDateStr);
                if (isPast) continue; // past days are not shown
                const isScheduled = scheduledDays.includes(weekdayName);
                const isOff = offDates.includes(dateStr);
                const isSelectable = (!isPast && isScheduled && !isOff);

                const card = document.createElement('button');
                card.type = 'button';
                card.setAttribute('data-date', dateStr);

                let badgeText = 'Available';
                let badgeClass = 'available';

                if (isPast) {
                    badgeText = 'Past';
                    badgeClass = 'empty';
                } else if (isOff) {
                    badgeText = 'Doctor Off';
                    badgeClass = 'empty';
                } else if (!isScheduled) {
                    badgeText = 'No Visit';
                    badgeClass = 'empty';
                }

                card.className = `day-picker-card dt-option ${isSelectable ? 'available' : 'disabled'}`;
                card.setAttribute('data-label', `${String(d).padStart(2, '0')} ${shortDay}`);

                const isToday = (dateStr === todayDateStr);
                const weekdayDisplay = isToday ? 'Today' : shortDay;

                card.innerHTML = `
                    <span class="day-date-number">${d}</span>
                    <span class="day-weekday">${weekdayDisplay}</span>
                    ${isSelectable ? '' : `<span class="day-status-badge ${badgeClass}">${badgeText}</span>`}
                `;

                if (isSelectable) {
                    // e.g. "Thursday, 26/11/2026"
                    const formattedDate = dateObj.toLocaleDateString('en-US', { weekday: 'long' }) + ', '
                        + String(d).padStart(2, '0') + '/' + String(selectedMonth).padStart(2, '0') + '/' + selectedYear;

                    card.onclick = () => selectDay(dateStr, formattedDate);

                    if (!firstSelectable) {
                        firstSelectable = { dateStr, formattedDate, card };
                    }

                    if (selectedDate === dateStr) {
                        card.classList.add('active');
                        foundCurrentSelected = true;
                    }
                }

                scroller.appendChild(card);
            }

            // If current selected date is not in this month or not selectable, select first available
            if (!foundCurrentSelected) {
                if (firstSelectable) {
                    selectDay(firstSelectable.dateStr, firstSelectable.formattedDate);
                } else {
                    // No available visiting dates in this month
                    selectedDate = null;
                    selectedTime = null;
                    document.getElementById('appointment_date').value = '';
                    document.getElementById('appointment_time').value = '';
                    const dayTrigger = document.getElementById('dayTriggerText');
                    if (dayTrigger) dayTrigger.textContent = '--';
                    document.getElementById('dynamicSlotsGridContainer').innerHTML = '';
                    document.getElementById('slotsEmptyNotice').style.display = 'block';
                    document.getElementById('slotsEmptyTitle').textContent = 'No Visiting Days in ' + monthNames[selectedMonth - 1];
                    document.getElementById('slotsEmptyDesc').textContent = 'The doctor does not have consultation hours scheduled for this month. Please try another month or year.';
                    document.getElementById('selectedSlotIndicator').style.display = 'none';
                    validateAndToggleButton();
                }
            } else {
                const activeCard = scroller.querySelector(`.day-picker-card.active`);
                const dayTrigger = document.getElementById('dayTriggerText');
                if (activeCard && dayTrigger) dayTrigger.textContent = activeCard.getAttribute('data-label');
            }
        }

        function selectDay(dateStr, formattedDate) {
            selectedDate = dateStr;

            document.querySelectorAll('.day-picker-card').forEach(c => {
                c.classList.toggle('active', c.getAttribute('data-date') === dateStr);
            });
            const activeDayCard = document.querySelector(`.day-picker-card[data-date="${dateStr}"]`);
            const dayTrigger = document.getElementById('dayTriggerText');
            if (activeDayCard && dayTrigger) dayTrigger.textContent = activeDayCard.getAttribute('data-label');
            closeDtDropdowns();

            const activeTitle = document.getElementById('activeDateTitle');
            if (activeTitle) activeTitle.textContent = formattedDate;

            loadSlots(dateStr, formattedDate);
        }

        // Year / Month / Day dropdown helpers
        function closeDtDropdowns(exceptId) {
            document.querySelectorAll('.dt-dropdown.is-open').forEach(dd => {
                if (dd.id !== exceptId) dd.classList.remove('is-open');
            });
        }

        function toggleDtDropdown(e, id) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            closeDtDropdowns(id);
            const slotDd = document.getElementById('customSlotDropdown');
            if (slotDd) slotDd.classList.remove('is-open');
            const dd = document.getElementById(id);
            if (dd) {
                dd.classList.toggle('is-open');
                // Scroll the list so the selected option is visible
                const menu = dd.querySelector('.dt-menu');
                const active = menu ? menu.querySelector('.dt-option.active') : null;
                if (menu && active) menu.scrollTop = active.offsetTop - 6;
            }
        }

        // Time range text for the "Select available time" box, e.g. "04:00 PM to 10:40 PM"
        let currentSlotRange = '';

        // 4. AJAX Slot Fetching & UI Rendering
        function loadSlots(dateStr, formattedDate) {
            const spinner = document.getElementById('slotsLoadingSpinner');
            const container = document.getElementById('dynamicSlotsGridContainer');
            const emptyNotice = document.getElementById('slotsEmptyNotice');

            if (slotsCache[dateStr]) {
                renderSlotsUI(slotsCache[dateStr], formattedDate, 'success');
                return;
            }

            if (spinner) spinner.style.display = 'block';
            if (container) container.innerHTML = '';
            if (emptyNotice) emptyNotice.style.display = 'none';

            fetch(`${getSlotsUrl}?date=${dateStr}&type=${currentType}`)
                .then(response => {
                    if (!response.ok) throw new Error('Network error');
                    return response.json();
                })
                .then(data => {
                    if (spinner) spinner.style.display = 'none';
                    if (data.status === 'success') {
                        slotsCache[dateStr] = data.slots;
                        renderSlotsUI(data.slots, formattedDate, 'success');
                    } else {
                        renderSlotsUI(null, formattedDate, data.status, data.message);
                    }
                })
                .catch(err => {
                    if (spinner) spinner.style.display = 'none';
                    renderSlotsUI(null, formattedDate, 'error', 'Unable to fetch doctor slots. Please check your internet connection.');
                });
        }

        function getSlotPeriod(timeKey) {
            const hour = parseInt(timeKey.split(':')[0], 10);
            if (hour < 12) return 'morning';
            if (hour < 17) return 'afternoon';
            return 'evening';
        }

        function renderSlotsUI(slots, formattedDate, status = 'success', message = '') {
            const container = document.getElementById('dynamicSlotsGridContainer');
            const emptyNotice = document.getElementById('slotsEmptyNotice');
            const emptyTitle = document.getElementById('slotsEmptyTitle');
            const emptyDesc = document.getElementById('slotsEmptyDesc');

            if (!slots || status !== 'success') {
                if (container) container.innerHTML = '';
                if (emptyNotice) {
                    emptyNotice.style.display = 'block';
                    emptyTitle.textContent = 'No Available Slots';
                    emptyDesc.textContent = message || 'Doctor is unavailable on this date. Please pick another date.';
                }
                resetSelectedSlot();
                return;
            }

            const typeSlots = currentType === 'online' ? (slots.online || []) : (slots.offline || []);

            if (typeSlots.length === 0) {
                if (container) container.innerHTML = '';
                if (emptyNotice) {
                    emptyNotice.style.display = 'block';
                    if (currentType === 'online') {
                        emptyTitle.textContent = 'No Online Video Slots on This Day';
                        emptyDesc.textContent = 'Doctor has not opened video slots on this day. You can select In-Clinic Visit or choose another date.';
                    } else {
                        emptyTitle.textContent = 'No In-Clinic Chamber Slots on This Day';
                        emptyDesc.textContent = 'Doctor is unavailable for chamber consultation on this date. Please choose another date.';
                    }
                }
                resetSelectedSlot();
                return;
            }

            const availableSlots = typeSlots.filter(s => !s.is_booked);
            const bookedSlots = typeSlots.filter(s => s.is_booked);

            if (availableSlots.length === 0) {
                if (container) container.innerHTML = '';
                if (emptyNotice) {
                    emptyNotice.style.display = 'block';
                    emptyTitle.textContent = 'All Slots Booked on This Day';
                    emptyDesc.textContent = 'All time slots for this date are already booked or have passed. Please pick another date above.';
                }
                resetSelectedSlot();
                return;
            }

            if (emptyNotice) emptyNotice.style.display = 'none';

            let morningCount = 0;
            let afternoonCount = 0;
            let eveningCount = 0;

            let slotsHtml = '';
            let selectedDisplay = '';

            typeSlots.forEach(slot => {
                const period = getSlotPeriod(slot.time_key);
                if (!slot.is_booked) {
                    if (period === 'morning') morningCount++;
                    else if (period === 'afternoon') afternoonCount++;
                    else eveningCount++;
                }

                if (slot.is_booked) {
                    slotsHtml += `
                        <div class="dropdown-slot-item booked" data-period="${period}" title="Booked or time passed">
                            <span>${slot.display}</span>
                        </div>
                    `;
                } else {
                    const isSlotActive = (selectedDate === document.getElementById('appointment_date').value && selectedTime === slot.time_key);
                    if (isSlotActive) {
                        selectedDisplay = slot.display;
                    }
                    slotsHtml += `
                        <button type="button" 
                                class="dropdown-slot-item available ${isSlotActive ? 'active' : ''}" 
                                data-period="${period}"
                                data-time="${slot.time_key}"
                                onclick="selectCustomSlot('${selectedDate}', '${slot.time_key}', '${formattedDate}', '${slot.display}')">
                            <span>${slot.display}</span>
                        </button>
                    `;
                }
            });

            const hasActiveSelection = !!selectedDisplay;
            currentSlotRange = availableSlots.length > 1
                ? `${availableSlots[0].display} to ${availableSlots[availableSlots.length - 1].display}`
                : availableSlots[0].display;

            let html = `
                <div class="custom-slot-dropdown" id="customSlotDropdown">
                    <button type="button" 
                            class="slot-dropdown-trigger ${hasActiveSelection ? 'has-selected' : ''}" 
                            id="slotDropdownTrigger" 
                            onclick="toggleSlotDropdown(event)">
                        <div class="trigger-left">
                            <div class="trigger-icon" id="triggerIcon">
                                <i class="${hasActiveSelection ? 'fas fa-check text-primary' : 'far fa-clock'}"></i>
                            </div>
                            <div class="trigger-text">
                                <span class="trigger-label">${hasActiveSelection ? 'Selected Time' : 'Select available time'}</span>
                                <span class="trigger-selected" id="triggerSelectedText">
                                    ${hasActiveSelection ? selectedDisplay : currentSlotRange}
                                </span>
                            </div>
                        </div>
                        <div class="trigger-right">
                            <span class="slots-count-chip" id="triggerSlotsCount">
                                ${hasActiveSelection ? 'Selected' : availableSlots.length + ' Available'}
                            </span>
                            <i class="fas fa-chevron-down trigger-chevron"></i>
                        </div>
                    </button>

                    <div class="slot-dropdown-menu" id="slotDropdownMenu">
                        ${availableSlots.length > 3 ? `
                        <div class="slot-filter-tabs">
                            <button type="button" class="slot-filter-btn active" onclick="filterSlotPeriod(event, 'all')">
                                All (${availableSlots.length})
                            </button>
                            ${morningCount > 0 ? `<button type="button" class="slot-filter-btn" onclick="filterSlotPeriod(event, 'morning')">🌅 Morning (${morningCount})</button>` : ''}
                            ${afternoonCount > 0 ? `<button type="button" class="slot-filter-btn" onclick="filterSlotPeriod(event, 'afternoon')">☀️ Afternoon (${afternoonCount})</button>` : ''}
                            ${eveningCount > 0 ? `<button type="button" class="slot-filter-btn" onclick="filterSlotPeriod(event, 'evening')">🌙 Evening (${eveningCount})</button>` : ''}
                        </div>
                        ` : ''}

                        <div class="slot-menu-grid" id="slotMenuGrid">
                            ${slotsHtml}
                        </div>

                        <div class="slot-menu-footer">
                            <span><i class="fas fa-info-circle text-primary me-1"></i> Click on any available time</span>
                        </div>
                    </div>
                </div>
            `;

            if (container) {
                container.innerHTML = html;
            }

            if (hasActiveSelection) {
                updateSelectedSlotUI(selectedDate, selectedTime, formattedDate, selectedDisplay);
            } else {
                resetSelectedSlot();
            }
        }

        function toggleSlotDropdown(e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            closeDtDropdowns();
            const dd = document.getElementById('customSlotDropdown');
            if (dd) {
                dd.classList.toggle('is-open');
            }
        }

        function filterSlotPeriod(e, period) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            document.querySelectorAll('.slot-filter-btn').forEach(btn => btn.classList.remove('active'));
            if (e && e.currentTarget) e.currentTarget.classList.add('active');

            const items = document.querySelectorAll('.dropdown-slot-item');
            items.forEach(item => {
                if (period === 'all' || item.getAttribute('data-period') === period) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        function selectCustomSlot(dateKey, timeVal, formattedDate, displayTime) {
            selectedDate = dateKey;
            selectedTime = timeVal;
            document.getElementById('appointment_date').value = dateKey;
            document.getElementById('appointment_time').value = timeVal;

            document.querySelectorAll('.dropdown-slot-item').forEach(item => {
                item.classList.toggle('active', item.getAttribute('data-time') === timeVal);
            });

            const trigger = document.getElementById('slotDropdownTrigger');
            const triggerText = document.getElementById('triggerSelectedText');
            const triggerIcon = document.getElementById('triggerIcon');
            const triggerChip = document.getElementById('triggerSlotsCount');

            if (trigger) trigger.classList.add('has-selected');
            if (triggerText) triggerText.innerHTML = `${displayTime} <i class="fas fa-check-circle text-primary ms-1"></i>`;
            if (triggerIcon) triggerIcon.innerHTML = `<i class="fas fa-check text-primary"></i>`;
            if (triggerChip) triggerChip.textContent = 'Selected';

            const dd = document.getElementById('customSlotDropdown');
            if (dd) dd.classList.remove('is-open');

            updateSelectedSlotUI(dateKey, timeVal, formattedDate, displayTime);
        }

        function resetSelectedSlot() {
            selectedTime = null;
            document.getElementById('appointment_time').value = '';

            const trigger = document.getElementById('slotDropdownTrigger');
            const triggerText = document.getElementById('triggerSelectedText');
            const triggerIcon = document.getElementById('triggerIcon');
            const triggerChip = document.getElementById('triggerSlotsCount');

            if (trigger) trigger.classList.remove('has-selected');
            if (triggerText) triggerText.textContent = currentSlotRange || 'Click to select a time slot';
            if (triggerIcon) triggerIcon.innerHTML = `<i class="far fa-clock"></i>`;

            const banner = document.getElementById('selectedSlotIndicator');
            if (banner) banner.style.display = 'none';
            const summaryTime = document.getElementById('summaryTime');
            if (summaryTime) summaryTime.textContent = '-- : --';
            validateAndToggleButton();
        }

        function updateSelectedSlotUI(dateKey, timeVal, formattedDate, displayTime) {
            const banner = document.getElementById('selectedSlotIndicator');
            if (banner) {
                banner.style.display = 'flex';
                document.getElementById('selectedSlotDateDisplay').textContent = formattedDate;
                document.getElementById('selectedSlotTimeDisplay').textContent = displayTime;
                
                const subtitle = document.getElementById('selectedSlotModeSubtitle');
                if (subtitle) {
                    if (currentType === 'online') {
                        subtitle.textContent = 'Online Video Consultation • HD Meeting Link';
                    } else {
                        subtitle.textContent = `In-Clinic Chamber Visit at ${clinicName}`;
                    }
                }
            }

            const summaryDate = document.getElementById('summaryDate');
            const summaryTime = document.getElementById('summaryTime');
            if (summaryDate) summaryDate.textContent = formattedDate;
            if (summaryTime) summaryTime.textContent = displayTime;

            validateAndToggleButton();
        }

        function selectSlot(element, dateKey, timeVal, formattedDate, displayTime) {
            selectCustomSlot(dateKey, timeVal, formattedDate, displayTime);
        }

        function validateAndToggleButton() {
            const btn = document.getElementById('btnConfirmBooking');
            const btnText = document.getElementById('submitBtnText');
            const reminder = document.getElementById('paymentSlotReminder');

            if (!selectedDate || !selectedTime) {
                if (btn) btn.disabled = true;
                if (btnText) btnText.innerHTML = '<i class="far fa-clock me-1"></i> Please Select a Time Slot';
                if (reminder) {
                    reminder.className = 'alert alert-warning py-2 px-3 small mb-3 d-flex align-items-center';
                    reminder.innerHTML = '<i class="fas fa-exclamation-triangle me-2 text-warning fs-5 flex-shrink-0"></i><div style="font-size: 11px; line-height: 1.4;"><strong>Please select a time slot first:</strong> Confirm appointment time from schedule before sending money to this number.</div>';
                }
                return;
            }

            if (btn) btn.disabled = false;
            if (reminder) {
                reminder.className = 'alert alert-success py-2 px-3 small mb-3 d-flex align-items-center';
                reminder.innerHTML = '<i class="fas fa-check-circle me-2 text-success fs-5 flex-shrink-0"></i><div style="font-size: 11px; line-height: 1.4;"><strong>Slot Selected!</strong> Send fee to the number above and enter TrxID & Sender Number.</div>';
            }
            if (btnText) {
                if (currentType === 'online') {
                    btnText.innerHTML = `<i class="fas fa-check-circle me-1"></i> Confirm Booking (৳${parseFloat(onlineFee).toLocaleString()})`;
                } else {
                    btnText.innerHTML = '<i class="fas fa-calendar-check me-1"></i> Confirm Booking (Pay at Chamber)';
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const typeOffline = document.getElementById('type_offline');
            const typeOnline = document.getElementById('type_online');
            const labelOffline = document.getElementById('label_type_offline');
            const labelOnline = document.getElementById('label_type_online');

            const summaryApptType = document.getElementById('summaryApptType');
            const summaryFee = document.getElementById('summaryFee');
            const summaryTotal = document.getElementById('summaryTotal');
            const summaryLocation = document.getElementById('summaryLocation');
            const paymentModeNote = document.getElementById('paymentModeNote');
            const onlinePaymentOptions = document.getElementById('onlinePaymentOptions');
            const chamberSelectorWrapper = document.getElementById('chamberSelectorWrapper');

            function getSelectedChamberFee() {
                const checked = document.querySelector('input[name="clinic_index"]:checked') || document.querySelector('input[name="clinic_index"][type="hidden"]');
                if (checked && checked.dataset.fee) {
                    return parseFloat(checked.dataset.fee) || 0;
                }
                return parseFloat(offlineFee) || 0;
            }

            function getSelectedChamberName() {
                const checked = document.querySelector('input[name="clinic_index"]:checked') || document.querySelector('input[name="clinic_index"][type="hidden"]');
                if (checked && checked.dataset.clinicName) {
                    return checked.dataset.clinicName;
                }
                return clinicName;
            }

            function applyConsultationType(isOnline) {
                currentType = isOnline ? 'online' : 'offline';

                // Toggle card active states
                if (labelOffline) labelOffline.classList.toggle('is-active', !isOnline);
                if (labelOnline) labelOnline.classList.toggle('is-active', isOnline);
                if (chamberSelectorWrapper) chamberSelectorWrapper.style.display = isOnline ? 'none' : 'block';

                // Update summary values
                if (isOnline) {
                    if (summaryApptType) {
                        summaryApptType.textContent = 'Video Consultation';
                        summaryApptType.className = 'row-value badge bg-primary text-white px-2 py-1';
                    }
                    if (summaryLocation) summaryLocation.textContent = 'Online (Video Call)';
                    if (summaryFee) summaryFee.textContent = '৳' + parseFloat(onlineFee).toLocaleString();
                    if (summaryTotal) summaryTotal.textContent = '৳' + parseFloat(onlineFee).toLocaleString();
                    if (paymentModeNote) paymentModeNote.style.display = 'none';
                    if (onlinePaymentOptions) onlinePaymentOptions.style.display = 'block';
                    setManualFieldsRequired(isManualPaymentSelected());
                } else {
                    const curOfflineFee = getSelectedChamberFee();
                    const curChamberName = getSelectedChamberName();
                    if (summaryApptType) {
                        summaryApptType.textContent = 'In-Clinic Visit';
                        summaryApptType.className = 'row-value badge bg-primary text-white px-2 py-1';
                    }
                    if (summaryLocation) summaryLocation.textContent = curChamberName;
                    if (summaryFee) summaryFee.textContent = '৳' + curOfflineFee.toLocaleString();
                    if (summaryTotal) summaryTotal.textContent = '৳' + curOfflineFee.toLocaleString();
                    const chamberPriceSpan = document.getElementById('chamber-mode-price');
                    if (chamberPriceSpan) chamberPriceSpan.textContent = '৳' + curOfflineFee.toLocaleString();
                    if (paymentModeNote) paymentModeNote.style.display = 'flex';
                    if (onlinePaymentOptions) onlinePaymentOptions.style.display = 'none';
                    const sNum = document.getElementById('sender_number');
                    const tId = document.getElementById('transaction_id');
                    if (sNum) sNum.removeAttribute('required');
                    if (tId) tId.removeAttribute('required');
                }

                // Reset selected slot when mode is switched
                const prevDate = selectedDate || document.getElementById('appointment_date').value;
                document.querySelectorAll('.slot-pill').forEach(el => el.classList.remove('selected'));
                const slotSelectEl = document.getElementById('slotSelectDropdown');
                if (slotSelectEl) slotSelectEl.value = '';
                document.getElementById('appointment_time').value = '';
                selectedTime = null;

                const banner = document.getElementById('selectedSlotIndicator');
                if (banner) banner.style.display = 'none';
                const summaryDate = document.getElementById('summaryDate');
                const summaryTime = document.getElementById('summaryTime');
                if (summaryDate) summaryDate.textContent = 'Select a slot';
                if (summaryTime) summaryTime.textContent = '-- : --';

                validateAndToggleButton();

                // Re-render slots for selected date with new consultation type
                if (prevDate) {
                    const activeTitle = document.getElementById('activeDateTitle')?.textContent || '';
                    loadSlots(prevDate, activeTitle);
                }
            }

            if (typeOffline) {
                typeOffline.addEventListener('change', () => applyConsultationType(false));
            }
            if (typeOnline) {
                typeOnline.addEventListener('change', () => applyConsultationType(true));
            }

            document.querySelectorAll('input[name="clinic_index"]').forEach(el => {
                el.addEventListener('change', function () {
                    if (currentType === 'offline') {
                        const fee = getSelectedChamberFee();
                        const name = getSelectedChamberName();
                        if (summaryLocation) summaryLocation.textContent = name;
                        if (summaryFee) summaryFee.textContent = '৳' + fee.toLocaleString();
                        if (summaryTotal) summaryTotal.textContent = '৳' + fee.toLocaleString();
                        const chamberPriceSpan = document.getElementById('chamber-mode-price');
                        if (chamberPriceSpan) chamberPriceSpan.textContent = '৳' + fee.toLocaleString();
                    }
                });
            });

            // Scroller navigation buttons
            const dayScroller = document.getElementById('dayStripScroller');
            const prevDayBtn = document.getElementById('prevDayBtn');
            const nextDayBtn = document.getElementById('nextDayBtn');

            if (prevDayBtn && dayScroller) {
                prevDayBtn.addEventListener('click', () => {
                    dayScroller.scrollBy({ left: -220, behavior: 'smooth' });
                });
            }
            if (nextDayBtn && dayScroller) {
                nextDayBtn.addEventListener('click', () => {
                    dayScroller.scrollBy({ left: 220, behavior: 'smooth' });
                });
            }

            // Form Submit Safety Verification
            const form = document.getElementById('mainBookingForm');
            if (form) {
                form.addEventListener('submit', function (e) {
                    if (!selectedDate || !selectedTime) {
                        e.preventDefault();
                        alert('Please select an appointment date and time slot.');
                        return false;
                    }

                    if (currentType === 'online' && isManualPaymentSelected()) {
                        const sNum = document.getElementById('sender_number')?.value.trim();
                        const tId = document.getElementById('transaction_id')?.value.trim();
                        if (!sNum || !tId) {
                            e.preventDefault();
                            alert('For online consultation, please provide both Sender Number and Transaction ID (TrxID).');
                            return false;
                        }
                    }
                });
            }

            // Global Manual Payment Helpers
            window.isManualPaymentSelected = function() {
                const checked = document.querySelector('input[name="payment_method"]:checked');
                return !checked || checked.value !== 'sslcommerz';
            };

            window.setManualFieldsRequired = function(required) {
                ['sender_number', 'transaction_id'].forEach(id => {
                    const el = document.getElementById(id);
                    if (!el) return;
                    if (required) el.setAttribute('required', 'required');
                    else el.removeAttribute('required');
                });
            };

            window.switchManualPayment = function(method) {
                document.querySelectorAll('.payment-radio-btn').forEach(btn => btn.classList.remove('active'));
                const manualFields = document.getElementById('manualPaymentFields');
                const sslczBox = document.getElementById('sslczInfoBox');
                const isSslcz = method === 'sslcommerz';
                if (manualFields) manualFields.style.display = isSslcz ? 'none' : 'block';
                if (sslczBox) sslczBox.style.display = isSslcz ? 'block' : 'none';
                setManualFieldsRequired(!isSslcz);
                if (isSslcz) {
                    const btn = document.getElementById('btnRadioSslcommerz');
                    if (btn) btn.classList.add('active');
                    return;
                }
                const bkashBox = document.getElementById('bkashInfoBox');
                const nagadBox = document.getElementById('nagadInfoBox');
                const rocketBox = document.getElementById('rocketInfoBox');

                if (bkashBox) bkashBox.style.display = 'none';
                if (nagadBox) nagadBox.style.display = 'none';
                if (rocketBox) rocketBox.style.display = 'none';

                if (method === 'bkash') {
                    const btn = document.getElementById('btnRadioBkash');
                    if (btn) btn.classList.add('active');
                    if (bkashBox) bkashBox.style.display = 'block';
                } else if (method === 'nagad') {
                    const btn = document.getElementById('btnRadioNagad');
                    if (btn) btn.classList.add('active');
                    if (nagadBox) nagadBox.style.display = 'block';
                } else if (method === 'rocket') {
                    const btn = document.getElementById('btnRadioRocket');
                    if (btn) btn.classList.add('active');
                    if (rocketBox) rocketBox.style.display = 'block';
                }
            };

            window.copyTextToClipboard = function(text, btn) {
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(text).then(() => showCopied(btn));
                } else {
                    const textArea = document.createElement('textarea');
                    textArea.value = text;
                    textArea.style.position = 'fixed';
                    textArea.style.left = '-999999px';
                    document.body.appendChild(textArea);
                    textArea.focus();
                    textArea.select();
                    try {
                        document.execCommand('copy');
                        showCopied(btn);
                    } catch (err) {}
                    document.body.removeChild(textArea);
                }
            };

            function showCopied(btn) {
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check text-success"></i> <span class="small text-success">Copied</span>';
                setTimeout(() => {
                    btn.innerHTML = originalText;
                }, 2000);
            }

            // Preview report file attachments
            const reportInput = document.getElementById('report_files');
            const previewList = document.getElementById('file_preview_list');
            if (reportInput && previewList) {
                reportInput.addEventListener('change', function () {
                    previewList.innerHTML = '';
                    if (this.files && this.files.length > 0) {
                        Array.from(this.files).forEach(file => {
                            const sizeKb = (file.size / 1024).toFixed(1);
                            const isPdf = file.name.toLowerCase().endsWith('.pdf');
                            const iconClass = isPdf ? 'fa-file-pdf text-danger' : 'fa-file-image text-primary';

                            const chip = document.createElement('span');
                            chip.className = 'file-preview-chip';
                            chip.innerHTML = `<i class="fas ${iconClass}"></i> ${file.name} <small class="text-muted">(${sizeKb} KB)</small>`;
                            previewList.appendChild(chip);
                        });
                    }
                });
            }

            // Close custom slot dropdown when clicking outside
            document.addEventListener('click', function (e) {
                const dd = document.getElementById('customSlotDropdown');
                if (dd && !dd.contains(e.target)) {
                    dd.classList.remove('is-open');
                }
                if (!e.target.closest('.dt-dropdown')) {
                    closeDtDropdowns();
                }
            });

            // Initial Date Picker render
            renderMonths();
            renderDays();
            loadSlots(initialDateStr, initialFormattedDate);
        });
    </script>

@endsection

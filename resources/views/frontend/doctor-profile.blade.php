@extends('layouts.app')

@section('title', 'Doctor Profile - Doccure')

@section('content')
    <!-- Custom Premium Styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/doctor-profile-modern.css') }}">

    <div class="content" style="background: #f8fafc; min-height: 100vh;">
        <div class="container">

            <!-- Doctor Header (Hero-like) -->
            <div class="doctor-profile-header">
                <div class="row">
                    <div class="col-md-2 text-center text-md-start">
                        <img src="{{ $doctor->profile_image ? asset($doctor->profile_image) : asset('assets/img/doctors/doctor-thumb-02.jpg') }}"
                            class="doc-img" alt="User Image">
                    </div>
                    <div class="col-md-6">
                        <h4 class="doc-name">Dr. {{ $doctor->user->name }}</h4>
                        <p class="doc-speciality">{{ $doctor->qualifications }}</p>
                        <p class="doc-department">
                            @if($doctor->speciality && $doctor->speciality->image)
                                <img src="{{ asset($doctor->speciality->image) }}" alt="Speciality">
                            @endif
                            {{ $doctor->speciality->name ?? 'General' }}
                        </p>

                        <div class="rating mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= $doctor->average_rating ? 'filled' : '' }}"
                                    style="color: {{ $i <= $doctor->average_rating ? '#fbbf24' : '#e5e7eb' }}"></i>
                            @endfor
                            <span class="d-inline-block average-rating ms-2 text-muted">({{ $doctor->review_count }}
                                Reviews)</span>
                        </div>

                        <div class="mb-2">
                            @php
                                $isOnline = optional($doctor->user)->isOnline();
                                $lastSeen = optional($doctor->user?->last_seen_at)->diffForHumans();
                            @endphp
                            <span class="badge"
                                style="background: {{ $isOnline ? '#dcfce7' : '#f3f4f6' }}; color: {{ $isOnline ? '#166534' : '#4b5563' }}; padding: 8px 12px; border-radius: 999px; font-weight: 600;">
                                <i class="fas fa-circle"
                                    style="font-size: 9px; margin-right: 6px; color: {{ $isOnline ? '#22c55e' : '#9ca3af' }};"></i>
                                {{ $isOnline ? 'Online Now' : 'Offline' }}
                                @if(!$isOnline && $lastSeen)
                                    <small class="ms-1">(Last seen {{ $lastSeen }})</small>
                                @endif
                            </span>
                        </div>

                        <div class="doctor-stats">
                            <div class="doctor-stat-item">
                                <i class="far fa-comment"></i> {{ $doctor->review_count }} Feedback
                            </div>
                            <div class="doctor-stat-item">
                                <i class="fas fa-map-marker-alt"></i> {{ $doctor->clinic_city }}
                            </div>
                            <div class="doctor-stat-item">
                                <i class="far fa-money-bill-alt"></i>
                                {{ $doctor->pricing === 'free' ? 'Free' : ($doctor->pricing === 'custom_price' ? '$' . $doctor->custom_price : 'N/A') }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex flex-column h-100 justify-content-center">
                            <div class="action-buttons mb-3">
                                <a href="javascript:void(0)" class="action-btn" title="Detailed Profile"><i
                                        class="far fa-user"></i> Profile</a>
                                <a href="javascript:void(0)" class="action-btn" title="Voice Call" data-bs-toggle="modal"
                                    data-bs-target="#voice_call"><i class="fas fa-phone-alt"></i> Call</a>
                                <a href="javascript:void(0)" class="action-btn" title="Video Call" data-bs-toggle="modal"
                                    data-bs-target="#video_call"><i class="fas fa-video"></i> Video</a>
                                <a href="{{ route('chat') }}" class="action-btn" title="Chat"><i
                                        class="far fa-comment-dots"></i> Chat</a>
                            </div>
                            <a href="{{ route('booking', $doctor->id) }}" class="booking-btn">
                                Book Appointment <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Main Content (Left) -->
                <div class="col-lg-8">

                    <!-- About Me -->
                    <div class="profile-section">
                        <h3 class="profile-section-title"><i class="fas fa-user-md"></i> About Me</h3>
                        <div class="about-content">
                            <p class="text-muted mb-0">{{ $doctor->bio ?? 'No biography available.' }}</p>
                        </div>
                    </div>

                    <!-- Education & Experience (Placeholder/Static for now) -->
                    <div class="profile-section">
                        <h3 class="profile-section-title"><i class="fas fa-graduation-cap"></i> Education & Experience</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="mb-3 text-dark font-weight-bold">Education</h5>
                                <ul class="experience-list list-unstyled experience-box">
                                    <li>
                                        <span class="exp-year">2008 - 2013</span>
                                        <h4 class="exp-title">MBBS</h4>
                                        <span class="exp-place">Dhaka Medical College</span>
                                    </li>
                                    <li>
                                        <span class="exp-year">2013 - 2015</span>
                                        <h4 class="exp-title">FCPS (Medicine)</h4>
                                        <span class="exp-place">Bangladesh College of Physicians and Surgeons</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5 class="mb-3 text-dark font-weight-bold">Experience</h5>
                                <ul class="experience-list list-unstyled experience-box">
                                    <li>
                                        <span class="exp-year">2016 - Present</span>
                                        <h4 class="exp-title">Senior Consultant</h4>
                                        <span class="exp-place">Square Hospital, Dhaka</span>
                                    </li>
                                    <li>
                                        <span class="exp-year">2014 - 2016</span>
                                        <h4 class="exp-title">Medical Officer</h4>
                                        <span class="exp-place">Dhaka Medical College Hospital</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Locations -->
                    <div class="profile-section">
                        <h3 class="profile-section-title"><i class="fas fa-map-marked-alt"></i> Locations</h3>
                        <div class="location-list">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="clinic-content">
                                        <p class="doc-speciality mb-2">{{ $doctor->qualifications }}</p>
                                        <div class="clinic-details mb-0">
                                            @forelse($doctor->clinic_locations as $location)
                                                <h4 class="clinic-name text-primary font-weight-bold mb-2">
                                                    {{ $location['name'] ?? 'Main Clinic' }}
                                                </h4>
                                                <p class="mb-2"><i class="fas fa-map-marker-alt text-danger me-2"></i>
                                                    {{ $location['address'] ?? 'Address not available' }}{{ $doctor->clinic_city ? ', ' . $doctor->clinic_city : '' }}</p>
                                            @empty
                                                <h4 class="clinic-name text-primary font-weight-bold mb-2">Main Clinic</h4>
                                                <p class="mb-2"><i class="fas fa-map-marker-alt text-danger me-2"></i>
                                                    {{ $doctor->primary_clinic_address ?? 'Address not available' }}{{ $doctor->clinic_city ? ', ' . $doctor->clinic_city : '' }}</p>
                                            @endforelse
                                            <p class="mb-0 text-primary" style="cursor: pointer;"><i
                                                    class="fas fa-directions me-2"></i> Get Directions</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="clinic-timing">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-muted">Consultation Fee</span>
                                            <span
                                                class="font-weight-bold text-dark">{{ $doctor->pricing === 'free' ? 'Free' : ($doctor->pricing === 'custom_price' ? '$' . $doctor->custom_price : 'N/A') }}</span>
                                        </div>
                                        <div class="consult-price">

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reviews -->
                    <div class="profile-section">
                        <h3 class="profile-section-title">
                            <span><i class="fas fa-star"></i> Reviews</span>
                            <a href="#" class="text-primary" style="font-size: 14px; font-weight: 500;">Write a Review</a>
                        </h3>

                        <div class="reviews-list">
                            @forelse($doctor->reviews as $review)
                                <div class="review-item border-bottom pb-3 mb-3">
                                    <div class="d-flex gap-3">
                                        <img class="avatar avatar-sm rounded-circle" alt="User Image"
                                            src="{{ optional($review->patient->user)->profile_image ? asset($review->patient->user->profile_image) : asset('assets/img/patients/patient.jpg') }}"
                                            style="width: 40px; height: 40px; object-fit: cover;">
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <h6 class="mb-0 font-weight-bold">
                                                    {{ $review->patient->user->name ?? 'Patient' }}</h6>
                                                <span class="text-muted small">{{ $review->created_at->diffForHumans() }}</span>
                                            </div>
                                            <div class="rating mb-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star"
                                                        style="font-size: 10px; color: {{ $i <= $review->rating ? '#fbbf24' : '#e5e7eb' }}"></i>
                                                @endfor
                                            </div>
                                            <p class="text-muted mb-0 small">{{ $review->comment }}</p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4">
                                    <p class="text-muted">No reviews yet.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                </div>

                <!-- Sidebar (Right) -->
                <div class="col-lg-4">
                    <div class="theiaStickySidebar">

                        <!-- Business Hours -->
                        <div class="sidebar-widget">
                            <h4 class="sidebar-title">Business Hours</h4>
                            <ul class="hours-list">
                                @forelse($doctor->schedules as $schedule)
                                    <li>
                                        <span class="day">{{ ucfirst($schedule->day) }}</span>
                                        <span class="time">{{ \Carbon\Carbon::parse($schedule->start_time)->format('g:i A') }} -
                                            {{ \Carbon\Carbon::parse($schedule->end_time)->format('g:i A') }}</span>
                                    </li>
                                @empty
                                    <li><span class="text-muted">No schedules available</span></li>
                                @endforelse
                            </ul>
                        </div>

                        <!-- Services -->
                        <div class="sidebar-widget">
                            <h4 class="sidebar-title">Services</h4>
                            <div class="service-tags">
                                @if(isset($doctor->services) && !empty($doctor->services))
                                    @foreach(json_decode($doctor->services) ?? [] as $service)
                                        <span class="service-tag">{{ $service }}</span>
                                    @endforeach
                                @else
                                    <span class="service-tag">General Consultation</span>
                                    <span class="service-tag">Online Support</span>
                                    <span class="service-tag">Medical Checkup</span>
                                    <span class="service-tag">Health Advice</span>
                                @endif
                            </div>
                        </div>

                        <!-- Share Profile -->
                        <div class="sidebar-widget">
                            <h4 class="sidebar-title">Share Profile</h4>
                            <div class="d-flex gap-2">
                                <a href="#" class="btn btn-outline-primary btn-sm flex-fill"><i
                                        class="fab fa-facebook-f"></i></a>
                                <a href="#" class="btn btn-outline-info btn-sm flex-fill"><i class="fab fa-twitter"></i></a>
                                <a href="#" class="btn btn-outline-secondary btn-sm flex-fill"><i
                                        class="fab fa-linkedin-in"></i></a>
                                <a href="#" class="btn btn-outline-success btn-sm flex-fill"><i
                                        class="fab fa-whatsapp"></i></a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/plugins/theia-sticky-sidebar/ResizeSensor.js') }}"></script>
    <script src="{{ asset('assets/plugins/theia-sticky-sidebar/theia-sticky-sidebar.js') }}"></script>
    <script>
        $(document).ready(function () {
            if ($(window).width() > 991) {
                $('.theiaStickySidebar').theiaStickySidebar({
                    additionalMarginTop: 100
                });
            }
        });
    </script>
@endpush

@extends('layouts.app')

@section('title', 'Doctor Profile - Doccure')

@push('styles')
<style>
    .doctor-page {
        background: linear-gradient(180deg, #f8fbff 0%, #f5f7fb 100%);
        min-height: 100vh;
        padding: 120px 0 60px;
    }

    .doctor-shell {
        border-radius: 20px;
        border: 1px solid #e5eaf3;
        background: #fff;
        box-shadow: 0 16px 45px rgba(15, 23, 42, 0.08);
        overflow: hidden;
    }

    .doctor-hero {
        padding: 28px;
        background: radial-gradient(circle at top right, #dbeafe 0%, #eff6ff 38%, #fff 100%);
        border-bottom: 1px solid #e5eaf3;
    }

    .doctor-avatar {
        width: 180px;
        height: 180px;
        border-radius: 20px;
        object-fit: cover;
        border: 4px solid #fff;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.16);
    }

    .doctor-name {
        margin: 0 0 6px;
        font-size: 32px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
    }

    .doctor-subtitle {
        margin: 0 0 10px;
        color: #334155;
        font-weight: 600;
    }

    .doctor-chip-row {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 14px;
    }

    .doctor-chip {
        border: 1px solid #dbe5f3;
        background: #fff;
        color: #1e293b;
        border-radius: 999px;
        padding: 6px 12px;
        font-size: 13px;
        font-weight: 700;
    }

    .doctor-chip.online {
        background: #dcfce7;
        border-color: #86efac;
        color: #166534;
    }

    .doctor-rating {
        color: #f59e0b;
        font-size: 14px;
        font-weight: 700;
    }

    .doctor-rating .muted {
        color: #64748b;
        font-weight: 600;
    }

    .doctor-metrics {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .metric {
        padding: 11px 12px;
        border-radius: 12px;
        border: 1px solid #e5eaf3;
        background: #fff;
        font-size: 13px;
        color: #475569;
    }

    .metric strong {
        display: block;
        font-size: 16px;
        color: #0f172a;
        margin-top: 2px;
    }

    .doctor-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 16px;
    }

    .doctor-actions .btn {
        border-radius: 10px;
        font-weight: 700;
        padding: 7px 12px;
        font-size: 14px;
        line-height: 1.2;
        min-height: 40px;
        box-shadow: none !important;
    }

    .doctor-content {
        padding: 26px;
    }

    .doctor-section {
        border: 1px solid #e5eaf3;
        border-radius: 16px;
        background: #fff;
        padding: 20px;
        margin-bottom: 16px;
    }

    .doctor-section h5 {
        margin: 0 0 12px;
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px 14px;
    }

    .detail-item {
        border: 1px solid #e8edf5;
        border-radius: 12px;
        background: #fafcff;
        padding: 10px 12px;
    }

    .detail-item .label {
        font-size: 12px;
        color: #64748b;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .2px;
    }

    .detail-item .value {
        font-size: 14px;
        color: #0f172a;
        font-weight: 600;
        margin-top: 3px;
        word-break: break-word;
    }

    .pill-list {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .pill-list span {
        padding: 7px 10px;
        background: #eef4ff;
        border: 1px solid #dbe7ff;
        color: #1e3a8a;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 700;
    }

    .schedule-list {
        display: grid;
        gap: 10px;
    }

    .schedule-row {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        border: 1px solid #e8edf5;
        border-radius: 12px;
        padding: 10px 12px;
        background: #fff;
    }

    .schedule-row .day {
        font-weight: 800;
        color: #1e293b;
        text-transform: capitalize;
    }

    .review-item {
        border: 1px solid #e8edf5;
        border-radius: 12px;
        padding: 12px;
        background: #fff;
        margin-bottom: 10px;
    }

    .review-head {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 8px;
    }

    .review-name {
        font-weight: 800;
        color: #0f172a;
    }

    .review-date {
        color: #64748b;
        font-size: 12px;
    }

    @media (max-width: 991.98px) {
        .doctor-page {
            padding-top: 95px;
        }

        .doctor-avatar {
            width: 130px;
            height: 130px;
            margin-bottom: 14px;
        }

        .doctor-name {
            font-size: 26px;
        }

        .detail-grid,
        .doctor-metrics {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
@php
    $profileImage = $doctor->profile_image
        ? (filter_var($doctor->profile_image, FILTER_VALIDATE_URL) ? $doctor->profile_image : asset($doctor->profile_image))
        : asset('assets/img/doctors/doctor-thumb-02.jpg');

    $specialityImage = ($doctor->speciality && $doctor->speciality->image)
        ? (filter_var($doctor->speciality->image, FILTER_VALIDATE_URL) ? $doctor->speciality->image : asset($doctor->speciality->image))
        : null;

    $parseList = function ($value) {
        if (blank($value)) {
            return [];
        }
        if (is_array($value)) {
            return array_values(array_filter($value, fn ($item) => filled($item)));
        }
        $decoded = json_decode((string) $value, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return array_values(array_filter(array_map(fn ($item) => is_string($item) ? trim($item) : '', $decoded)));
        }
        return [trim((string) $value)];
    };

    $education = $parseList($doctor->education ?? null);
    $services = $parseList($doctor->services ?? null);
    $awards = $parseList($doctor->awards ?? null);
    $languages = $parseList($doctor->languages ?? null);
    $isOnline = optional($doctor->user)->isOnline();
    $lastSeen = optional($doctor->user?->last_seen_at)->diffForHumans();
    $locationText = trim(($doctor->clinic_address ?? '') . ', ' . ($doctor->area->name ?? '') . ', ' . ($doctor->district->name ?? ''), ', ');
@endphp

<div class="doctor-page">
    <div class="container">
        <div class="doctor-shell">
            <div class="doctor-hero">
                <div class="row g-4 align-items-start">
                    <div class="col-lg-3 col-md-4 text-center text-md-start">
                        <img src="{{ $profileImage }}" alt="{{ $doctor->user->name }}" class="doctor-avatar">
                    </div>
                    <div class="col-lg-6 col-md-8">
                        <h1 class="doctor-name">Dr. {{ $doctor->user->name }}</h1>
                        <p class="doctor-subtitle">{{ $doctor->qualification ?? $doctor->qualifications ?? 'Medical Specialist' }}</p>
                        <div class="doctor-chip-row">
                            <span class="doctor-chip">
                                @if($specialityImage)
                                    <img src="{{ $specialityImage }}" alt="Speciality" style="width:16px;height:16px;border-radius:50%;margin-right:6px;object-fit:cover;">
                                @endif
                                {{ $doctor->speciality->name ?? 'General' }}
                            </span>
                            @if($doctor->is_verified)
                                <span class="doctor-chip">Verified</span>
                            @endif
                            <span class="doctor-chip {{ $isOnline ? 'online' : '' }}">
                                {{ $isOnline ? 'Online Now' : 'Offline' }}{{ !$isOnline && $lastSeen ? ' • Last seen ' . $lastSeen : '' }}
                            </span>
                        </div>
                        <div class="doctor-rating mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= round($doctor->average_rating) ? 'filled' : '' }}"></i>
                            @endfor
                            <span class="muted"> {{ number_format($doctor->average_rating, 1) }} ({{ $doctor->review_count }} reviews)</span>
                        </div>
                        <div class="text-muted">
                            <i class="fas fa-map-marker-alt me-2"></i>{{ $locationText !== '' ? $locationText : 'Location not provided' }}
                        </div>
                        <div class="doctor-actions">
                            <a href="{{ route('booking', $doctor->id) }}" class="btn btn-primary">
                                <i class="fas fa-calendar-check me-1"></i> Book Appointment
                            </a>
                            <a href="{{ route('chat') }}" class="btn btn-outline-primary">
                                <i class="far fa-comment-dots me-1"></i> Chat
                            </a>
                            <a href="{{ route('voice.call') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-phone-alt me-1"></i> Voice Call
                            </a>
                            <a href="{{ route('video.call') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-video me-1"></i> Video Call
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="doctor-metrics">
                            <div class="metric">Consultation Fee<strong>৳{{ number_format((float) ($doctor->consultation_fee ?? 0), 0) }}</strong></div>
                            <div class="metric">Experience<strong>{{ (int) ($doctor->experience_years ?? 0) }} years</strong></div>
                            <div class="metric">Status<strong>{{ ucfirst($doctor->status ?? 'N/A') }}</strong></div>
                            <div class="metric">Home Visit<strong>{{ !empty($doctor->home_visit) ? 'Available' : 'No' }}</strong></div>
                            <div class="metric">Online Consult<strong>{{ !empty($doctor->online_consultation) ? 'Available' : 'No' }}</strong></div>
                            <div class="metric">Online Fee<strong>৳{{ number_format((float) ($doctor->online_fee ?? 0), 0) }}</strong></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="doctor-content">
                <div class="doctor-section">
                    <h5>About Doctor</h5>
                    <p class="mb-0 text-muted">{{ $doctor->bio ?? 'No biography added yet.' }}</p>
                </div>

                <div class="doctor-section">
                    <h5>Professional Details</h5>
                    <div class="detail-grid">
                        <div class="detail-item"><div class="label">Phone</div><div class="value">{{ $doctor->phone ?? 'N/A' }}</div></div>
                        <div class="detail-item"><div class="label">Gender</div><div class="value">{{ $doctor->gender ? ucfirst($doctor->gender) : 'N/A' }}</div></div>
                        <div class="detail-item"><div class="label">Date Of Birth</div><div class="value">{{ !empty($doctor->date_of_birth) ? \Carbon\Carbon::parse($doctor->date_of_birth)->format('d M Y') : 'N/A' }}</div></div>
                        <div class="detail-item"><div class="label">Registration No</div><div class="value">{{ $doctor->registration_number ?? 'N/A' }}</div></div>
                        <div class="detail-item"><div class="label">Registration Date</div><div class="value">{{ !empty($doctor->registration_date) ? \Carbon\Carbon::parse($doctor->registration_date)->format('d M Y') : 'N/A' }}</div></div>
                        <div class="detail-item"><div class="label">Clinic Name</div><div class="value">{{ $doctor->clinic_name ?? 'N/A' }}</div></div>
                        <div class="detail-item"><div class="label">Clinic Address</div><div class="value">{{ $doctor->clinic_address ?? 'N/A' }}</div></div>
                        <div class="detail-item"><div class="label">Area / District</div><div class="value">{{ ($doctor->area->name ?? 'N/A') . ' / ' . ($doctor->district->name ?? 'N/A') }}</div></div>
                        <div class="detail-item"><div class="label">Website</div><div class="value">{{ $doctor->website ?? 'N/A' }}</div></div>
                        <div class="detail-item"><div class="label">Facebook</div><div class="value">{{ $doctor->facebook ?? 'N/A' }}</div></div>
                        <div class="detail-item"><div class="label">LinkedIn</div><div class="value">{{ $doctor->linkedin ?? 'N/A' }}</div></div>
                        <div class="detail-item"><div class="label">Featured</div><div class="value">{{ !empty($doctor->is_featured) ? 'Yes' : 'No' }}</div></div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <div class="doctor-section">
                            <h5>Languages</h5>
                            @if(count($languages))
                                <div class="pill-list">@foreach($languages as $item)<span>{{ $item }}</span>@endforeach</div>
                            @else
                                <p class="text-muted mb-0">No languages added.</p>
                            @endif
                        </div>

                        <div class="doctor-section">
                            <h5>Services</h5>
                            @if(count($services))
                                <div class="pill-list">@foreach($services as $item)<span>{{ $item }}</span>@endforeach</div>
                            @else
                                <p class="text-muted mb-0">No services listed.</p>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="doctor-section">
                            <h5>Education</h5>
                            @if(count($education))
                                <div class="pill-list">@foreach($education as $item)<span>{{ $item }}</span>@endforeach</div>
                            @else
                                <p class="text-muted mb-0">No education history listed.</p>
                            @endif
                        </div>

                        <div class="doctor-section">
                            <h5>Awards</h5>
                            @if(count($awards))
                                <div class="pill-list">@foreach($awards as $item)<span>{{ $item }}</span>@endforeach</div>
                            @else
                                <p class="text-muted mb-0">No awards listed.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="doctor-section">
                    <h5>Business Hours</h5>
                    @if($doctor->schedules->count())
                        <div class="schedule-list">
                            @foreach($doctor->schedules as $schedule)
                                <div class="schedule-row">
                                    <span class="day">{{ $schedule->day }}</span>
                                    <span>{{ \Carbon\Carbon::parse($schedule->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('g:i A') }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">No schedules available.</p>
                    @endif
                </div>

                <div class="doctor-section mb-0">
                    <h5>Patient Reviews</h5>
                    @forelse($doctor->reviews as $review)
                        <div class="review-item">
                            <div class="review-head">
                                <div>
                                    <div class="review-name">{{ $review->patient->user->name ?? 'Patient' }}</div>
                                    <div class="doctor-rating">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $review->rating ? 'filled' : '' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                                <div class="review-date">{{ $review->created_at->diffForHumans() }}</div>
                            </div>
                            <p class="mb-0 text-muted">{{ $review->comment ?: 'No comment provided.' }}</p>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No reviews yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

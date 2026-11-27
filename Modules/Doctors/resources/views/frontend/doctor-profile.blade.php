@extends('layouts.app')

@section('title', 'Doctor Profile - abcsheba')

@push('styles')
<style>
    .doctor-page {
        background: linear-gradient(180deg, #f8fbff 0%, #f5f7fb 100%);
        min-height: 100vh;
        padding: 100px 0 24px;
    }

    .doctor-shell {
        border-radius: 20px;
        border: 1px solid #e5eaf3;
        background: #fff;
        box-shadow: 0 16px 45px rgba(15, 23, 42, 0.08);
        overflow: hidden;
    }

    .doctor-hero {
        padding: 20px;
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
        gap: 8px;
        margin-bottom: 8px;
    }

    .doctor-designation {
        margin: 0 0 8px;
        color: #1e3a8a;
        font-weight: 700;
        font-size: 15px;
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
        font-size: 14px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 3px;
    }

    .doctor-rating i {
        color: #cbd5e1;
    }

    .doctor-rating i.filled {
        color: #f59e0b;
    }

    .doctor-rating .muted {
        color: #64748b;
        font-weight: 600;
        margin-left: 6px;
    }

    .doctor-metrics {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 6px;
    }

    .metric {
        padding: 7px 10px;
        border-radius: 10px;
        border: 1px solid #e5eaf3;
        background: #fff;
        font-size: 12.5px;
        color: #475569;
        line-height: 1.3;
    }

    .metric strong {
        font-size: 14px;
        color: #0f172a;
        white-space: nowrap;
    }

    .doctor-actions {
        display: grid;
        grid-template-columns: 1fr;
        gap: 8px;
        margin-top: 12px;
        max-width: 480px;
    }

    .doctor-actions .btn {
        border-radius: 10px !important;
        font-weight: 600;
        padding: 9px 12px;
        font-size: 13px;
        line-height: 1.2;
        min-height: 40px;
        box-shadow: none !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        white-space: nowrap;
        transition: all 0.2s ease;
    }

    .doctor-actions .btn-primary {
        grid-column: 1 / -1;
        font-size: 15px;
        padding: 11px 18px;
    }

    .doctor-content {
        padding: 16px;
    }

    .doctor-section {
        border: 1px solid #e5eaf3;
        border-radius: 14px;
        background: #fff;
        padding: 14px 16px;
        margin-bottom: 10px;
    }

    .doctor-section h5 {
        margin: 0 0 8px;
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 6px 10px;
    }

    .detail-item {
        border: 1px solid #e8edf5;
        border-radius: 10px;
        background: #fafcff;
        padding: 7px 10px;
        font-size: 13.5px;
        line-height: 1.35;
    }

    .detail-item-full {
        grid-column: 1 / -1;
    }

    .detail-item .label {
        color: #0f172a;
        font-weight: 700;
    }

    .detail-item .value {
        color: #334155;
        font-weight: 500;
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
        gap: 6px;
    }

    .schedule-row {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        border: 1px solid #e8edf5;
        border-radius: 10px;
        padding: 7px 10px;
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
            padding-top: 80px;
        }

        .doctor-hero {
            padding: 14px;
        }

        .doctor-hero .row {
            --bs-gutter-y: 0.5rem;
        }

        .doctor-avatar {
            width: 120px;
            height: 120px;
            margin-bottom: 4px;
        }

        .doctor-name {
            font-size: 22px;
            margin-bottom: 4px;
        }

        .doctor-content {
            padding: 10px;
        }

        .doctor-section {
            padding: 12px;
        }

        /* fee / experience stay two per row on phones */
        .doctor-metrics {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .detail-grid {
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
    $locationText = $doctor->clinic_address ?? trim(($doctor->area->name ?? '') . ', ' . ($doctor->district->name ?? ''), ', ');

    $canChat = false;
    if (auth()->check()) {
        if (auth()->user()->role === 'patient') {
            $patient = auth()->user()->patient;
            if ($patient) {
                $canChat = \App\Models\Appointment::where('patient_id', $patient->id)
                    ->where('doctor_id', $doctor->id)
                    ->exists();
            }
        }
    } else {
        $canChat = true;
    }
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
                        <h1 class="doctor-name">{{ $doctor->user->name }}</h1>
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
                        @if(!empty($doctor->designation) || !empty($doctor->organization_name))
                            <p class="doctor-designation">
                                <i class="fas fa-briefcase-medical me-1"></i>
                                {{ $doctor->designation }}{{ !empty($doctor->designation) && !empty($doctor->organization_name) ? ', ' : '' }}{{ $doctor->organization_name }}
                            </p>
                        @endif
                        <div class="doctor-rating mb-2">
                            @if($doctor->review_count > 0)
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= round($doctor->average_rating) ? 'filled' : '' }}"></i>
                                @endfor
                                <span class="muted">{{ number_format($doctor->average_rating, 1) }} ({{ $doctor->review_count }} reviews)</span>
                            @else
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star"></i>
                                @endfor
                                <span class="muted">New Doctor (0 reviews)</span>
                            @endif
                        </div>
                        <div class="text-muted">
                            <i class="fas fa-map-marker-alt me-2"></i>{{ $locationText !== '' ? $locationText : 'Location not provided' }}
                        </div>
                        <div class="doctor-actions">
                            <a href="{{ route('booking', $doctor->id) }}" class="btn btn-primary">
                                <i class="fas fa-calendar-check"></i> Book Appointment
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="doctor-metrics">
                            <div class="metric">Consultation Fee: <strong>৳{{ number_format((float) ($doctor->consultation_fee ?? 0), 0) }}</strong></div>
                            <div class="metric">Experience: <strong>{{ (int) ($doctor->experience_years ?? 0) }} yrs</strong></div>
                            <div class="metric">Availability: <strong>Accepting</strong></div>
                            <div class="metric">Home Visit: <strong>{{ !empty($doctor->home_visit) ? 'Yes' : 'No' }}</strong></div>
                            <div class="metric">Online Consult: <strong>{{ !empty($doctor->online_consultation) ? 'Yes' : 'No' }}</strong></div>
                            <div class="metric">Online Fee: <strong>৳{{ number_format((float) ($doctor->online_fee ?? 0), 0) }}</strong></div>
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
                        <div class="detail-item"><span class="label">Gender:</span> <span class="value">{{ $doctor->gender ? ucfirst($doctor->gender) : 'N/A' }}</span></div>
                        <div class="detail-item"><span class="label">Registration No:</span> <span class="value">{{ $doctor->registration_number ?? 'Verified' }}</span></div>
                        @php $doctorLocation = ($doctor->area->name ?? '') . ($doctor->area && $doctor->district ? ', ' : '') . ($doctor->district->name ?? 'Dhaka'); @endphp
                        @forelse($doctor->clinic_locations as $i => $chamber)
                            @php $n = $i > 0 ? ' ' . ($i + 1) : ''; @endphp
                            <div class="detail-item"><span class="label">Chamber{{ $n }}:</span> <span class="value">{{ $chamber['name'] }}</span></div>
                            <div class="detail-item"><span class="label">Location{{ $n }}:</span> <span class="value">{{ $doctorLocation }}</span></div>
                            <div class="detail-item"><span class="label">Address{{ $n }}:</span> <span class="value">{{ $chamber['address'] ?: 'Available upon booking' }}</span></div>
                        @empty
                            <div class="detail-item"><span class="label">Chamber:</span> <span class="value">N/A</span></div>
                            <div class="detail-item"><span class="label">Location:</span> <span class="value">{{ $doctorLocation }}</span></div>
                            <div class="detail-item"><span class="label">Address:</span> <span class="value">Available upon booking</span></div>
                        @endforelse
                        @php
                            // Group schedules that share the same time range: "Saturday, Monday" / "05:00 PM - 07:30 PM"
                            $weekOrder = ['saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
                            $scheduleGroups = function ($type) use ($doctor, $weekOrder) {
                                $groups = [];
                                foreach ($doctor->schedules->where('type', $type)->where('is_available', '!=', 0) as $sc) {
                                    $time = \Carbon\Carbon::parse($sc->start_time)->format('h:i A') . ' - ' . \Carbon\Carbon::parse($sc->end_time)->format('h:i A');
                                    $groups[$time][] = strtolower(trim($sc->day));
                                }
                                $result = [];
                                foreach ($groups as $time => $days) {
                                    $days = array_values(array_intersect($weekOrder, array_unique($days)));
                                    if (empty($days)) continue;
                                    $result[] = [
                                        'days' => count($days) === 7 ? 'All days' : implode(', ', array_map('ucfirst', $days)),
                                        'time' => $time,
                                    ];
                                }
                                return $result;
                            };
                            $chamberHours = $scheduleGroups('offline');
                            $onlineHours = !empty($doctor->online_consultation) ? $scheduleGroups('online') : [];
                        @endphp
                        @foreach($chamberHours as $hours)
                            <div class="detail-item"><span class="label">Days:</span> <span class="value">{{ $hours['days'] }}</span></div>
                            <div class="detail-item"><span class="label">Time:</span> <span class="value">{{ $hours['time'] }}</span></div>
                        @endforeach
                        @if(!empty($doctor->online_consultation))
                            <div class="detail-item detail-item-full"><span class="label">Online Consultation:</span> <span class="value">Yes</span></div>
                            @foreach($onlineHours as $hours)
                                <div class="detail-item"><span class="label">Days:</span> <span class="value">{{ $hours['days'] }}</span></div>
                                <div class="detail-item"><span class="label">Time:</span> <span class="value">{{ $hours['time'] }}</span></div>
                            @endforeach
                        @endif
                        <div class="detail-item"><span class="label">Contact / Helpline:</span> <span class="value">{{ $doctor->phone ?? 'Support Available' }}</span></div>
                        @if(!empty($doctor->website) && $doctor->website !== 'N/A')
                            <div class="detail-item"><span class="label">Website:</span> <span class="value"><a href="{{ $doctor->website }}" target="_blank" class="text-primary">{{ $doctor->website }}</a></span></div>
                        @endif
                        @if(!empty($doctor->facebook) && $doctor->facebook !== 'N/A')
                            <div class="detail-item"><span class="label">Facebook:</span> <span class="value"><a href="{{ $doctor->facebook }}" target="_blank" class="text-primary">Profile</a></span></div>
                        @endif
                        @if(!empty($doctor->linkedin) && $doctor->linkedin !== 'N/A')
                            <div class="detail-item"><span class="label">LinkedIn:</span> <span class="value"><a href="{{ $doctor->linkedin }}" target="_blank" class="text-primary">Profile</a></span></div>
                        @endif
                    </div>
                </div>

                {{-- Dynamic Custom Profile Sections --}}
                @if(!empty($doctor->custom_sections) && is_iterable($doctor->custom_sections))
                    @foreach($doctor->custom_sections as $sec)
                        @continue(!is_array($sec))
                        @php
                            $secType = $sec['type'] ?? 'standard';
                            $secTitle = trim($sec['title'] ?? '');
                            $secDesc = trim($sec['description'] ?? '');
                            $secImg = $sec['image'] ?? null;
                        @endphp

                        @if($secType === 'image_only')
                            {{-- Image Only Section (Banner / Showcase) --}}
                            @if(!empty($secImg))
                                <div class="doctor-section custom-doctor-section custom-image-only-section">
                                    @if(!empty($secTitle))
                                        <h5 class="mb-3 text-dark font-weight-bold"><i class="fas fa-image text-primary me-2"></i>{{ $secTitle }}</h5>
                                    @endif
                                    <div class="custom-sec-banner-wrap rounded-3 overflow-hidden border shadow-sm bg-light text-center p-2">
                                        <img src="{{ asset($secImg) }}" 
                                             alt="{{ $secTitle ?: 'Doctor Image' }}" 
                                             class="img-fluid rounded-3"
                                             style="max-height: 480px; width: 100%; object-fit: contain; background: #fff;"
                                             onerror="this.closest('.custom-image-only-section').style.display='none';">
                                    </div>
                                </div>
                            @endif
                        @else
                            {{-- Standard Section (Text & Image) --}}
                            @if(!empty($secTitle) || !empty($secDesc) || !empty($secImg))
                                <div class="doctor-section custom-doctor-section">
                                    <div class="row align-items-center g-4">
                                        @if(!empty($secImg))
                                            <div class="col-md-5">
                                                <div class="custom-sec-img-wrap rounded-3 overflow-hidden border shadow-sm bg-light text-center p-1">
                                                    <img src="{{ asset($secImg) }}" 
                                                         alt="{{ $secTitle ?: 'Section Image' }}" 
                                                         class="img-fluid rounded-3"
                                                         style="max-height: 280px; width: 100%; object-fit: cover;"
                                                         onerror="this.closest('.col-md-5').style.display='none'; const siblingCol = this.closest('.row').querySelector('.col-md-7'); if(siblingCol){ siblingCol.classList.replace('col-md-7', 'col-12'); }">
                                                </div>
                                            </div>
                                            <div class="col-md-7">
                                                @if(!empty($secTitle))
                                                    <h5 class="mb-2 text-dark font-weight-bold">{{ $secTitle }}</h5>
                                                @endif
                                                @if(!empty($secDesc))
                                                    <div class="text-secondary" style="font-size: 15px; line-height: 1.7;">
                                                        {!! nl2br(e($secDesc)) !!}
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <div class="col-12">
                                                @if(!empty($secTitle))
                                                    <h5 class="mb-2 text-dark font-weight-bold">{{ $secTitle }}</h5>
                                                @endif
                                                @if(!empty($secDesc))
                                                    <div class="text-secondary" style="font-size: 15px; line-height: 1.7;">
                                                        {!! nl2br(e($secDesc)) !!}
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endif
                    @endforeach
                @endif

                @php
                    $hasLeftCol = count($languages) > 0 || count($services) > 0;
                    $hasRightCol = count($education) > 0 || count($awards) > 0;
                @endphp

                @if($hasLeftCol || $hasRightCol)
                    <div class="row">
                        @if($hasLeftCol)
                            <div class="{{ $hasRightCol ? 'col-lg-6' : 'col-12' }}">
                                @if(count($languages))
                                    <div class="doctor-section">
                                        <h5>Languages</h5>
                                        <div class="pill-list">@foreach($languages as $item)<span>{{ $item }}</span>@endforeach</div>
                                    </div>
                                @endif

                                @if(count($services))
                                    <div class="doctor-section">
                                        <h5>Services</h5>
                                        <div class="pill-list">@foreach($services as $item)<span>{{ $item }}</span>@endforeach</div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        @if($hasRightCol)
                            <div class="{{ $hasLeftCol ? 'col-lg-6' : 'col-12' }}">
                                @if(count($education))
                                    <div class="doctor-section">
                                        <h5>Education</h5>
                                        <div class="pill-list">@foreach($education as $item)<span>{{ $item }}</span>@endforeach</div>
                                    </div>
                                @endif

                                @if(count($awards))
                                    <div class="doctor-section">
                                        <h5>Awards</h5>
                                        <div class="pill-list">@foreach($awards as $item)<span>{{ $item }}</span>@endforeach</div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @endif

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

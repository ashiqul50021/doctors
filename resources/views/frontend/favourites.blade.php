@extends('layouts.app')

@section('title', 'Favourites - abcsheba')

@push('styles')
<style>
    .doctor-card-new {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        overflow: hidden;
        transition: all 0.3s;
        height: 100%;
        display: flex;
        flex-direction: column;
        border: 1px solid #f0f0f0;
    }

    .doctor-card-new:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 50px rgba(0, 102, 255, 0.15);
        border-color: #1D4ED8;
    }

    .doctor-img-wrapper {
        position: relative;
        height: 240px;
        overflow: hidden;
        background: linear-gradient(135deg, #e8f4ff 0%, #f0f8ff 100%);
    }

    .doctor-img-wrapper .doctor-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center top;
        transition: transform 0.3s;
    }

    .doctor-card-new:hover .doctor-img {
        transform: scale(1.05);
    }

    .doctor-fee-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: linear-gradient(135deg, #1D4ED8, #60A5FA);
        color: #fff;
        padding: 8px 15px;
        border-radius: 25px;
        font-weight: 700;
        font-size: 14px;
        box-shadow: 0 4px 15px rgba(0, 102, 255, 0.3);
    }

    .doctor-info {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .doctor-speciality {
        font-size: 13px;
        color: #1D4ED8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
        font-weight: 600;
        display: inline-block;
        background: #e8f4ff;
        padding: 5px 12px;
        border-radius: 20px;
        width: fit-content;
    }

    .doctor-name {
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 10px;
        line-height: 1.4;
        min-height: 25px;
    }

    .doctor-name a {
        color: #272b41;
        text-decoration: none;
    }

    .doctor-name a:hover {
        color: #1D4ED8;
    }

    .verified-badge {
        color: #09e5ab;
        font-size: 14px;
        margin-left: 5px;
    }

    .doctor-rating {
        display: flex;
        align-items: center;
        gap: 5px;
        margin-bottom: 10px;
        font-size: 14px;
    }

    .doctor-rating i {
        color: #ffc107;
    }

    .doctor-rating .rating-count {
        color: #888;
        font-size: 12px;
    }

    .doctor-location {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 15px;
        font-size: 13px;
        color: #666;
    }

    .doctor-location i {
        color: #1D4ED8;
    }

    /* Doctor Buttons Container */
    .doctor-buttons {
        display: flex;
        gap: 10px;
        margin-top: auto;
    }

    .btn-view-details {
        flex: 1;
        padding: 10px 8px;
        background: transparent;
        border: 2px solid #1D4ED8;
        border-radius: 8px;
        color: #1D4ED8;
        font-weight: 600;
        font-size: 12px;
        text-align: center;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-view-details:hover {
        background: #1D4ED8;
        color: #fff;
        text-decoration: none;
    }

    .btn-view-details i {
        margin-right: 4px;
    }

    .btn-book-appointment {
        flex: 1;
        padding: 10px 8px;
        background: linear-gradient(135deg, #1D4ED8, #60A5FA);
        border: none;
        border-radius: 8px;
        color: #fff;
        font-weight: 600;
        font-size: 12px;
        text-align: center;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-book-appointment:hover {
        background: linear-gradient(135deg, #1E40AF, #3B82F6);
        transform: translateY(-2px);
        color: #fff;
        text-decoration: none;
    }

    .btn-book-appointment i {
        margin-right: 4px;
    }
</style>
@endpush

@section('content')

<!-- Page Content -->
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-md-5 col-lg-4 col-xl-3 theiaStickySidebar">
                @include('frontend.includes.patient-sidebar')
            </div>

            <div class="col-md-7 col-lg-8 col-xl-9">
                <div class="row row-grid">

                    @forelse($doctors ?? [] as $doctor)
                    <!-- Doctor Widget -->
                    <div class="col-xl-4 col-md-6 col-sm-6 mb-4 doctor-grid-item">
                        <div class="doctor-card-new">
                            <div class="doctor-img-wrapper">
                                <a href="{{ route('doctors.profile', $doctor->id) }}">
                                    <img src="{{ $doctor->profile_image ? asset($doctor->profile_image) : asset('assets/img/doctors/doctor-thumb-01.jpg') }}"
                                        class="doctor-img" alt="{{ $doctor->user->name }}">
                                </a>
                                <div class="doctor-fee-badge">
                                    <span>৳{{ $doctor->pricing === 'free' ? 'Free' : number_format($doctor->custom_price ?: ($doctor->consultation_fee ?: 500), 0) }}</span>
                                </div>
                                <a href="javascript:void(0)" class="fav-btn active" data-id="{{ $doctor->id }}">
                                    <i class="fas fa-bookmark"></i>
                                </a>
                            </div>
                            <div class="doctor-info">
                                <span class="doctor-speciality">{{ $doctor->speciality->name ?? 'General' }}</span>
                                <h4 class="doctor-name">
                                    <a href="{{ route('doctors.profile', $doctor->id) }}">{{ str_starts_with(strtolower($doctor->user->name), 'dr.') ? $doctor->user->name : 'Dr. ' . $doctor->user->name }}</a>
                                    @if($doctor->is_verified)
                                        <i class="fas fa-check-circle verified-badge" title="Verified" style="color: #09e5ab;"></i>
                                    @endif
                                </h4>
                                <div class="doctor-rating">
                                    <i class="fas fa-star" style="color: #f39c12; margin-right: 4px;"></i>
                                    <span>{{ number_format($doctor->average_rating, 1) }}</span>
                                    <span class="rating-count">({{ $doctor->review_count }} reviews)</span>
                                </div>
                                <div class="doctor-location">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>{{ $doctor->clinic_name ?? ($doctor->area->name ?? 'Dhaka') }}</span>
                                </div>
                                <div class="doctor-buttons">
                                    <a href="{{ route('doctors.profile', $doctor->id) }}" class="btn-view-details">
                                        <i class="fas fa-user"></i> Details
                                    </a>
                                    <a href="{{ route('booking', $doctor->id) }}" class="btn-book-appointment">
                                        <i class="fas fa-calendar-check"></i> Appointment
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Doctor Widget -->
                    @empty
                    <div class="col-12 text-center py-5">
                        <div class="text-muted">No favourite doctors found.</div>
                    </div>
                    @endforelse

                </div>
            </div>
        </div>
    </div>

</div>
<!-- /Page Content -->
@endsection

@push('scripts')
<script src="{{ asset('assets/plugins/theia-sticky-sidebar/ResizeSensor.js') }}"></script>
<script src="{{ asset('assets/plugins/theia-sticky-sidebar/theia-sticky-sidebar.js') }}"></script>
@endpush

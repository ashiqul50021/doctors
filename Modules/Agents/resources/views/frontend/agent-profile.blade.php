@extends('layouts.app')

@section('title', $agent->user->name . ' - Partner Profile - abcsheba.com')

@section('content')
<!-- Page Content -->
<div class="content" style="padding-top: 100px; min-height: 80vh; background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Premium Profile Card -->
                <div class="card border-0 shadow-lg overflow-hidden" style="border-radius: 20px; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px);">
                    <div class="card-body p-0">
                        <!-- Top Banner / Accent Gradient -->
                        <div style="height: 150px; background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); position: relative;">
                            <div class="position-absolute bottom-0 start-50 translate-middle-x" style="transform: translateY(50%) !important;">
                                <div class="profile-img-wrapper" style="width: 130px; height: 130px; border-radius: 50%; border: 5px solid #fff; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.15); background: #fff;">
                                    <img src="{{ $agent->profile_image ? asset($agent->profile_image) : asset('assets/img/doctors/doctor-thumb-02.jpg') }}" 
                                         alt="{{ $agent->user->name }}" 
                                         style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                            </div>
                        </div>

                        <!-- Profile Info Section -->
                        <div class="text-center" style="padding: 90px 30px 40px 30px;">
                            <div class="mb-2">
                                <span class="badge rounded-pill bg-success-light px-3 py-2 text-success fw-bold" style="font-size: 0.85rem; letter-spacing: 0.5px;">
                                    <i class="fas fa-check-circle me-1"></i> VERIFIED PARTNER
                                </span>
                            </div>
                            <h2 class="fw-bold text-dark mb-1" style="font-size: 2rem;">{{ $agent->user->name }}</h2>
                            <p class="text-muted mb-4"><i class="fas fa-user-tie me-1"></i> Authorized Agent, ABC Sheba</p>

                            <!-- Glassmorphism Referral Message -->
                            <div class="p-4 mb-5 text-center" style="background: rgba(30, 60, 114, 0.05); border-radius: 16px; border: 1px solid rgba(30, 60, 114, 0.1);">
                                <h5 class="fw-bold text-primary mb-2">Welcome! You have been referred by {{ $agent->user->name }}</h5>
                                <p class="text-secondary mb-0 mb-md-2" style="font-size: 0.95rem;">
                                    Get premium access to our best healthcare services, consultations, health packages, and online courses. By visiting through this link, you will receive maximum discount benefits.
                                </p>
                                @if($agent->referral_code)
                                    <div class="mt-3">
                                        <span class="text-muted small">Partner Code: </span>
                                        <span class="badge bg-primary px-3 py-2 fw-mono" style="font-size: 0.9rem; letter-spacing: 1px;">{{ $agent->referral_code }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Interactive CTAs -->
                            <h4 class="fw-bold text-dark mb-4">Our Services</h4>
                            <div class="row g-4 justify-content-center">
                                <!-- Doctors CTA -->
                                @if($agent->can_book_appointments)
                                <div class="col-md-4">
                                    <div class="service-card p-4 h-100 text-center border-0 shadow-sm transition-all" style="border-radius: 16px; background: #fff; cursor: pointer;">
                                        <div class="icon-box mb-3 d-inline-flex align-items-center justify-content-center bg-primary-light rounded-circle" style="width: 70px; height: 70px; color: #2a5298;">
                                            <i class="fas fa-user-md fa-2x"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-2">Consult Doctors</h5>
                                        <p class="text-muted small mb-3">Book appointments with top verified specialist doctors.</p>
                                        <a href="{{ route('doctors.search') }}" class="btn btn-outline-primary w-100 rounded-pill">Book Now</a>
                                    </div>
                                </div>
                                @endif

                                <!-- Products CTA -->
                                @if($agent->can_sell_products)
                                <div class="col-md-4">
                                    <div class="service-card p-4 h-100 text-center border-0 shadow-sm transition-all" style="border-radius: 16px; background: #fff; cursor: pointer;">
                                        <div class="icon-box mb-3 d-inline-flex align-items-center justify-content-center bg-success-light rounded-circle" style="width: 70px; height: 70px; color: #28a745;">
                                            <i class="fas fa-briefcase-medical fa-2x"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-2">Products</h5>
                                        <p class="text-muted small mb-3">Order essential healthcare devices and wellness packages.</p>
                                        <a href="{{ route('ecommerce.products') }}" class="btn btn-outline-success w-100 rounded-pill">Shop Now</a>
                                    </div>
                                </div>
                                @endif

                                <!-- Courses CTA -->
                                @if($agent->can_sell_courses)
                                <div class="col-md-4">
                                    <div class="service-card p-4 h-100 text-center border-0 shadow-sm transition-all" style="border-radius: 16px; background: #fff; cursor: pointer;">
                                        <div class="icon-box mb-3 d-inline-flex align-items-center justify-content-center bg-warning-light rounded-circle" style="width: 70px; height: 70px; color: #ffc107;">
                                            <i class="fas fa-graduation-cap fa-2x"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-2">Health Courses</h5>
                                        <p class="text-muted small mb-3">Learn health, safety, and wellness from expert instructors.</p>
                                        <a href="{{ route('courses.index') }}" class="btn btn-outline-warning w-100 rounded-pill">Browse Courses</a>
                                    </div>
                                </div>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .service-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .service-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
    }
    .bg-primary-light {
        background-color: rgba(42, 82, 152, 0.1) !important;
    }
    .bg-success-light {
        background-color: rgba(40, 167, 69, 0.1) !important;
    }
    .bg-warning-light {
        background-color: rgba(255, 193, 7, 0.1) !important;
    }
</style>
<!-- /Page Content -->
@endsection

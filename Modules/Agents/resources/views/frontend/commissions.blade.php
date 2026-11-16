@extends('layouts.app')

@section('title', 'Commission Breakdown - ' . ($siteSettings['site_name'] ?? 'abcsheba'))

@section('content')
    <!-- Breadcrumb -->
    <div class="breadcrumb-bar">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-12 col-12">
                    <nav aria-label="breadcrumb" class="page-breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('agent.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Commission Breakdown</li>
                        </ol>
                    </nav>
                    <h2 class="breadcrumb-title">Commission Breakdown</h2>
                </div>
            </div>
        </div>
    </div>
    <!-- /Breadcrumb -->

    <!-- Page Content -->
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-md-5 col-lg-4 col-xl-3 theiaStickySidebar">
                    @include('agents::frontend.includes.agent-sidebar')
                </div>

                <div class="col-md-7 col-lg-8 col-xl-9">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="card-title mb-0">Your Commission Settings</h5>
                            <p class="text-muted small mb-0 mt-1">View detailed breakdown of your commissions across products, doctor bookings, and courses</p>
                        </div>

                        <!-- Tab Navigation -->
                        <ul class="nav nav-tabs nav-tabs-bottom" role="tablist" style="border-bottom: 1px solid #f0f0f0;">
                            <li class="nav-item">
                                <a class="nav-link active" id="products-tab" data-bs-toggle="tab" href="#products-pane" role="tab" aria-controls="products-pane" aria-selected="true">
                                    <i class="fas fa-box-open me-2"></i> Products <span class="badge bg-info ms-2">{{ $productCommissions->count() }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="bookings-tab" data-bs-toggle="tab" href="#bookings-pane" role="tab" aria-controls="bookings-pane" aria-selected="false">
                                    <i class="fas fa-calendar-check me-2"></i> Doctor Bookings
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="courses-tab" data-bs-toggle="tab" href="#courses-pane" role="tab" aria-controls="courses-pane" aria-selected="false">
                                    <i class="fas fa-graduation-cap me-2"></i> Courses <span class="badge bg-info ms-2">{{ $courses->count() }}</span>
                                </a>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content">
                            <!-- Tab 1: Products -->
                            <div class="tab-pane fade show active" id="products-pane" role="tabpanel" aria-labelledby="products-tab">
                                <div class="card-body p-4">
                                    @if($productCommissions->isEmpty())
                                        <div class="alert alert-info text-center py-5">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <strong>No products with custom commissions yet.</strong>
                                            <p class="text-muted mt-2 mb-0">Products can be configured with specific commission rates in the product settings.</p>
                                        </div>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Product Name</th>
                                                        <th class="text-center">Sale Price</th>
                                                        <th class="text-center">Agent Commission</th>
                                                        <th class="text-center">Coupon Commission</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($productCommissions as $product)
                                                        <tr>
                                                            <td class="fw-bold text-dark">{{ $product->name }}</td>
                                                            <td class="text-center">
                                                                <span class="badge bg-success">৳{{ number_format($product->sale_price ?? $product->price, 2) }}</span>
                                                            </td>
                                                            <td class="text-center">
                                                                @if($product->agent_commission_value)
                                                                    @if($product->agent_commission_type === 'fixed')
                                                                        <span class="badge bg-primary">৳{{ number_format($product->agent_commission_value, 2) }} per unit</span>
                                                                    @else
                                                                        <span class="badge bg-primary">{{ $product->agent_commission_value }}%</span>
                                                                    @endif
                                                                @else
                                                                    <span class="text-muted small"><i class="fas fa-info-circle"></i> Uses agent default</span>
                                                                @endif
                                                            </td>
                                                            <td class="text-center">
                                                                @if($product->coupon_commission_value)
                                                                    @if($product->coupon_commission_type === 'fixed')
                                                                        <span class="badge bg-warning">৳{{ number_format($product->coupon_commission_value, 2) }} per unit</span>
                                                                    @else
                                                                        <span class="badge bg-warning">{{ $product->coupon_commission_value }}%</span>
                                                                    @endif
                                                                @else
                                                                    <span class="text-muted small"><i class="fas fa-info-circle"></i> Uses agent commission</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- Legend -->
                                        <div class="mt-4 pt-3 border-top">
                                            <p class="text-muted small mb-2"><strong>Legend:</strong></p>
                                            <ul class="list-unstyled small text-muted">
                                                <li><i class="fas fa-square me-2" style="color: #0d6efd;"></i> <strong>Agent Commission</strong> - Earned when customers order directly via your referral link</li>
                                                <li><i class="fas fa-square me-2" style="color: #ffc107;"></i> <strong>Coupon Commission</strong> - Earned when customers use your coupon code at checkout</li>
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Tab 2: Doctor Bookings -->
                            <div class="tab-pane fade" id="bookings-pane" role="tabpanel" aria-labelledby="bookings-tab">
                                <div class="card-body p-4">
                                    @if($doctors->isEmpty())
                                        <div class="alert alert-info text-center py-5">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <strong>No doctors available.</strong>
                                        </div>
                                    @else
                                        <div class="mb-4 p-3 bg-light rounded">
                                            <h6 class="mb-2 fw-bold">Your Booking Commission Rate</h6>
                                            <h4 class="text-primary mb-0">
                                                <i class="fas fa-taka-sign"></i>{{ number_format($bookingCommission, 2) }} <span class="small text-muted">per appointment</span>
                                            </h4>
                                            <small class="text-muted d-block mt-2">You earn this amount for each appointment booked through your referral</small>
                                        </div>

                                        <div class="row g-3">
                                            @foreach($doctors as $doctor)
                                                <div class="col-md-6">
                                                    <div class="card border-0 shadow-sm h-100 hover-shadow transition-all">
                                                        <div class="card-body">
                                                            <div class="d-flex align-items-center mb-3">
                                                                @if($doctor->user->avatar)
                                                                    <img src="{{ asset($doctor->user->avatar) }}" alt="{{ $doctor->user->name }}" class="rounded-circle me-3" width="50" height="50" style="object-fit: cover;">
                                                                @else
                                                                    <div class="rounded-circle me-3 bg-primary d-flex align-items-center justify-content-center text-white" style="width: 50px; height: 50px;">
                                                                        <i class="fas fa-user-md fa-lg"></i>
                                                                    </div>
                                                                @endif
                                                                <div class="flex-grow-1">
                                                                    <h6 class="mb-0 fw-bold">{{ $doctor->user->name }}</h6>
                                                                    <small class="text-muted">{{ $doctor->specialization }}</small>
                                                                </div>
                                                            </div>
                                                            <div class="mt-3 pt-3 border-top">
                                                                <p class="mb-2 small text-muted">Your Commission per Booking:</p>
                                                                <h5 class="text-success mb-0">
                                                                    <i class="fas fa-taka-sign"></i>{{ number_format($bookingCommission, 2) }}
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="mt-4 pt-3 border-top">
                                            <p class="text-muted small mb-2"><strong>How it works:</strong></p>
                                            <ul class="list-unstyled small text-muted">
                                                <li><i class="fas fa-check-circle text-success me-2"></i> Share your referral link with customers</li>
                                                <li><i class="fas fa-check-circle text-success me-2"></i> When they book an appointment with any doctor, you earn ৳{{ number_format($bookingCommission, 2) }}</li>
                                                <li><i class="fas fa-check-circle text-success me-2"></i> Commission is credited to your wallet when appointment is confirmed</li>
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Tab 3: Courses -->
                            <div class="tab-pane fade" id="courses-pane" role="tabpanel" aria-labelledby="courses-tab">
                                <div class="card-body p-4">
                                    @if($courses->isEmpty())
                                        <div class="alert alert-info text-center py-5">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <strong>No courses available yet.</strong>
                                        </div>
                                    @else
                                        <div class="mb-4 p-3 bg-light rounded">
                                            <h6 class="mb-2 fw-bold">Your Course Commission Rate</h6>
                                            <h4 class="text-primary mb-0">{{ $courseCommission }}% <span class="small text-muted">commission on course sales</span></h4>
                                            <small class="text-muted d-block mt-2">You earn this percentage on each course enrollment through your referral</small>
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Course Name</th>
                                                        <th class="text-center">Price</th>
                                                        <th class="text-center">Your Commission</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($courses as $course)
                                                        @php
                                                            $commissionAmount = ($course->price * $courseCommission) / 100;
                                                        @endphp
                                                        <tr>
                                                            <td class="fw-bold text-dark">{{ $course->title }}</td>
                                                            <td class="text-center">
                                                                <span class="badge bg-success">৳{{ number_format($course->price, 2) }}</span>
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="badge bg-primary">{{ $courseCommission }}% (৳{{ number_format($commissionAmount, 2) }})</span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="mt-4 pt-3 border-top">
                                            <p class="text-muted small mb-2"><strong>How it works:</strong></p>
                                            <ul class="list-unstyled small text-muted">
                                                <li><i class="fas fa-check-circle text-success me-2"></i> Share your referral link with potential learners</li>
                                                <li><i class="fas fa-check-circle text-success me-2"></i> When they enroll in any course, you earn {{ $courseCommission }}% of the course price</li>
                                                <li><i class="fas fa-check-circle text-success me-2"></i> Commission is credited to your wallet upon successful enrollment</li>
                                            </ul>
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
        .hover-shadow {
            transition: box-shadow 0.3s ease;
        }
        .hover-shadow:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
        }
        .nav-tabs-bottom {
            border-bottom: none;
        }
        .nav-tabs-bottom .nav-link {
            border: none;
            border-bottom: 3px solid transparent;
            border-radius: 0;
            padding: 1rem 1.5rem;
            transition: all 0.3s ease;
            color: #6b7280;
            font-weight: 500;
        }
        .nav-tabs-bottom .nav-link:hover {
            border-bottom-color: #0d6efd;
            color: #0d6efd;
            background-color: rgba(13, 110, 253, 0.05);
        }
        .nav-tabs-bottom .nav-link.active {
            border-bottom-color: #0d6efd;
            color: #0d6efd;
            background-color: transparent;
        }
    </style>
@endsection

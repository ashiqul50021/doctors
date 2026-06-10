@extends('layouts.app')

@section('title', 'Agent Dashboard - ' . ($siteSettings['site_name'] ?? 'abcsheba'))

@push('styles')
<style>
    .content {
        background-color: #f8f9fa;
        padding: 40px 0;
    }
    .dash-card {
        border-radius: 15px;
        border: none;
        box-shadow: 0 4px 25px rgba(0, 0, 0, 0.05);
        margin-bottom: 30px;
        background: #fff;
    }
    .dash-widget {
        display: flex;
        align-items: center;
        padding: 25px 20px;
        position: relative;
    }
    .dct-border-rht {
        border-right: 1px solid #f0f0f0;
    }
    .circle-bar {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 20px;
        flex-shrink: 0;
        position: relative;
        overflow: hidden;
    }
    .circle-bar1 { background: rgba(52, 92, 206, 0.1); }
    .circle-bar2 { background: rgba(15, 183, 107, 0.1); }
    .circle-bar3 { background: rgba(247, 53, 99, 0.1); }
    
    .dash-widget-info {
        flex: 1;
    }
    .dash-widget-info h6 {
        font-size: 0.85rem;
        color: #6b7280;
        font-weight: 600;
        margin-bottom: 5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .dash-widget-info h3 {
        font-size: 1.6rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 3px;
    }
    .dash-widget-info p {
        font-size: 0.8rem;
        margin-bottom: 0;
        color: #9ca3af;
    }
    @media (max-width: 991px) {
        .dct-border-rht {
            border-right: none;
            border-bottom: 1px solid #f0f0f0;
        }
    }
</style>
@endpush

@section('content')
    <!-- Breadcrumb -->
    <div class="breadcrumb-bar">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-12 col-12">
                    <nav aria-label="breadcrumb" class="page-breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Agent Dashboard</li>
                        </ol>
                    </nav>
                    <h2 class="breadcrumb-title">Dashboard</h2>
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
                    <!-- Stats Section -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card dash-card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 col-lg-4">
                                            <div class="dash-widget dct-border-rht">
                                                <div class="circle-bar circle-bar1">
                                                    <i class="fas fa-wallet text-primary" style="font-size: 1.8rem; z-index: 2;"></i>
                                                </div>
                                                <div class="dash-widget-info">
                                                    <h6>Wallet Balance</h6>
                                                    <h3>৳{{ number_format($availableBalance, 2) }}</h3>
                                                    <p>Available for Cashout</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12 col-lg-4">
                                            <div class="dash-widget dct-border-rht">
                                                <div class="circle-bar circle-bar2">
                                                    <i class="fas fa-hand-holding-usd text-success" style="font-size: 1.8rem; z-index: 2;"></i>
                                                </div>
                                                <div class="dash-widget-info">
                                                    <h6>Total Earned</h6>
                                                    <h3>৳{{ number_format($totalEarned, 2) }}</h3>
                                                    <p>Life-time Commissions</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12 col-lg-4">
                                            <div class="dash-widget">
                                                <div class="circle-bar circle-bar3">
                                                    <i class="fas fa-chart-line text-danger" style="font-size: 1.8rem; z-index: 2;"></i>
                                                </div>
                                                <div class="dash-widget-info">
                                                    <h6>Sales & Bookings</h6>
                                                    <h3>{{ $bookingsCount + $salesCount + $coursesCount }}</h3>
                                                    <p class="text-muted" style="font-size: 0.75rem !important;">{{ $bookingsCount }} B | {{ $salesCount }} P | {{ $coursesCount }} C</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Referral Links Card -->
                    <div class="card mt-4 shadow-sm">
                        <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                            <h4 class="card-title font-weight-bold mb-0">Your Affiliate Referral Links</h4>
                            <p class="text-muted mb-0 small">Share these links with customers. Anyone purchasing via these links will earn you commissions.</p>
                        </div>
                        <div class="card-body px-4 pb-4">
                            @if ($agent->can_sell_products)
                                <div class="mb-3">
                                    <label class="form-label font-weight-bold text-muted small">Products Shop Link</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="{{ url('/products?ref=' . $agent->referral_code) }}" id="product_ref_link" readonly>
                                        <button class="btn btn-outline-primary" type="button" onclick="copyLink('product_ref_link')">Copy Link</button>
                                    </div>
                                </div>
                            @endif

                            @if ($agent->can_sell_courses)
                                <div class="mb-0">
                                    <label class="form-label font-weight-bold text-muted small">Online Courses Link</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="{{ url('/courses?ref=' . $agent->referral_code) }}" id="course_ref_link" readonly>
                                        <button class="btn btn-outline-primary" type="button" onclick="copyLink('course_ref_link')">Copy Link</button>
                                    </div>
                                </div>
                            @endif

                            @php
                                $linkedCoupon = $agent->coupons()->where('status', true)->first();
                            @endphp
                            @if ($linkedCoupon)
                                <div class="mt-4 pt-3 border-top">
                                    <label class="form-label font-weight-bold text-muted small">Your Personal Discount Coupon Code</label>
                                    <p class="text-muted mb-2 small">Customers can use this coupon at checkout to get a discount. You will still receive the referral commission!</p>
                                    <div class="input-group" style="max-width: 450px;">
                                        <span class="input-group-text bg-light font-weight-bold text-primary">{{ $linkedCoupon->type == 'percent' ? $linkedCoupon->amount . '%' : '৳' . number_format($linkedCoupon->amount, 0) }} OFF</span>
                                        <input type="text" class="form-control font-weight-bold text-center bg-white" value="{{ $linkedCoupon->code }}" id="coupon_code_field" readonly style="letter-spacing: 1px;">
                                        <button class="btn btn-outline-primary" type="button" onclick="copyLink('coupon_code_field')">Copy Coupon</button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Tabs Section for Recent Activity -->
                    <div class="card mt-4 shadow-sm">
                        <div class="card-body px-0 py-0">
                            <nav>
                                <div class="nav nav-tabs nav-tabs-solid px-4 pt-3 border-bottom-0" id="nav-tab" role="tablist">
                                    <button class="nav-link active" id="nav-bookings-tab" data-bs-toggle="tab" data-bs-target="#nav-bookings" type="button" role="tab" aria-controls="nav-bookings" aria-selected="true">Recent Bookings</button>
                                    <button class="nav-link" id="nav-orders-tab" data-bs-toggle="tab" data-bs-target="#nav-orders" type="button" role="tab" aria-controls="nav-orders" aria-selected="false">Recent Orders</button>
                                    <button class="nav-link" id="nav-txs-tab" data-bs-toggle="tab" data-bs-target="#nav-txs" type="button" role="tab" aria-controls="nav-txs" aria-selected="false">Wallet Ledger</button>
                                </div>
                            </nav>
                            <div class="tab-content p-4" id="nav-tabContent">
                                <!-- Bookings Tab -->
                                <div class="tab-pane fade show active" id="nav-bookings" role="tabpanel" aria-labelledby="nav-bookings-tab">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-center mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Doctor</th>
                                                    <th>Patient</th>
                                                    <th>Appt Date</th>
                                                    <th>Appt Time</th>
                                                    <th>Fee</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($recentBookings as $appt)
                                                    <tr>
                                                        <td>
                                                            <strong>{{ $appt->doctor->user->name ?? 'N/A' }}</strong><br>
                                                            <small class="text-muted">{{ $appt->doctor->speciality->name ?? '' }}</small>
                                                        </td>
                                                        <td>{{ $appt->patient->user->name ?? 'N/A' }}</td>
                                                        <td>{{ $appt->appointment_date->format('d M Y') }}</td>
                                                        <td>{{ date('h:i A', strtotime($appt->appointment_time)) }}</td>
                                                        <td>৳{{ number_format($appt->fee, 2) }}</td>
                                                        <td>
                                                            <span class="badge rounded-pill bg-{{ $appt->status === 'completed' ? 'success' : ($appt->status === 'confirmed' ? 'info' : ($appt->status === 'pending' ? 'warning' : 'danger')) }}-light">
                                                                {{ ucfirst($appt->status) }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6" class="text-center py-4 text-muted">No appointments booked yet.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Orders Tab -->
                                <div class="tab-pane fade" id="nav-orders" role="tabpanel" aria-labelledby="nav-orders-tab">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-center mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Order No.</th>
                                                    <th>Customer</th>
                                                    <th>Total</th>
                                                    <th>Date</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($recentOrders as $order)
                                                    <tr>
                                                        <td><strong>#{{ $order->order_number }}</strong></td>
                                                        <td>
                                                            {{ $order->customer_name ?? 'Guest' }}<br>
                                                            <small class="text-muted">{{ $order->customer_phone }}</small>
                                                        </td>
                                                        <td>৳{{ number_format($order->total, 2) }}</td>
                                                        <td>{{ $order->created_at->format('d M Y') }}</td>
                                                        <td>
                                                            <span class="badge rounded-pill bg-{{ $order->status === 'delivered' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'info') }}-light">
                                                                {{ ucfirst($order->status) }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center py-4 text-muted">No product orders placed yet.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Ledger Tab -->
                                <div class="tab-pane fade" id="nav-txs" role="tabpanel" aria-labelledby="nav-txs-tab">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-center mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Type</th>
                                                    <th>Ref ID</th>
                                                    <th>Description</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($recentTransactions as $tx)
                                                    <tr>
                                                        <td>{{ $tx->created_at->format('d M Y, h:i A') }}</td>
                                                        <td>
                                                            <span class="badge bg-{{ in_array($tx->type, ['commission_booking', 'commission_product', 'commission_course']) ? 'success' : 'danger' }}">
                                                                {{ str_replace('_', ' ', ucfirst($tx->type)) }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $tx->reference_id ?? '-' }}</td>
                                                        <td>{{ $tx->description }}</td>
                                                        <td>
                                                            <span class="font-weight-bold text-{{ in_array($tx->type, ['commission_booking', 'commission_product', 'commission_course']) ? 'success' : 'danger' }}">
                                                                {{ in_array($tx->type, ['commission_booking', 'commission_product', 'commission_course']) ? '+' : '-' }}৳{{ number_format($tx->amount, 2) }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="badge rounded-pill bg-{{ $tx->status === 'completed' ? 'success' : ($tx->status === 'pending' ? 'warning' : 'danger') }}-light">
                                                                {{ ucfirst($tx->status) }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6" class="text-center py-4 text-muted">No transactions found.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Page Content -->

    <script>
        function copyLink(inputId) {
            const copyText = document.getElementById(inputId);
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(copyText.value);
            
            // Temporary alert using browser alert or toastr if loaded
            if (typeof toastr !== 'undefined') {
                toastr.success("Affiliate link copied to clipboard!");
            } else {
                alert("Affiliate link copied: " + copyText.value);
            }
        }
    </script>
@endsection

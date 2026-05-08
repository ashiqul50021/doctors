@extends('layouts.app')

@section('title', 'Checkout - Doccure')

@section('content')

    <!-- Page Content -->
    <div class="content">
        <div class="container">

            <div class="row">
                <div class="col-md-7 col-lg-8">
                    <div class="card">
                        <div class="card-body">

                            <!-- Checkout Form -->
                            <form action="{{ route('booking.payment') }}" method="POST">
                                @csrf
                                <!-- Personal Information -->
                                <div class="info-widget">
                                    <h4 class="card-title">Personal Information</h4>
                                    <div class="row">
                                        <div class="col-md-6 col-sm-12">
                                            <div class="mb-3 card-label">
                                                <label>First Name</label>
                                                <input class="form-control" type="text" name="first_name"
                                                    value="{{ Auth::user()->name ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-12">
                                            <div class="mb-3 card-label">
                                                <label>Last Name</label>
                                                <input class="form-control" type="text" name="last_name">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-12">
                                            <div class="mb-3 card-label">
                                                <label>Email</label>
                                                <input class="form-control" type="email" name="email"
                                                    value="{{ Auth::user()->email ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-12">
                                            <div class="mb-3 card-label">
                                                <label>Phone</label>
                                                <input class="form-control" type="text" name="phone">
                                            </div>
                                        </div>
                                    </div>
                                    @guest
                                        <div class="exist-customer">Existing Customer? <a href="{{ route('login') }}">Click here
                                                to login</a></div>
                                    @endguest
                                </div>
                                <!-- /Personal Information -->

                                @if($booking['type'] === 'online')
                                <div class="payment-widget">
                                    <h4 class="card-title">Payment Method</h4>

                                    <!-- SSLCommerz Payment -->
                                    <div class="payment-list">
                                        <label class="payment-radio credit-card-option">
                                            <input type="radio" name="payment_method" value="sslcommerz" checked>
                                            <span class="checkmark"></span>
                                            SSLCommerz (Credit Card / Mobile Banking)
                                        </label>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <p class="text-muted mt-3 mb-0">You will be redirected to SSLCommerz's secure gateway to complete your payment.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /SSLCommerz Payment -->

                                    <!-- Terms Accept -->
                                    <div class="terms-accept">
                                        <div class="custom-checkbox">
                                            <input type="checkbox" id="terms_accept" required>
                                            <label for="terms_accept">I have read and accept <a href="#">Terms &amp;
                                                    Conditions</a></label>
                                        </div>
                                    </div>
                                    <!-- /Terms Accept -->

                                    <!-- Submit Section -->
                                    <div class="submit-section mt-4">
                                        <button type="submit" class="btn btn-primary submit-btn">Confirm and Pay</button>
                                    </div>
                                    <!-- /Submit Section -->

                                </div>
                                @else
                                <div class="payment-widget">
                                    <div class="terms-accept">
                                        <div class="custom-checkbox">
                                            <input type="checkbox" id="terms_accept" required>
                                            <label for="terms_accept">I have read and accept <a href="#">Terms &amp;
                                                    Conditions</a></label>
                                        </div>
                                    </div>
                                    <div class="submit-section mt-4">
                                        <button type="submit" class="btn btn-primary submit-btn">Confirm Booking</button>
                                    </div>
                                </div>
                                @endif
                            </form>
                            <!-- /Checkout Form -->

                        </div>
                    </div>

                </div>

                <div class="col-md-5 col-lg-4 theiaStickySidebar">

                    <!-- Booking Summary -->
                    <div class="card booking-card">
                        <div class="card-header">
                            <h4 class="card-title">Booking Summary</h4>
                        </div>
                        <div class="card-body">

                            <!-- Booking Doctor Info -->
                            <div class="booking-doc-info">
                                <a href="{{ route('doctors.profile', $doctor->id) }}" class="booking-doc-img">
                                    <img src="{{ $doctor->profile_image ? asset($doctor->profile_image) : asset('assets/img/doctors/doctor-thumb-02.jpg') }}"
                                        alt="User Image">
                                </a>
                                <div class="booking-info">
                                    <h4><a href="{{ route('doctors.profile', $doctor->id) }}">Dr.
                                            {{ $doctor->user->name }}</a></h4>
                                    <div class="rating">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $doctor->average_rating ? 'filled' : '' }}"></i>
                                        @endfor
                                        <span class="d-inline-block average-rating">{{ $doctor->review_count }}</span>
                                    </div>
                                    <div class="clinic-details">
                                        <p class="doc-location"><i class="fas fa-map-marker-alt"></i>
                                            {{ $doctor->clinic_city }}, {{ $doctor->primary_clinic_address }}</p>
                                    </div>
                                </div>
                            </div>
                            <!-- Booking Doctor Info -->

                            <div class="booking-summary">
                                <div class="booking-item-wrap">
                                    <ul class="booking-date">
                                        <li>Date <span>{{ \Carbon\Carbon::parse($booking['date'])->format('d M Y') }}</span>
                                        </li>
                                        <li>Time <span>{{ \Carbon\Carbon::parse($booking['time'])->format('h:i A') }}</span>
                                        </li>
                                        <li>Type <span class="text-uppercase">{{ $booking['type'] }}</span></li>
                                    </ul>
                                    <ul class="booking-fee">
                                        <li>Consulting Fee <span>${{ $booking['fee'] }}</span></li>
                                    </ul>
                                    <div class="booking-total">
                                        <ul class="booking-total-list">
                                            <li>
                                                <span>Total</span>
                                                <span class="total-cost">${{ $booking['fee'] }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Booking Summary -->

                </div>
            </div>

        </div>

    </div>
    <!-- /Page Content -->
@endsection

@push('scripts')
    <!-- Sticky Sidebar JS -->
    <script src="{{ asset('assets/plugins/theia-sticky-sidebar/ResizeSensor.js') }}"></script>
    <script src="{{ asset('assets/plugins/theia-sticky-sidebar/theia-sticky-sidebar.js') }}"></script>
@endpush

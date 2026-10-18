@extends('layouts.app')

@section('title', 'Checkout - abcsheba')

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
                                                    value="{{ old('first_name', Auth::check() ? (explode(' ', Auth::user()->name, 2)[0] ?? '') : '') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-12">
                                            <div class="mb-3 card-label">
                                                <label>Last Name</label>
                                                <input class="form-control" type="text" name="last_name"
                                                    value="{{ old('last_name', Auth::check() ? (explode(' ', Auth::user()->name, 2)[1] ?? '') : '') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-12">
                                            <div class="mb-3 card-label">
                                                <label>Email</label>
                                                <input class="form-control" type="email" name="email"
                                                    value="{{ old('email', Auth::user()->email ?? '') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-12">
                                            <div class="mb-3 card-label">
                                                <label>Phone</label>
                                                <input class="form-control" type="text" name="phone"
                                                    value="{{ old('phone', Auth::check() && Auth::user()->patient ? Auth::user()->patient->phone : '') }}">
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
                                    <h4 class="card-title">Payment Method (মোবাইল ব্যাংকিং)</h4>

                                    <!-- Manual Mobile Banking Options -->
                                    <div class="payment-list mb-3">
                                        <label class="form-label fw-bold text-dark mb-2">পেমেন্ট মেথড বেছে নিন:</label>
                                        <div class="d-flex gap-2 mb-3">
                                            <label class="btn btn-outline-danger flex-fill text-center py-2 payment-radio-btn active" id="chkRadioBkash" style="border-radius: 4px; font-weight: 500; cursor: pointer;">
                                                <input type="radio" name="payment_method" value="bkash" class="d-none" checked onchange="switchCheckoutPayment('bkash')">
                                                <i class="fas fa-wallet me-1"></i> bKash
                                            </label>
                                            <label class="btn btn-outline-warning flex-fill text-center py-2 payment-radio-btn" id="chkRadioNagad" style="border-radius: 4px; font-weight: 500; cursor: pointer;">
                                                <input type="radio" name="payment_method" value="nagad" class="d-none" onchange="switchCheckoutPayment('nagad')">
                                                <i class="fas fa-wallet me-1"></i> Nagad
                                            </label>
                                            @if(!empty($siteSettings['manual_rocket_number']))
                                            <label class="btn btn-outline-primary flex-fill text-center py-2 payment-radio-btn" id="chkRadioRocket" style="border-radius: 4px; font-weight: 500; cursor: pointer;">
                                                <input type="radio" name="payment_method" value="rocket" class="d-none" onchange="switchCheckoutPayment('rocket')">
                                                <i class="fas fa-wallet me-1"></i> Rocket
                                            </label>
                                            @endif
                                        </div>

                                        <!-- Account Number Display Box -->
                                        <div class="p-3 mb-3 rounded" style="background: #F0FDF4; border: 1px dashed #86EFAC;">
                                            <div id="chkBoxBkash">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="text-danger fw-bold"><i class="fas fa-arrow-circle-right me-1"></i> বিকাশ নম্বর:</span>
                                                    <span class="badge bg-danger text-white">{{ $siteSettings['manual_bkash_type'] ?? 'Personal (Send Money)' }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mt-2">
                                                    <strong class="text-dark fs-5">{{ $siteSettings['manual_bkash_number'] ?? '01711111111' }}</strong>
                                                    <button type="button" class="btn btn-sm btn-light border py-1 px-2" onclick="navigator.clipboard.writeText('{{ $siteSettings['manual_bkash_number'] ?? '01711111111' }}'); alert('নম্বর কপি করা হয়েছে!');">
                                                        <i class="far fa-copy text-primary me-1"></i> Copy
                                                    </button>
                                                </div>
                                            </div>

                                            <div id="chkBoxNagad" style="display: none;">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="text-warning fw-bold"><i class="fas fa-arrow-circle-right me-1"></i> নগদ নম্বর:</span>
                                                    <span class="badge bg-warning text-dark">{{ $siteSettings['manual_nagad_type'] ?? 'Personal (Send Money)' }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mt-2">
                                                    <strong class="text-dark fs-5">{{ $siteSettings['manual_nagad_number'] ?? '01811111111' }}</strong>
                                                    <button type="button" class="btn btn-sm btn-light border py-1 px-2" onclick="navigator.clipboard.writeText('{{ $siteSettings['manual_nagad_number'] ?? '01811111111' }}'); alert('নম্বর কপি করা হয়েছে!');">
                                                        <i class="far fa-copy text-primary me-1"></i> Copy
                                                    </button>
                                                </div>
                                            </div>

                                            @if(!empty($siteSettings['manual_rocket_number']))
                                            <div id="chkBoxRocket" style="display: none;">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="text-primary fw-bold"><i class="fas fa-arrow-circle-right me-1"></i> রকেট নম্বর:</span>
                                                    <span class="badge bg-primary text-white">{{ $siteSettings['manual_rocket_type'] ?? 'Personal (Send Money)' }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mt-2">
                                                    <strong class="text-dark fs-5">{{ $siteSettings['manual_rocket_number'] }}</strong>
                                                    <button type="button" class="btn btn-sm btn-light border py-1 px-2" onclick="navigator.clipboard.writeText('{{ $siteSettings['manual_rocket_number'] }}'); alert('নম্বর কপি করা হয়েছে!');">
                                                        <i class="far fa-copy text-primary me-1"></i> Copy
                                                    </button>
                                                </div>
                                            </div>
                                            @endif

                                            <small class="text-muted d-block mt-2">
                                                <i class="fas fa-info-circle text-primary me-1"></i> {{ $siteSettings['manual_payment_instruction'] ?? 'উপরের নম্বরে Send Money করে নিচের বক্সে আপনার প্রেরক নম্বর ও TrxID দিন।' }}
                                            </small>
                                        </div>

                                        <!-- Sender Number and TrxID Inputs -->
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold text-dark">যে নম্বর থেকে টাকা পাঠিয়েছেন (Sender No) <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="sender_number" required placeholder="01XXXXXXXXX" value="{{ old('sender_number') }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold text-dark">Transaction ID (TrxID) <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="transaction_id" required placeholder="যেমন: 9J3K8L2P" value="{{ old('transaction_id') }}">
                                            </div>
                                        </div>
                                    </div>

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
                                        <button type="submit" class="btn btn-primary submit-btn">Confirm Appointment</button>
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
                                    <h4><a href="{{ route('doctors.profile', $doctor->id) }}" style="text-decoration: none;">{{ str_starts_with(strtolower($doctor->user->name), 'dr.') ? $doctor->user->name : 'Dr. ' . $doctor->user->name }}</a></h4>
                                    <div class="rating">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $doctor->average_rating ? 'filled' : '' }}"></i>
                                        @endfor
                                        <span class="d-inline-block average-rating">{{ $doctor->review_count }}</span>
                                    </div>
                                    <div class="clinic-details">
                                        <p class="doc-location"><i class="fas fa-map-marker-alt"></i>
                                            {{ $doctor->clinic_city ? $doctor->clinic_city . ', ' : '' }}{{ $doctor->primary_clinic_address }}</p>
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
                                        <li>Consulting Fee <span>৳{{ $booking['fee'] }}</span></li>
                                    </ul>
                                    <div class="booking-total">
                                        <ul class="booking-total-list">
                                            <li>
                                                <span>Total</span>
                                                <span class="total-cost">৳{{ $booking['fee'] }}</span>
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
    <script>
        function switchCheckoutPayment(method) {
            document.querySelectorAll('#chkRadioBkash, #chkRadioNagad, #chkRadioRocket').forEach(btn => btn?.classList.remove('active'));
            const bkashBox = document.getElementById('chkBoxBkash');
            const nagadBox = document.getElementById('chkBoxNagad');
            const rocketBox = document.getElementById('chkBoxRocket');

            if (bkashBox) bkashBox.style.display = 'none';
            if (nagadBox) nagadBox.style.display = 'none';
            if (rocketBox) rocketBox.style.display = 'none';

            if (method === 'bkash') {
                document.getElementById('chkRadioBkash')?.classList.add('active');
                if (bkashBox) bkashBox.style.display = 'block';
            } else if (method === 'nagad') {
                document.getElementById('chkRadioNagad')?.classList.add('active');
                if (nagadBox) nagadBox.style.display = 'block';
            } else if (method === 'rocket') {
                document.getElementById('chkRadioRocket')?.classList.add('active');
                if (rocketBox) rocketBox.style.display = 'block';
            }
        }
    </script>
@endpush

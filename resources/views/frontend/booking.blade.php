@extends('layouts.app')

@section('title', 'Book Appointment - ' . ($doctor->user->name ?? 'abcsheba'))

@section('content')

    <!-- Custom Modern Booking Styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/booking-modern.css') }}">

    <!-- Page Content -->
    <div class="content py-4">
        <div class="container">

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Doctor Header Info Card -->
            <div class="card mb-4 border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div class="booking-doc-info">
                            <a href="{{ route('doctors.profile', $doctor->id) }}" class="booking-doc-img">
                                <img src="{{ $doctor->profile_image ? asset($doctor->profile_image) : asset('assets/img/doctors/doctor-thumb-02.jpg') }}"
                                    alt="Doctor Image" onerror="this.src='{{ asset('assets/img/doctors/doctor-thumb-02.jpg') }}'">
                            </a>
                            <div class="booking-info">
                                <h4 class="mb-1">
                                    <a href="{{ route('doctors.profile', $doctor->id) }}">
                                        {{ str_starts_with(strtolower($doctor->user->name ?? ''), 'dr.') ? $doctor->user->name : 'Dr. ' . ($doctor->user->name ?? '') }}
                                    </a>
                                </h4>
                                <p class="text-primary fw-semibold mb-1" style="font-size: 14px;">
                                    <i class="fas fa-stethoscope me-1"></i> {{ $doctor->speciality->name ?? 'General Physician' }}
                                </p>
                                <div class="rating mb-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= ($doctor->average_rating ?? 5) ? 'filled text-warning' : 'text-muted' }}"></i>
                                    @endfor
                                    <span class="d-inline-block average-rating text-muted">({{ $doctor->review_count ?? 0 }} reviews)</span>
                                </div>
                                <p class="text-muted mb-0 small">
                                    <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                    {{ $doctor->clinic_city ? $doctor->clinic_city . ', ' : '' }}{{ $doctor->primary_clinic_address ?: 'Chamber Address Available' }}
                                </p>
                            </div>
                        </div>

                        <div class="d-flex flex-column align-items-end justify-content-center text-end">
                            <div class="d-flex gap-2">
                                <span class="badge badge-fee badge-fee-offline">
                                    <i class="fas fa-building me-1"></i> Chamber: ৳{{ $doctor->consultation_fee ?? 0 }}
                                </span>
                                @if(($doctor->online_fee ?? 0) > 0)
                                <span class="badge badge-fee badge-fee-online">
                                    <i class="fas fa-video me-1"></i> Online: ৳{{ $doctor->online_fee }}
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking Main Layout (2-Column Form & Live Sticky Summary) -->
            <form action="{{ route('booking.submit', $doctor->id) }}" method="POST" id="mainBookingForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="appointment_date" id="appointment_date" value="{{ old('appointment_date') }}">
                <input type="hidden" name="appointment_time" id="appointment_time" value="{{ old('appointment_time') }}">

                <div class="row g-4">
                    <!-- Left Column: Step 1 (Type), Step 2 (Slot), Step 3 (Patient Info) -->
                    <div class="col-lg-8">

                        <!-- Step 1: Appointment Type -->
                        <div class="card mb-4 border-0 shadow-sm" style="border-radius: 16px;">
                            <div class="card-body p-4">
                                <h5 class="card-title mb-3">
                                    <span class="badge bg-primary text-white me-2 rounded-circle px-2">1</span> 
                                    Choose Appointment Type
                                </h5>
                                <div class="appointment-type-group">
                                    <!-- In-Clinic Visit -->
                                    <div class="appointment-option">
                                        <input class="appointment-type-radio" type="radio" name="type" id="type_offline"
                                            value="offline" {{ old('type', 'offline') === 'offline' ? 'checked' : '' }}
                                            data-fee="{{ $doctor->consultation_fee ?? 0 }}"
                                            data-type-label="In-Clinic Chamber Visit">
                                        <label class="appointment-type-label" for="type_offline">
                                            <div class="type-icon"><i class="fas fa-building text-primary"></i></div>
                                            <div class="d-flex flex-column">
                                                <span class="type-text">In-Clinic Visit</span>
                                                <small class="text-muted">Pay at Chamber (৳{{ $doctor->consultation_fee ?? 0 }})</small>
                                            </div>
                                        </label>
                                    </div>

                                    <!-- Online Video Consultation -->
                                    <div class="appointment-option">
                                        <input class="appointment-type-radio" type="radio" name="type" id="type_online"
                                            value="online" {{ old('type') === 'online' ? 'checked' : '' }}
                                            data-fee="{{ $doctor->online_fee ?? $doctor->consultation_fee ?? 0 }}"
                                            data-type-label="Online Video Consultation">
                                        <label class="appointment-type-label" for="type_online">
                                            <div class="type-icon"><i class="fas fa-video text-success"></i></div>
                                            <div class="d-flex flex-column">
                                                <span class="type-text">Video Consultation</span>
                                                <small class="text-muted">Instant Video Link (৳{{ $doctor->online_fee ?? $doctor->consultation_fee ?? 0 }})</small>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Date & Time Slot Selection -->
                        @if(count($dates) > 0)
                        <div class="card mb-4 border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="card-title mb-0">
                                        <span class="badge bg-primary text-white me-2 rounded-circle px-2">2</span> 
                                        Select Date & Available Slot
                                    </h5>
                                    <span class="text-muted small"><i class="fas fa-mouse-pointer me-1"></i> Click a slot pill to choose</span>
                                </div>

                                <!-- Schedule Header (7 Days) -->
                                <div class="schedule-header rounded-3 mb-3">
                                    <div class="day-slot">
                                        <ul class="d-flex justify-content-between">
                                            @foreach($dates as $date)
                                                <li>
                                                    <span>{{ $date->format('D') }}</span>
                                                    <span class="slot-date">{{ $date->format('d M') }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>

                                <!-- Chamber (Offline) Schedule Slots -->
                                <div class="schedule-cont" id="offline-schedule-container">
                                    <div class="time-slot p-0">
                                        <ul class="clearfix d-flex">
                                            @foreach($dates as $date)
                                                <li>
                                                    @php
                                                        $dateKey = $date->format('Y-m-d');
                                                        $daySlots = $subdividedSlots['offline'][$dateKey] ?? [];
                                                    @endphp

                                                    @if(count($daySlots) > 0)
                                                        @foreach($daySlots as $slot)
                                                            @if($slot['is_booked'])
                                                                <a class="timing disabled" href="javascript:void(0)" title="Already Booked">
                                                                    <span>{{ $slot['display'] }}</span>
                                                                    <small style="font-size: 10px; display:block; color:#dc2626;">Booked</small>
                                                                </a>
                                                            @else
                                                                <a class="timing"
                                                                   href="javascript:void(0)"
                                                                   onclick="selectSlot(this, '{{ $dateKey }}', '{{ $slot['time_key'] }}', '{{ $date->format('D, d M Y') }}', '{{ $slot['display'] }}')">
                                                                    <span>{{ $slot['display'] }}</span>
                                                                </a>
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        <a class="timing disabled" href="javascript:void(0)">
                                                            <span>Off Day</span>
                                                        </a>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>

                                <!-- Online Video Schedule Slots -->
                                <div class="schedule-cont" id="online-schedule-container" style="display: none;">
                                    <div class="time-slot p-0">
                                        <ul class="clearfix d-flex">
                                            @foreach($dates as $date)
                                                <li>
                                                    @php
                                                        $dateKey = $date->format('Y-m-d');
                                                        $daySlots = $subdividedSlots['online'][$dateKey] ?? [];
                                                    @endphp

                                                    @if(count($daySlots) > 0)
                                                        @foreach($daySlots as $slot)
                                                            @if($slot['is_booked'])
                                                                <a class="timing disabled" href="javascript:void(0)" title="Already Booked">
                                                                    <span>{{ $slot['display'] }}</span>
                                                                    <small style="font-size: 10px; display:block; color:#dc2626;">Booked</small>
                                                                </a>
                                                            @else
                                                                <a class="timing"
                                                                   href="javascript:void(0)"
                                                                   onclick="selectSlot(this, '{{ $dateKey }}', '{{ $slot['time_key'] }}', '{{ $date->format('D, d M Y') }}', '{{ $slot['display'] }}')">
                                                                    <span>{{ $slot['display'] }}</span>
                                                                </a>
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        <a class="timing disabled" href="javascript:void(0)">
                                                            <span>Off Day</span>
                                                        </a>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>


                                <!-- Selected Slot Visual Indicator -->
                                <div id="selectedSlotIndicator" class="selected-slot-banner mt-3" style="display: none;">
                                    <div class="slot-info">
                                        <div class="slot-icon"><i class="far fa-calendar-check"></i></div>
                                        <div class="slot-text">
                                            <strong id="selectedSlotDateDisplay">Date Selected</strong>
                                            <span id="selectedSlotTimeDisplay">Time slot</span>
                                        </div>
                                    </div>
                                    <span class="badge bg-success text-white px-3 py-2"><i class="fas fa-check me-1"></i> Slot Reserved</span>
                                </div>

                            </div>
                        </div>
                        @else
                        <div class="card mb-4 border-0 shadow-sm" style="border-radius: 16px;">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">এই ডাক্তারের বর্তমানে কোনো available date নেই</h5>
                                <p class="text-muted mb-0">ডাক্তার এখনো schedule সেট করেননি অথবা আগামী দিনগুলোতে off day আছে। অনুগ্রহ করে পরে চেষ্টা করুন।</p>
                            </div>
                        </div>
                        @endif

                        <!-- Step 3: Patient Information Form -->
                        <div class="card mb-4 border-0 shadow-sm" style="border-radius: 16px;">
                            <div class="card-body p-4">
                                <h5 class="card-title mb-3">
                                    <span class="badge bg-primary text-white me-2 rounded-circle px-2">3</span> 
                                    Patient Information
                                </h5>

                                <div class="patient-info-box">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Patient Full Name <span class="text-danger">*</span></label>
                                            <input type="text" name="patient_name" class="form-control" placeholder="যেমন: মো: করিম উদ্দিন"
                                                value="{{ old('patient_name', Auth::check() ? Auth::user()->name : '') }}" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Phone Number (Mobile) <span class="text-danger">*</span></label>
                                            <input type="tel" name="phone" id="patient_phone" class="form-control" placeholder="01XXXXXXXXX"
                                                value="{{ old('phone', Auth::check() && Auth::user()->patient ? Auth::user()->patient->phone : '') }}" required>
                                            <small class="text-muted">সঠিক মোবাইল নম্বর দিন, বুকিং কনফার্মেশন ও টোকেন যাবে।</small>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Email Address <span class="text-muted small">(Optional)</span></label>
                                            <input type="email" name="email" class="form-control" placeholder="name@example.com"
                                                value="{{ old('email', Auth::check() ? Auth::user()->email : '') }}">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Problem / Reason <span class="text-muted small">(Optional)</span></label>
                                            <input type="text" name="reason" class="form-control" placeholder="যেমন: বুক ব্যথা, মাথা ব্যথা, নিয়মিত চেকআপ"
                                                value="{{ old('reason') }}">
                                        </div>

                                        <!-- Previous Prescription / Report Upload -->
                                        <div class="col-12">
                                            <label class="form-label d-flex justify-content-between align-items-center">
                                                <span><i class="fas fa-file-medical text-primary me-1"></i> আগের প্রেসক্রিপশন বা টেস্ট রিপোর্ট <span class="text-muted small">(Optional)</span></span>
                                                <span class="badge bg-light text-muted border">JPG, PNG, PDF (Max 5MB)</span>
                                            </label>
                                            <div class="report-upload-box p-3 border rounded text-center" style="background: #f8fafc; border-style: dashed !important; border-color: #cbd5e1 !important;">
                                                <input type="file" name="reports[]" id="report_files" class="d-none" multiple accept=".jpg,.jpeg,.png,.webp,.pdf">
                                                <label for="report_files" class="mb-0 w-100" style="cursor: pointer;">
                                                    <div class="py-2">
                                                        <i class="fas fa-cloud-upload-alt text-primary mb-2" style="font-size: 28px;"></i>
                                                        <div class="fw-semibold text-dark">ক্লিক করে ছবি বা PDF ফাইল সিলেক্ট করুন</div>
                                                        <small class="text-muted d-block mt-1">আগের প্রেসক্রিপশন বা ল্যাব টেস্ট রিপোর্ট যুক্ত করতে পারেন (ডাক্তার সরাসরি দেখতে পাবেন)</small>
                                                    </div>
                                                </label>
                                                <div id="file_preview_list" class="d-flex flex-wrap gap-2 justify-content-center mt-2"></div>
                                            </div>
                                        </div>
                                    </div>

                                    @guest
                                    <div class="mt-3 p-2 rounded bg-light border text-muted small d-flex align-items-center justify-content-between">
                                        <span><i class="fas fa-info-circle text-primary me-1"></i> একাউন্ট ছাড়াই সহজে বুকিং করতে পারবেন।</span>
                                        <span>ইতিমধ্যে একাউন্ট আছে? <a href="{{ route('login') }}" class="text-primary fw-semibold">লগইন করুন</a></span>
                                    </div>
                                    @endguest
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Right Column: Live Sticky Summary & Instant Confirmation -->
                    <div class="col-lg-4">
                        <div class="booking-summary-sidebar">
                            <div class="booking-summary-card">
                                <div class="summary-header">
                                    <i class="fas fa-file-invoice me-1"></i> Booking Summary
                                </div>
                                <div class="summary-body">
                                    <div class="summary-row">
                                        <span>Doctor</span>
                                        <strong class="text-dark">{{ str_starts_with(strtolower($doctor->user->name ?? ''), 'dr.') ? $doctor->user->name : 'Dr. ' . ($doctor->user->name ?? '') }}</strong>
                                    </div>
                                    <div class="summary-row">
                                        <span>Appointment Type</span>
                                        <span id="summaryApptType" class="badge bg-primary text-white">In-Clinic Visit</span>
                                    </div>
                                    <div class="summary-row">
                                        <span>Date</span>
                                        <span id="summaryDate" class="val-highlight">Please select slot</span>
                                    </div>
                                    <div class="summary-row">
                                        <span>Time</span>
                                        <span id="summaryTime" class="text-muted">-</span>
                                    </div>
                                    <div class="summary-row">
                                        <span>Consultation Fee</span>
                                        <span id="summaryFee" class="fw-bold">৳{{ $doctor->consultation_fee ?? 0 }}</span>
                                    </div>
                                    <div class="summary-row total-row">
                                        <span>Total Payable</span>
                                        <span id="summaryTotal" class="text-primary">৳{{ $doctor->consultation_fee ?? 0 }}</span>
                                    </div>

                                    <!-- Payment Mode Note -->
                                    <div id="paymentModeNote" class="mt-3 p-3 rounded bg-light border text-center small text-muted">
                                        <i class="fas fa-hand-holding-usd text-success me-1"></i> 
                                        <strong>Pay at Chamber:</strong> ফি চেম্বারে উপস্থিত হয়ে সরাসরি প্রদান করতে পারবেন।
                                    </div>

                                    <!-- Online Payment Options (Shown only for online) -->
                                    <div id="onlinePaymentOptions" class="mt-3" style="display: none;">
                                        <div class="form-check p-0">
                                            <div class="payment-method-card selected">
                                                <div class="method-left">
                                                    <input class="form-check-input ms-0 me-2" type="radio" name="payment_method" id="pay_sslcommerz" value="sslcommerz" checked>
                                                    <label class="form-check-label mb-0 fw-semibold" for="pay_sslcommerz">
                                                        Online Pay (bKash / Nagad / Card)
                                                    </label>
                                                </div>
                                                <i class="fas fa-shield-alt text-primary method-icon"></i>
                                            </div>
                                        </div>
                                        <small class="text-muted d-block text-center mb-2">সুরক্ষিত পেমেন্ট গেটওয়ের মাধ্যমে নিরাপদে পেমেন্ট করুন।</small>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="mt-4">
                                        <button type="submit" id="btnConfirmBooking" class="btn btn-primary submit-btn w-100 py-3 d-flex align-items-center justify-content-center gap-2" disabled>
                                            <i class="fas fa-check-circle"></i>
                                            <span id="submitBtnText">Select a Time Slot First</span>
                                        </button>
                                    </div>

                                    <div class="mt-3 text-center">
                                        <small class="text-muted">
                                            <i class="fas fa-lock text-success me-1"></i> 100% Safe & Confidential
                                        </small>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </form>

        </div>
    </div>

    <!-- Booking Scripts -->
    <script>
        let selectedDate = null;
        let selectedTime = null;

        function selectSlot(element, dateKey, timeVal, formattedDate, formattedTime) {
            // Remove previous selections
            document.querySelectorAll('.timing').forEach(el => el.classList.remove('selected'));
            element.classList.add('selected');

            // Store values
            selectedDate = dateKey;
            selectedTime = timeVal;
            document.getElementById('appointment_date').value = dateKey;
            document.getElementById('appointment_time').value = timeVal;

            // Update banner
            const banner = document.getElementById('selectedSlotIndicator');
            if (banner) {
                banner.style.display = 'flex';
                document.getElementById('selectedSlotDateDisplay').textContent = formattedDate;
                document.getElementById('selectedSlotTimeDisplay').textContent = formattedTime;
            }

            // Update summary
            document.getElementById('summaryDate').textContent = formattedDate;
            document.getElementById('summaryTime').textContent = formattedTime;

            // Enable submit button
            validateAndToggleButton();
        }

        function validateAndToggleButton() {
            const btn = document.getElementById('btnConfirmBooking');
            const btnText = document.getElementById('submitBtnText');
            const typeOffline = document.getElementById('type_offline');

            if (!selectedDate || !selectedTime) {
                btn.disabled = true;
                btnText.textContent = 'Select a Time Slot First';
                return;
            }

            btn.disabled = false;
            if (typeOffline && typeOffline.checked) {
                btnText.textContent = 'Confirm Appointment (Pay at Chamber)';
            } else {
                btnText.textContent = 'Proceed to Pay & Confirm';
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const typeOffline = document.getElementById('type_offline');
            const typeOnline = document.getElementById('type_online');
            const offlineContainer = document.getElementById('offline-schedule-container');
            const onlineContainer = document.getElementById('online-schedule-container');
            const summaryApptType = document.getElementById('summaryApptType');
            const summaryFee = document.getElementById('summaryFee');
            const summaryTotal = document.getElementById('summaryTotal');
            const paymentModeNote = document.getElementById('paymentModeNote');
            const onlinePaymentOptions = document.getElementById('onlinePaymentOptions');

            function updateTypeView() {
                const isOnline = typeOnline && typeOnline.checked;

                if (isOnline) {
                    if (offlineContainer) offlineContainer.style.display = 'none';
                    if (onlineContainer) onlineContainer.style.display = 'block';
                    if (summaryApptType) {
                        summaryApptType.textContent = 'Video Consultation';
                        summaryApptType.className = 'badge bg-success text-white';
                    }
                    const fee = typeOnline.getAttribute('data-fee') || '0';
                    if (summaryFee) summaryFee.textContent = '৳' + fee;
                    if (summaryTotal) summaryTotal.textContent = '৳' + fee;
                    if (paymentModeNote) paymentModeNote.style.display = 'none';
                    if (onlinePaymentOptions) onlinePaymentOptions.style.display = 'block';
                } else {
                    if (offlineContainer) offlineContainer.style.display = 'block';
                    if (onlineContainer) onlineContainer.style.display = 'none';
                    if (summaryApptType) {
                        summaryApptType.textContent = 'In-Clinic Visit';
                        summaryApptType.className = 'badge bg-primary text-white';
                    }
                    const fee = (typeOffline ? typeOffline.getAttribute('data-fee') : '0') || '0';
                    if (summaryFee) summaryFee.textContent = '৳' + fee;
                    if (summaryTotal) summaryTotal.textContent = '৳' + fee;
                    if (paymentModeNote) paymentModeNote.style.display = 'block';
                    if (onlinePaymentOptions) onlinePaymentOptions.style.display = 'none';
                }

                // Reset selected slots when switching between offline and online
                document.querySelectorAll('.timing').forEach(el => el.classList.remove('selected'));
                document.getElementById('appointment_date').value = '';
                document.getElementById('appointment_time').value = '';
                selectedDate = null;
                selectedTime = null;

                const banner = document.getElementById('selectedSlotIndicator');
                if (banner) banner.style.display = 'none';
                document.getElementById('summaryDate').textContent = 'Please select slot';
                document.getElementById('summaryTime').textContent = '-';

                validateAndToggleButton();
            }

            if (typeOffline && typeOnline) {
                typeOffline.addEventListener('change', updateTypeView);
                typeOnline.addEventListener('change', updateTypeView);
                updateTypeView();
            }

            // Client side validation on submit
            const form = document.getElementById('mainBookingForm');
            if (form) {
                form.addEventListener('submit', function (e) {
                    if (!selectedDate || !selectedTime) {
                        e.preventDefault();
                        alert('অনুগ্রহ করে একটি অ্যাপয়েন্টমেন্ট তারিখ ও সময় স্লট নির্বাচন করুন।');
                        return false;
                    }
                });
            }

            // Preview selected report files
            const reportInput = document.getElementById('report_files');
            const previewList = document.getElementById('file_preview_list');
            if (reportInput && previewList) {
                reportInput.addEventListener('change', function() {
                    previewList.innerHTML = '';
                    if (this.files && this.files.length > 0) {
                        Array.from(this.files).forEach(file => {
                            const sizeKb = (file.size / 1024).toFixed(1);
                            const pill = document.createElement('span');
                            pill.className = 'badge bg-white text-dark border px-2 py-1 shadow-sm d-inline-flex align-items-center gap-1';
                            pill.innerHTML = `<i class="fas fa-file-medical text-primary"></i> ${file.name} <small class="text-muted">(${sizeKb} KB)</small>`;
                            previewList.appendChild(pill);
                        });
                    }
                });
            }
        });
    </script>
@endsection


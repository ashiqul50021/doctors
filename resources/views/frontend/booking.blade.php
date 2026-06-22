@extends('layouts.app')

@section('title', 'Booking - abcsheba')

@section('content')

    <!-- Custom Premium Booking Styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/booking-modern.css') }}">
    <script>
        function selectSlot(element, date, time) {
            // Remove selected class from all slots
            document.querySelectorAll('.timing').forEach(el => el.classList.remove('selected'));
            // Add selected class to clicked slot
            element.classList.add('selected');
            // Set hidden inputs
            document.getElementById('appointment_date').value = date;
            document.getElementById('appointment_time').value = time;
        }
    </script>

    <!-- Page Content -->
    <div class="content">
        <div class="container">

            <div class="row">
                <div class="col-12">

                    <!-- Doctor Info Card -->
                    <div class="card">
                        <div class="card-body">
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
                                        <span class="d-inline-block average-rating">({{ $doctor->review_count }})</span>
                                    </div>
                                    <p class="text-muted mb-0"><i class="fas fa-map-marker-alt"></i>
                                        {{ $doctor->clinic_city ? $doctor->clinic_city . ', ' : '' }}{{ $doctor->primary_clinic_address }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Form -->
                    <form action="{{ route('booking.submit', $doctor->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="appointment_date" id="appointment_date">
                        <input type="hidden" name="appointment_time" id="appointment_time">

                        <!-- Appointment Type -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h4 class="card-title">Choose Appointment Type</h4>
                                <div class="appointment-type-group">
                                    <!-- Offline Option -->
                                    <div class="appointment-option">
                                        <input class="appointment-type-radio" type="radio" name="type" id="type_offline"
                                            value="offline" checked>
                                        <label class="appointment-type-label" for="type_offline">
                                            <div class="type-icon"><i class="fas fa-building"></i></div>
                                            <span class="type-text">In-Clinic Visit</span>
                                        </label>
                                    </div>

                                    <!-- Online Option -->
                                    <div class="appointment-option">
                                        <input class="appointment-type-radio" type="radio" name="type" id="type_online"
                                            value="online">
                                        <label class="appointment-type-label" for="type_online">
                                            <div class="type-icon"><i class="fas fa-video"></i></div>
                                            <span class="type-text">Video Consultation</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if(count($dates) > 0)
                        <!-- Schedule Widget -->
                        <div class="card booking-schedule schedule-widget">

                            <!-- Schedule Header -->
                            <div class="schedule-header">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="day-slot">
                                            <ul>
                                                @foreach($dates as $date)
                                                    <li>
                                                        <span>{{ $date->format('D') }}</span>
                                                        <span class="slot-date">{{ $date->format('d M') }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /Schedule Header -->

                            <!-- Chamber (Offline) Schedule Content -->
                            <div class="schedule-cont" id="offline-schedule-container">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="time-slot">
                                            <ul class="clearfix">
                                                @foreach($dates as $date)
                                                    <li>
                                                        @php
                                                            $dayName = strtolower($date->format('l'));
                                                            $daySchedules = $doctor->schedules->where('day', $dayName)->where('type', 'offline');
                                                            $dateKey = $date->format('Y-m-d');
                                                        @endphp

                                                        @if($daySchedules->count() > 0)
                                                            @foreach($daySchedules as $daySchedule)
                                                                @php
                                                                    $startTime = \Carbon\Carbon::parse($daySchedule->start_time)->format('g:i A');
                                                                    $endTime = \Carbon\Carbon::parse($daySchedule->end_time)->format('g:i A');
                                                                    $isBooked = isset($bookedSlotsOffline[$dateKey]) && in_array($daySchedule->start_time, $bookedSlotsOffline[$dateKey]);
                                                                @endphp
                                                                @if($isBooked)
                                                                    <a class="timing disabled" href="javascript:void(0)" title="Already Booked">
                                                                        <span>{{ $startTime }} - {{ $endTime }} (Booked)</span>
                                                                    </a>
                                                                @else
                                                                    <a class="timing"
                                                                       href="javascript:void(0)"
                                                                       onclick="selectSlot(this, '{{ $dateKey }}', '{{ $daySchedule->start_time }}')">
                                                                        <span>{{ $startTime }} - {{ $endTime }}</span>
                                                                    </a>
                                                                @endif
                                                            @endforeach
                                                        @else
                                                            <a class="timing disabled" href="javascript:void(0)">
                                                                <span>Closed</span>
                                                            </a>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /Chamber Schedule Content -->

                            <!-- Video (Online) Schedule Content -->
                            <div class="schedule-cont" id="online-schedule-container" style="display: none;">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="time-slot">
                                            <ul class="clearfix">
                                                @foreach($dates as $date)
                                                    <li>
                                                        @php
                                                            $dayName = strtolower($date->format('l'));
                                                            $daySchedules = $doctor->schedules->where('day', $dayName)->where('type', 'online');
                                                            $dateKey = $date->format('Y-m-d');
                                                        @endphp

                                                        @if($daySchedules->count() > 0)
                                                            @foreach($daySchedules as $daySchedule)
                                                                @php
                                                                    $startTime = \Carbon\Carbon::parse($daySchedule->start_time)->format('g:i A');
                                                                    $endTime = \Carbon\Carbon::parse($daySchedule->end_time)->format('g:i A');
                                                                    $isBooked = isset($bookedSlotsOnline[$dateKey]) && in_array($daySchedule->start_time, $bookedSlotsOnline[$dateKey]);
                                                                @endphp
                                                                @if($isBooked)
                                                                    <a class="timing disabled" href="javascript:void(0)" title="Already Booked">
                                                                        <span>{{ $startTime }} - {{ $endTime }} (Booked)</span>
                                                                    </a>
                                                                @else
                                                                    <a class="timing"
                                                                       href="javascript:void(0)"
                                                                       onclick="selectSlot(this, '{{ $dateKey }}', '{{ $daySchedule->start_time }}')">
                                                                        <span>{{ $startTime }} - {{ $endTime }}</span>
                                                                    </a>
                                                                @endif
                                                            @endforeach
                                                        @else
                                                            <a class="timing disabled" href="javascript:void(0)">
                                                                <span>Closed</span>
                                                            </a>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /Video Schedule Content -->

                        </div>
                        <!-- /Schedule Widget -->

                        <!-- Submit Section -->
                        <div class="submit-section proceed-btn text-end">
                            <button type="submit" class="btn btn-primary submit-btn">
                                <span id="submit-btn-text">Proceed to Confirm Appointment</span>
                                <i class="fas fa-chevron-right ms-2"></i>
                            </button>
                        </div>
                        <!-- /Submit Section -->

                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                const btnText = document.getElementById('submit-btn-text');
                                const typeOffline = document.getElementById('type_offline');
                                const typeOnline = document.getElementById('type_online');
                                const offlineContainer = document.getElementById('offline-schedule-container');
                                const onlineContainer = document.getElementById('online-schedule-container');

                                function updateBtn() {
                                    if (typeOffline && typeOffline.checked) {
                                        btnText.textContent = 'Proceed to Confirm Appointment';
                                        if (offlineContainer) offlineContainer.style.display = 'block';
                                        if (onlineContainer) onlineContainer.style.display = 'none';
                                    } else if (typeOnline && typeOnline.checked) {
                                        btnText.textContent = 'Proceed to Pay';
                                        if (offlineContainer) offlineContainer.style.display = 'none';
                                        if (onlineContainer) onlineContainer.style.display = 'block';
                                    }

                                    // Reset active slot selections and hidden fields when type switches
                                    document.querySelectorAll('.timing').forEach(el => el.classList.remove('selected'));
                                    document.getElementById('appointment_date').value = '';
                                    document.getElementById('appointment_time').value = '';
                                }

                                if (typeOffline && typeOnline) {
                                    typeOffline.addEventListener('change', updateBtn);
                                    typeOnline.addEventListener('change', updateBtn);
                                    updateBtn();
                                }
                            });
                        </script>
                        @else
                        <!-- No Available Dates -->
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">এই ডাক্তারের কোনো available date নেই</h5>
                                <p class="text-muted mb-0">ডাক্তার এখনো schedule সেট করেননি অথবা আগামী দিনগুলোতে off day আছে। পরে আবার চেষ্টা করুন।</p>
                            </div>
                        </div>
                        @endif
                    </form>

                </div>
            </div>
        </div>

    </div>

    </div>
    </div>
    </div>

    </div>
    <!-- /Page Content -->
@endsection

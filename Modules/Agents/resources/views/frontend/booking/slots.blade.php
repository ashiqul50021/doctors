@extends('layouts.app')

@section('title', 'Select Slot & Book - ' . ($siteSettings['site_name'] ?? 'abcsheba'))

@section('content')
    <!-- Custom Premium Booking Styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/booking-modern.css') }}">
    <script>
        function selectSlot(element, date, time) {
            document.querySelectorAll('.timing').forEach(el => el.classList.remove('selected'));
            element.classList.add('selected');
            document.getElementById('appointment_date').value = date;
            document.getElementById('appointment_time').value = time;
        }
    </script>

    <!-- Breadcrumb -->
    <div class="breadcrumb-bar">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-12 col-12">
                    <nav aria-label="breadcrumb" class="page-breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('agent.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('agent.book-appointment') }}">Book Appointment</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Select Slot</li>
                        </ol>
                    </nav>
                    <h2 class="breadcrumb-title">Appointment Slot & Patient Info</h2>
                </div>
            </div>
        </div>
    </div>
    <!-- /Breadcrumb -->
 
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-md-5 col-lg-4 col-xl-3 theiaStickySidebar">
                    @include('agents::frontend.includes.agent-sidebar')
                </div>

                <div class="col-md-7 col-lg-8 col-xl-9">
                    <!-- Doctor Header card -->
                    <div class="card mb-4 shadow-sm">
                        <div class="card-body">
                            <div class="booking-doc-info">
                                <a href="#" class="booking-doc-img">
                                    <img src="{{ $doctor->profile_image ? asset($doctor->profile_image) : asset('assets/img/doctors/doctor-thumb-02.jpg') }}" alt="Doctor Image">
                                </a>
                                <div class="booking-info">
                                    <h4>Dr. {{ $doctor->user->name }}</h4>
                                    <p class="text-primary font-weight-bold mb-1">{{ $doctor->speciality->name ?? 'General Practitioner' }}</p>
                                    <p class="text-muted mb-0"><i class="fas fa-map-marker-alt"></i> {{ $doctor->clinic_city }}, {{ $doctor->primary_clinic_address }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('agent.booking.submit', $doctor->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="appointment_date" id="appointment_date" required>
                        <input type="hidden" name="appointment_time" id="appointment_time" required>

                        <!-- Choice of Appt Type -->
                        <div class="card mb-4 shadow-sm">
                            <div class="card-body">
                                <h4 class="card-title font-weight-bold mb-3">1. Appointment Type</h4>
                                <div class="appointment-type-group">
                                    <div class="appointment-option">
                                        <input class="appointment-type-radio" type="radio" name="type" id="type_offline" value="offline" checked>
                                        <label class="appointment-type-label" for="type_offline">
                                            <div class="type-icon"><i class="fas fa-building"></i></div>
                                            <span class="type-text">In-Clinic (৳{{ number_format($doctor->consultation_fee, 2) }})</span>
                                        </label>
                                    </div>
                                    <div class="appointment-option">
                                        <input class="appointment-type-radio" type="radio" name="type" id="type_online" value="online">
                                        <label class="appointment-type-label" for="type_online">
                                            <div class="type-icon"><i class="fas fa-video"></i></div>
                                            <span class="type-text">Video Consult (৳{{ number_format($doctor->online_fee ?? $doctor->consultation_fee, 2) }})</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Schedule Calendar -->
                        <div class="card mb-4 shadow-sm schedule-widget">
                            <div class="card-body">
                                <h4 class="card-title font-weight-bold mb-3">2. Available Schedule Slots</h4>
                                @if(count($dates) > 0)
                                    <div class="schedule-header">
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
                                    <div class="schedule-cont">
                                        <div class="time-slot">
                                            <ul class="clearfix">
                                                @foreach($dates as $date)
                                                    <li>
                                                        @php
                                                            $dayName = strtolower($date->format('l'));
                                                            $daySchedule = $doctor->schedules->where('day', $dayName)->first();
                                                        @endphp

                                                        @if($daySchedule)
                                                            @php
                                                                $startTime = \Carbon\Carbon::parse($daySchedule->start_time)->format('g:i A');
                                                                $endTime = \Carbon\Carbon::parse($daySchedule->end_time)->format('g:i A');
                                                                $dateKey = $date->format('Y-m-d');
                                                            @endphp

                                                            <a class="timing" href="javascript:void(0)"
                                                               onclick="selectSlot(this, '{{ $date->format('Y-m-d') }}', '{{ $daySchedule->start_time }}')">
                                                                <span>{{ $startTime }} - {{ $endTime }}</span>
                                                            </a>
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
                                @else
                                    <div class="text-center py-4 text-muted">
                                        <i class="fas fa-calendar-times fa-2x mb-2"></i>
                                        <p class="mb-0">No slots available for the next 30 days.</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Patient details card -->
                        <div class="card mb-4 shadow-sm">
                            <div class="card-body">
                                <h4 class="card-title font-weight-bold mb-3">3. Patient Information</h4>
                                <div class="row">
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group mb-3">
                                            <label class="form-label font-weight-bold text-muted small">Patient Name <span class="text-danger">*</span></label>
                                            <input type="text" name="patient_name" class="form-control" value="{{ old('patient_name') }}" required placeholder="e.g. Charlene Reed">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group mb-3">
                                            <label class="form-label font-weight-bold text-muted small">Patient Email <span class="text-danger">*</span></label>
                                            <input type="email" name="patient_email" class="form-control" value="{{ old('patient_email') }}" required placeholder="e.g. patient@gmail.com">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group mb-3">
                                            <label class="form-label font-weight-bold text-muted small">Patient Phone/Mobile <span class="text-danger">*</span></label>
                                            <input type="text" name="patient_phone" class="form-control" value="{{ old('patient_phone') }}" required placeholder="e.g. 01711111111">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <label class="form-label font-weight-bold text-muted small">Reason for Visit (Optional)</label>
                                            <textarea name="reason" class="form-control" rows="3" placeholder="Symptoms, notes, etc.">{{ old('reason') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Booking Button -->
                        <div class="text-end mb-4">
                            <button type="submit" class="btn btn-primary submit-btn px-4 py-2 font-weight-bold">
                                Confirm & Book Appointment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

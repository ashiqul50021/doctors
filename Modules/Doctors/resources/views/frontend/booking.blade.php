@extends('layouts.app')

@section('title', 'Booking - Doccure')

@section('content')

<!-- Page Content -->
<div class="content">
    <div class="container">

        <div class="row">
            <div class="col-12">

                <div class="card">
                    <div class="card-body">
                        <div class="booking-doc-info">
                            <a href="{{ route('doctors.profile', $doctor->id) }}" class="booking-doc-img">
                                <img src="{{ $doctor->profile_image ? asset('storage/'.$doctor->profile_image) : asset('assets/img/doctors/doctor-thumb-02.jpg') }}" alt="User Image">
                            </a>
                            <div class="booking-info">
                                <h4><a href="{{ route('doctors.profile', $doctor->id) }}">Dr. {{ $doctor->user->name }}</a></h4>
                                <div class="rating">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $doctor->average_rating ? 'filled' : '' }}"></i>
                                    @endfor
                                    <span class="d-inline-block average-rating">{{ $doctor->review_count }}</span>
                                </div>
                                <p class="text-muted mb-0"><i class="fas fa-map-marker-alt"></i> {{ $doctor->clinic_city }}, {{ $doctor->clinic_address }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Schedule Widget -->
                <form action="{{ route('booking.submit', $doctor->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="appointment_date" id="appointment_date">
                    <input type="hidden" name="appointment_time" id="appointment_time">

                    <div class="card booking-schedule schedule-widget">

                        <!-- Schedule Header -->
                        <div class="schedule-header">
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- Day Slot -->
                                    <div class="day-slot">
                                        <ul>
                                            @foreach($dates as $date)
                                            <li>
                                                <span>{{ $date->format('D') }}</span>
                                                <span class="slot-date">{{ $date->format('d M') }} <small class="slot-year">{{ $date->format('Y') }}</small></span>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <!-- /Day Slot -->
                                </div>
                            </div>
                        </div>
                        <!-- /Schedule Header -->

                        <!-- Schedule Content -->
                        <div class="schedule-cont">
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- Time Slot -->
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
                                                        $startTime = \Carbon\Carbon::parse($daySchedule->start_time);
                                                        $endTime = \Carbon\Carbon::parse($daySchedule->end_time);
                                                        $interval = $daySchedule->slot_duration ?? 60; // Default 60 mins
                                                    @endphp

                                                    @while($startTime < $endTime)
                                                        <a class="timing" href="javascript:void(0)" onclick="selectSlot(this, '{{ $date->format('Y-m-d') }}', '{{ $startTime->format('H:i') }}')">
                                                            <span>{{ $startTime->format('g:i A') }}</span>
                                                        </a>
                                                        @php $startTime->addMinutes($interval); @endphp
                                                    @endwhile
                                                @else
                                                    <a class="timing disabled" href="javascript:void(0)">
                                                        <span>Closed</span>
                                                    </a>
                                                @endif
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <!-- /Time Slot -->
                                </div>
                            </div>
                        </div>
                        <!-- /Schedule Content -->

                    </div>
                    <!-- /Schedule Widget -->

                    <!-- Submit Section -->
                    <div class="submit-section proceed-btn text-right">
                        <button type="submit" class="btn btn-primary submit-btn">Proceed to Pay</button>
                    </div>
                    <!-- /Submit Section -->
                </form>

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

            </div>
        </div>
    </div>

</div>
<!-- /Page Content -->
@endsection

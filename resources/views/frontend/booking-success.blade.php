@extends('layouts.app')

@section('title', 'Booking Success - Doccure')

@section('content')

    <!-- Page Content -->
    <div class="content success-page-cont">
        <div class="container-fluid">

            <div class="row justify-content-center">
                <div class="col-lg-6">

                    <!-- Success Card -->
                    <div class="card success-card">
                        <div class="card-body">
                            <div class="success-cont">
                                <i class="fas fa-check"></i>
                                <h3>Appointment booked Successfully!</h3>
                                <p>Appointment booked with <strong>Dr. {{ session('doctor_name') }}</strong><br>
                                    on <strong>{{ \Carbon\Carbon::parse(session('date'))->format('d M Y') }}
                                        {{ \Carbon\Carbon::parse(session('time'))->format('g:i A') }}</strong>
                                </p>

                                @if(session('type') == 'offline' && session('token_number'))
                                    <div class="card mt-3 mb-3 border-0 bg-light">
                                        <div class="card-body text-center">
                                            <h5 class="text-muted mb-1">Your Token Number</h5>
                                            <h2 class="text-primary mb-0">{{ session('token_number') }}</h2>
                                            <small class="text-muted">Please show this at the clinic reception.</small>
                                        </div>
                                    </div>
                                @elseif(session('type') == 'online' && session('meeting_link'))
                                    <div class="mt-4 mb-4">
                                        <a href="{{ session('meeting_link') }}" target="_blank" class="btn btn-success btn-lg">
                                            <i class="fas fa-video me-2"></i> Join Video Call
                                        </a>
                                        <p class="text-muted mt-2 small">Link will be active at the scheduled time.</p>
                                    </div>
                                @endif

                                <a href="{{ route('home') }}" class="btn btn-primary view-inv-btn">Go to Home</a>
                            </div>
                        </div>
                    </div>
                    <!-- /Success Card -->

                </div>
            </div>

        </div>
    </div>
    <!-- /Page Content -->
@endsection
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
                            <p>Appointment booked with <strong>Dr. Darren Elder</strong><br> on <strong>12 Nov 2019 5:00PM to 6:00PM</strong></p>
                            <a href="{{ route('invoice.view', ['id' => 1]) }}" class="btn btn-primary view-inv-btn">View Invoice</a>
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

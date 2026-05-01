@extends('layouts.app')

@section('title', 'Appointments - ' . ($siteSettings['site_name'] ?? 'Doccure'))

@section('content')
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-md-5 col-lg-4 col-xl-3 theiaStickySidebar">
                @include('frontend.includes.doctor-sidebar')
            </div>

            <div class="col-md-7 col-lg-8 col-xl-9">
                <div class="appointments">

                    @forelse($appointments as $appointment)
                    <div class="appointment-list">
                        <div class="profile-info-widget">
                            <a href="#" class="booking-doc-img">
                                <img src="{{ optional($appointment->patient)->profile_image ? asset('storage/' . $appointment->patient->profile_image) : asset('assets/img/patients/patient.jpg') }}" alt="Patient Image">
                            </a>
                            <div class="profile-det-info">
                                <h3><a href="#">{{ optional(optional($appointment->patient)->user)->name ?? 'Unknown Patient' }}</a></h3>
                                <div class="patient-details">
                                    <h5><i class="far fa-clock"></i> {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d M Y') }}, {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}</h5>
                                    @if(optional($appointment->patient)->address)
                                    <h5><i class="fas fa-map-marker-alt"></i> {{ $appointment->patient->address }}</h5>
                                    @endif
                                    @if(optional(optional($appointment->patient)->user)->email)
                                    <h5><i class="fas fa-envelope"></i> {{ $appointment->patient->user->email }}</h5>
                                    @endif
                                    @if(optional($appointment->patient)->phone)
                                    <h5 class="mb-0"><i class="fas fa-phone"></i> {{ $appointment->patient->phone }}</h5>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="appointment-action">
                            <a href="#" class="btn btn-sm bg-info-light" data-bs-toggle="modal" data-bs-target="#appt_details_{{ $appointment->id }}">
                                <i class="far fa-eye"></i> View
                            </a>
                            @if($appointment->status == 'pending')
                                <form action="{{ route('appointment.accept', $appointment->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm bg-success-light">
                                        <i class="fas fa-check"></i> Accept
                                    </button>
                                </form>
                                <form action="{{ route('appointment.cancel', $appointment->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm bg-danger-light">
                                        <i class="fas fa-times"></i> Cancel
                                    </button>
                                </form>
                            @elseif($appointment->status == 'confirmed')
                                <form action="{{ route('appointment.complete', $appointment->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm bg-info-light">
                                        <i class="fas fa-check-double"></i> Complete
                                    </button>
                                </form>
                                <form action="{{ route('appointment.cancel', $appointment->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm bg-danger-light">
                                        <i class="fas fa-times"></i> Cancel
                                    </button>
                                </form>
                            @else
                                <span class="badge bg-{{ $appointment->status == 'completed' ? 'info' : 'danger' }}">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No appointments found.</h5>
                    </div>
                    @endforelse

                    <div class="mt-3">
                        {{ $appointments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

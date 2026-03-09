@extends('layouts.app')

@section('title', 'My Patients - ' . ($siteSettings['site_name'] ?? 'Doccure'))

@section('content')
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-md-5 col-lg-4 col-xl-3 theiaStickySidebar">
                @include('frontend.includes.doctor-sidebar')
            </div>
            <div class="col-md-7 col-lg-8 col-xl-9">
                <div class="row row-grid">
                    @forelse($patients as $patient)
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <div class="card widget-profile pat-widget-profile">
                            <div class="card-body">
                                <div class="pro-widget-content">
                                    <div class="profile-info-widget">
                                        <a href="#" class="booking-doc-img">
                                            <img src="{{ $patient->profile_image ? asset('storage/' . $patient->profile_image) : asset('assets/img/patients/patient.jpg') }}" alt="User Image">
                                        </a>
                                        <div class="profile-det-info">
                                            <h3>{{ optional($patient->user)->name ?? 'Unknown' }}</h3>
                                            <div class="patient-details">
                                                <h5><b>Patient ID :</b> PT{{ sprintf('%04d', $patient->id) }}</h5>
                                                @if(optional($patient->user)->email)
                                                <h5 class="mb-0"><i class="fas fa-envelope"></i> {{ $patient->user->email }}</h5>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="patient-info">
                                    <ul>
                                        @if($patient->phone)
                                        <li>Phone <span>{{ $patient->phone }}</span></li>
                                        @endif
                                        @if($patient->gender)
                                        <li>Gender <span>{{ ucfirst($patient->gender) }}</span></li>
                                        @endif
                                        @if($patient->blood_group)
                                        <li>Blood Group <span>{{ $patient->blood_group }}</span></li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No patients found.</h5>
                    </div>
                    @endforelse
                </div>

                <div class="mt-3">
                    {{ $patients->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

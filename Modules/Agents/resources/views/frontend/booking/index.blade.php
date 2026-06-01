@extends('layouts.app')

@section('title', 'Book Appointment - ' . ($siteSettings['site_name'] ?? 'Doccure'))

@section('content')
    <!-- Breadcrumb -->
    <div class="breadcrumb-bar">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-12 col-12">
                    <nav aria-label="breadcrumb" class="page-breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('agent.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Book Appointment</li>
                        </ol>
                    </nav>
                    <h2 class="breadcrumb-title">Select Doctor for Patient</h2>
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
                    <!-- Search Filter Form -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <form action="{{ route('agent.book-appointment') }}" method="GET">
                                <div class="row">
                                    <div class="col-md-5 mb-2">
                                        <input type="text" name="search" class="form-control" placeholder="Search Doctor by Name..." value="{{ request('search') }}">
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <select name="speciality" class="form-select">
                                            <option value="">All Specialities</option>
                                            @foreach ($specialities as $spec)
                                                <option value="{{ $spec->id }}" {{ request('speciality') == $spec->id ? 'selected' : '' }}>
                                                    {{ $spec->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <button type="submit" class="btn btn-primary w-100">Filter Doctors</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Doctors List -->
                    <div class="row">
                        @forelse ($doctors as $doc)
                            <div class="col-md-4 col-sm-6 mb-4">
                                <div class="card h-100 shadow-sm border-0 overflow-hidden">
                                    <div class="position-relative text-center py-4 bg-light">
                                        <img src="{{ $doc->profile_image ? asset($doc->profile_image) : asset('assets/img/doctors/doctor-thumb-02.jpg') }}" 
                                            class="rounded-circle border border-4 border-white shadow-sm" 
                                            style="width: 100px; height: 100px; object-fit: cover;" 
                                            alt="{{ $doc->user->name }}">
                                    </div>
                                    <div class="card-body text-center d-flex flex-column">
                                        <h5 class="card-title font-weight-bold mb-1">Dr. {{ $doc->user->name }}</h5>
                                        <p class="text-primary small mb-3">{{ $doc->speciality->name ?? 'General Practitioner' }}</p>
                                        
                                        <div class="border-top border-bottom py-2 mb-4 text-start">
                                            <div class="d-flex justify-content-between small text-muted mb-1">
                                                <span>Clinic Fee:</span>
                                                <span class="font-weight-bold text-dark">৳{{ number_format($doc->consultation_fee, 2) }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between small text-muted">
                                                <span>Online Fee:</span>
                                                <span class="font-weight-bold text-dark">৳{{ number_format($doc->online_fee ?? $doc->consultation_fee, 2) }}</span>
                                            </div>
                                        </div>
                                        
                                        <a href="{{ route('agent.booking', $doc->id) }}" class="btn btn-primary w-100 mt-auto py-2">
                                            Book Appointment
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-5">
                                <i class="fas fa-user-md fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No approved doctors found.</h5>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $doctors->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

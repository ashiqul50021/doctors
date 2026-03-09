@extends('layouts.app')

@section('title', 'Favourites - Doccure')

@section('content')

<!-- Page Content -->
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-5 col-lg-4 col-xl-3 theiaStickySidebar">
                <div class="profile-sidebar">
                    <div class="widget-profile pro-widget-content">
                        <div class="profile-info-widget">
                            <a href="#" class="booking-doc-img">
                                <img src="{{ asset('assets/img/patients/patient.jpg') }}" alt="User Image">
                            </a>
                            <div class="profile-det-info">
                                <h3>Richard Wilson</h3>
                                <div class="patient-details">
                                    <h5><i class="fas fa-birthday-cake"></i> 24 Jul 1983, 38 years</h5>
                                    <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> Newyork, USA</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="dashboard-widget">
                        <nav class="dashboard-menu">
                            <ul>
                                <li>
                                    <a href="{{ route('patient.dashboard') }}">
                                        <i class="fas fa-columns"></i>
                                        <span>Dashboard</span>
                                    </a>
                                </li>
                                <li class="active">
                                    <a href="{{ route('favourites') }}">
                                        <i class="fas fa-bookmark"></i>
                                        <span>Favourites</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('chat') }}">
                                        <i class="fas fa-comments"></i>
                                        <span>Message</span>
                                        <small class="unread-msg">23</small>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('profile.settings') }}">
                                        <i class="fas fa-user-cog"></i>
                                        <span>Profile Settings</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('change.password') }}">
                                        <i class="fas fa-lock"></i>
                                        <span>Change Password</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('login') }}">
                                        <i class="fas fa-sign-out-alt"></i>
                                        <span>Logout</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>

                </div>
            </div>

            <div class="col-md-7 col-lg-8 col-xl-9">
                <div class="row row-grid">

                    <!-- Doctor Widget -->
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <div class="profile-widget">
                            <div class="doc-img">
                                <a href="{{ route('doctor.profile') }}">
                                    <img class="img-fluid" alt="User Image" src="{{ asset('assets/img/doctors/doctor-01.jpg') }}">
                                </a>
                                <a href="javascript:void(0)" class="fav-btn">
                                    <i class="far fa-bookmark"></i>
                                </a>
                            </div>
                            <div class="pro-content">
                                <h3 class="title">
                                    <a href="{{ route('doctor.profile') }}">Ruby Perrin</a>
                                    <i class="fas fa-check-circle verified"></i>
                                </h3>
                                <p class="speciality">MDS - Periodontology and Oral Implantology, BDS</p>
                                <div class="rating">
                                    <i class="fas fa-star filled"></i>
                                    <i class="fas fa-star filled"></i>
                                    <i class="fas fa-star filled"></i>
                                    <i class="fas fa-star filled"></i>
                                    <i class="fas fa-star filled"></i>
                                    <span class="d-inline-block average-rating">(17)</span>
                                </div>
                                <ul class="available-info">
                                    <li>
                                        <i class="fas fa-map-marker-alt"></i> Florida, USA
                                    </li>
                                    <li>
                                        <i class="far fa-clock"></i> Available on Fri, 22 Mar
                                    </li>
                                    <li>
                                        <i class="far fa-money-bill-alt"></i> $300 - $1000 <i class="fas fa-info-circle" data-bs-toggle="tooltip" title="Lorem Ipsum"></i>
                                    </li>
                                </ul>
                                <div class="row row-sm">
                                    <div class="col-6">
                                        <a href="{{ route('doctor.profile') }}" class="btn view-btn">View Profile</a>
                                    </div>
                                    <div class="col-6">
                                        <a href="{{ route('booking') }}" class="btn book-btn">Book Now</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Doctor Widget -->

                    <!-- Doctor Widget -->
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <div class="profile-widget">
                            <div class="doc-img">
                                <a href="{{ route('doctor.profile') }}">
                                    <img class="img-fluid" alt="User Image" src="{{ asset('assets/img/doctors/doctor-02.jpg') }}">
                                </a>
                                <a href="javascript:void(0)" class="fav-btn">
                                    <i class="far fa-bookmark"></i>
                                </a>
                            </div>
                            <div class="pro-content">
                                <h3 class="title">
                                    <a href="{{ route('doctor.profile') }}">Darren Elder</a>
                                    <i class="fas fa-check-circle verified"></i>
                                </h3>
                                <p class="speciality">BDS, MDS - Oral & Maxillofacial Surgery</p>
                                <div class="rating">
                                    <i class="fas fa-star filled"></i>
                                    <i class="fas fa-star filled"></i>
                                    <i class="fas fa-star filled"></i>
                                    <i class="fas fa-star filled"></i>
                                    <i class="fas fa-star"></i>
                                    <span class="d-inline-block average-rating">(35)</span>
                                </div>
                                <ul class="available-info">
                                    <li>
                                        <i class="fas fa-map-marker-alt"></i> Newyork, USA
                                    </li>
                                    <li>
                                        <i class="far fa-clock"></i> Available on Fri, 22 Mar
                                    </li>
                                    <li>
                                        <i class="far fa-money-bill-alt"></i> $50 - $300 <i class="fas fa-info-circle" data-bs-toggle="tooltip" title="Lorem Ipsum"></i>
                                    </li>
                                </ul>
                                <div class="row row-sm">
                                    <div class="col-6">
                                        <a href="{{ route('doctor.profile') }}" class="btn view-btn">View Profile</a>
                                    </div>
                                    <div class="col-6">
                                        <a href="{{ route('booking') }}" class="btn book-btn">Book Now</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Doctor Widget -->

                    <!-- Doctor Widget -->
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <div class="profile-widget">
                            <div class="doc-img">
                                <a href="{{ route('doctor.profile') }}">
                                    <img class="img-fluid" alt="User Image" src="{{ asset('assets/img/doctors/doctor-03.jpg') }}">
                                </a>
                                <a href="javascript:void(0)" class="fav-btn">
                                    <i class="far fa-bookmark"></i>
                                </a>
                            </div>
                            <div class="pro-content">
                                <h3 class="title">
                                    <a href="{{ route('doctor.profile') }}">Deborah Angel</a>
                                    <i class="fas fa-check-circle verified"></i>
                                </h3>
                                <p class="speciality">MBBS, MD - General Medicine, DNB - Cardiology</p>
                                <div class="rating">
                                    <i class="fas fa-star filled"></i>
                                    <i class="fas fa-star filled"></i>
                                    <i class="fas fa-star filled"></i>
                                    <i class="fas fa-star filled"></i>
                                    <i class="fas fa-star"></i>
                                    <span class="d-inline-block average-rating">(27)</span>
                                </div>
                                <ul class="available-info">
                                    <li>
                                        <i class="fas fa-map-marker-alt"></i> Georgia, USA
                                    </li>
                                    <li>
                                        <i class="far fa-clock"></i> Available on Fri, 22 Mar
                                    </li>
                                    <li>
                                        <i class="far fa-money-bill-alt"></i> $100 - $400 <i class="fas fa-info-circle" data-bs-toggle="tooltip" title="Lorem Ipsum"></i>
                                    </li>
                                </ul>
                                <div class="row row-sm">
                                    <div class="col-6">
                                        <a href="{{ route('doctor.profile') }}" class="btn view-btn">View Profile</a>
                                    </div>
                                    <div class="col-6">
                                        <a href="{{ route('booking') }}" class="btn book-btn">Book Now</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Doctor Widget -->

                    <!-- Doctor Widget -->
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <div class="profile-widget">
                            <div class="doc-img">
                                <a href="{{ route('doctor.profile') }}">
                                    <img class="img-fluid" alt="User Image" src="{{ asset('assets/img/doctors/doctor-04.jpg') }}">
                                </a>
                                <a href="javascript:void(0)" class="fav-btn">
                                    <i class="far fa-bookmark"></i>
                                </a>
                            </div>
                            <div class="pro-content">
                                <h3 class="title">
                                    <a href="{{ route('doctor.profile') }}">Sofia Brient</a>
                                    <i class="fas fa-check-circle verified"></i>
                                </h3>
                                <p class="speciality">MBBS, MS - General Surgery, MCh - Urology</p>
                                <div class="rating">
                                    <i class="fas fa-star filled"></i>
                                    <i class="fas fa-star filled"></i>
                                    <i class="fas fa-star filled"></i>
                                    <i class="fas fa-star filled"></i>
                                    <i class="fas fa-star"></i>
                                    <span class="d-inline-block average-rating">(4)</span>
                                </div>
                                <ul class="available-info">
                                    <li>
                                        <i class="fas fa-map-marker-alt"></i> Louisiana, USA
                                    </li>
                                    <li>
                                        <i class="far fa-clock"></i> Available on Fri, 22 Mar
                                    </li>
                                    <li>
                                        <i class="far fa-money-bill-alt"></i> $150 - $250 <i class="fas fa-info-circle" data-bs-toggle="tooltip" title="Lorem Ipsum"></i>
                                    </li>
                                </ul>
                                <div class="row row-sm">
                                    <div class="col-6">
                                        <a href="{{ route('doctor.profile') }}" class="btn view-btn">View Profile</a>
                                    </div>
                                    <div class="col-6">
                                        <a href="{{ route('booking') }}" class="btn book-btn">Book Now</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Doctor Widget -->

                </div>
            </div>
        </div>
    </div>

</div>
<!-- /Page Content -->
@endsection

@push('scripts')
<script src="{{ asset('assets/plugins/theia-sticky-sidebar/ResizeSensor.js') }}"></script>
<script src="{{ asset('assets/plugins/theia-sticky-sidebar/theia-sticky-sidebar.js') }}"></script>
@endpush

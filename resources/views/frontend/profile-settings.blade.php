@extends('layouts.app')

@section('title', 'Profile Settings - Doccure')

@section('content')

<!-- Page Content -->
<div class="content">
    <div class="container">
        <div class="row">

            <!-- Profile Sidebar -->
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
                                <li>
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
                                <li class="active">
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
            <!-- /Profile Sidebar -->

            <div class="col-md-7 col-lg-8 col-xl-9">
                <div class="card">
                    <div class="card-body">

                        <!-- Profile Settings Form -->
                        <form>
                            <div class="row form-row">
                                <div class="col-12 col-md-12">
                                    <div class="mb-3">
                                        <div class="change-avatar">
                                            <div class="profile-img">
                                                <img src="{{ asset('assets/img/patients/patient.jpg') }}" alt="User Image">
                                            </div>
                                            <div class="upload-img">
                                                <div class="change-photo-btn">
                                                    <span><i class="fa fa-upload"></i> Upload Photo</span>
                                                    <input type="file" class="upload">
                                                </div>
                                                <small class="form-text text-muted">Allowed JPG, GIF or PNG. Max size of 2MB</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="mb-3">
                                        <label>First Name</label>
                                        <input type="text" class="form-control" value="Richard">
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="mb-3">
                                        <label>Last Name</label>
                                        <input type="text" class="form-control" value="Wilson">
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="mb-3">
                                        <label>Date of Birth</label>
                                        <div class="cal-icon">
                                            <input type="text" class="form-control datetimepicker" value="24-07-1983">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="mb-3">
                                        <label>Blood Group</label>
                                        <select class="form-control select">
                                            <option>A-</option>
                                            <option>A+</option>
                                            <option>B-</option>
                                            <option>B+</option>
                                            <option>AB-</option>
                                            <option>AB+</option>
                                            <option>O-</option>
                                            <option>O+</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="mb-3">
                                        <label>Email ID</label>
                                        <input type="email" class="form-control" value="richard@example.com">
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="mb-3">
                                        <label>Mobile</label>
                                        <input type="text" value="+1 202-555-0125" class="form-control">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                    <label>Address</label>
                                        <input type="text" class="form-control" value="806 Twin Willow Lane">
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="mb-3">
                                        <label>City</label>
                                        <input type="text" class="form-control" value="Old Forge">
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="mb-3">
                                        <label>State</label>
                                        <input type="text" class="form-control" value="Newyork">
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="mb-3">
                                        <label>Zip Code</label>
                                        <input type="text" class="form-control" value="13420">
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="mb-3">
                                        <label>Country</label>
                                        <input type="text" class="form-control" value="United States">
                                    </div>
                                </div>
                            </div>
                            <div class="submit-section">
                                <button type="submit" class="btn btn-primary submit-btn">Save Changes</button>
                            </div>
                        </form>
                        <!-- /Profile Settings Form -->

                    </div>
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

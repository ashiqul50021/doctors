@extends('layouts.app')

@section('title', 'Video Call - Doccure')

@section('body_class', 'call-page')

@section('content')
<!-- Page Content -->
<div class="content">
    <div class="container-fluid">

        <!-- Call Wrapper -->
        <div class="call-wrapper">
            <div class="call-main-row">
                <div class="call-main-wrapper">
                    <div class="call-view">
                        <div class="call-window">

                            <!-- Call Header -->
                            <div class="fixed-header">
                                <div class="navbar">
                                    <div class="user-details">
                                        <div class="float-start user-img">
                                            <a class="avatar avatar-sm me-2" href="{{ route('patient.profile') }}" title="Charlene Reed">
                                                <img src="{{ asset('assets/img/patients/patient1.jpg') }}" alt="User Image" class="rounded-circle">
                                                <span class="status online"></span>
                                            </a>
                                        </div>
                                        <div class="user-info float-start">
                                            <a href="{{ route('patient.profile') }}"><span>Charlene Reed</span></a>
                                            <span class="last-seen">Online</span>
                                        </div>
                                    </div>
                                    <ul class="nav float-end custom-menu">
                                        <li class="nav-item dropdown dropdown-action">
                                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-cog"></i></a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a href="javascript:void(0)" class="dropdown-item">Settings</a>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <!-- /Call Header -->

                            <!-- Call Contents -->
                            <div class="call-contents">
                                <div class="call-content-wrap">
                                    <div class="user-video">
                                        <img src="{{ asset('assets/img/video-call.jpg') }}" alt="User Image">
                                    </div>
                                    <div class="my-video">
                                        <ul>
                                            <li>
                                                <img src="{{ asset('assets/img/patients/patient1.jpg') }}" class="img-fluid" alt="User Image">
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!-- Call Contents -->

                            <!-- Call Footer -->
                            <div class="call-footer">
                                <div class="call-icons">
                                    <span class="call-duration">00:59</span>
                                    <ul class="call-items">
                                        <li class="call-item">
                                            <a href="#" title="Enable Video" data-placement="top" data-bs-toggle="tooltip">
                                                <i class="fas fa-video camera"></i>
                                            </a>
                                        </li>
                                        <li class="call-item">
                                            <a href="#" title="Mute Audio" data-placement="top" data-bs-toggle="tooltip">
                                                <i class="fa fa-microphone microphone"></i>
                                            </a>
                                        </li>
                                        <li class="call-item">
                                            <a href="#" title="Add User" data-placement="top" data-bs-toggle="tooltip">
                                                <i class="fa fa-user-plus"></i>
                                            </a>
                                        </li>
                                        <li class="call-item">
                                            <a href="#" title="Full Screen" data-placement="top" data-bs-toggle="tooltip">
                                                <i class="fas fa-arrows-alt-v full-screen"></i>
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="end-call">
                                        <a href="javascript:void(0);">
                                            <i class="material-icons">call_end</i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <!-- /Call Footer -->

                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- /Call Wrapper -->

    </div>

</div>
<!-- /Page Content -->
@endsection

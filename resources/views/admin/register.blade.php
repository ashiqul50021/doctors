@extends('layouts.admin')

@section('title', 'Register - Doccure Admin')

@section('content')
<div class="main-wrapper login-body">
    <div class="login-wrapper">
        <div class="container">
            <div class="loginbox">
                <div class="login-left">
                    <img class="img-fluid" src="{{ asset('assets/img/logo-white.png') }}" alt="Logo">
                </div>
                <div class="login-right">
                    <div class="login-right-wrap">
                        <h1>Register</h1>
                        <p class="account-subtitle">Access to our dashboard</p>

                        <!-- Form -->
                        <form action="{{ route('admin.login') }}">
                            <div class="mb-3">
                                <input class="form-control" type="text" placeholder="Name">
                            </div>
                            <div class="mb-3">
                                <input class="form-control" type="text" placeholder="Email">
                            </div>
                            <div class="mb-3">
                                <input class="form-control" type="text" placeholder="Password">
                            </div>
                            <div class="mb-3">
                                <input class="form-control" type="text" placeholder="Confirm Password">
                            </div>
                            <div class="mb-3 mb-0">
                                <button class="btn btn-primary btn-block" type="submit">Register</button>
                            </div>
                        </form>
                        <!-- /Form -->

                        <div class="login-or">
                            <span class="or-line"></span>
                            <span class="span-or">or</span>
                        </div>

                        <div class="social-login">
                            <span>Register with</span>
                            <a href="#" class="facebook"><i class="fab fa-facebook-f"></i></a><a href="#" class="google"><i class="fab fa-google"></i></a>
                        </div>

                        <div class="text-center dont-have">Already have an account? <a href="{{ route('admin.login') }}">Login</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

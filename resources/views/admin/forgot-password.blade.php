@extends('layouts.admin')

@section('title', 'Forgot Password - Doccure Admin')

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
                        <h1>Forgot Password?</h1>
                        <p class="account-subtitle">Enter your email to get a password reset link</p>

                        <!-- Form -->
                        <form action="{{ route('admin.login') }}">
                            <div class="mb-3">
                                <input class="form-control" type="text" placeholder="Email">
                            </div>
                            <div class="mb-3 mb-0">
                                <button class="btn btn-primary btn-block" type="submit">Reset Password</button>
                            </div>
                        </form>
                        <!-- /Form -->

                        <div class="text-center dont-have">Remember your password? <a href="{{ route('admin.login') }}">Login</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

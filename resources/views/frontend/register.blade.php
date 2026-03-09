@extends('layouts.app')

@section('title', 'Register - ' . ($siteSettings['site_name'] ?? 'Doccure'))

@section('content')
    <!-- Page Content -->
    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-md-8 offset-md-2">

                    <!-- Register Content -->
                    <div class="account-content">
                        <div class="row align-items-center justify-content-center">
                            <div class="col-md-7 col-lg-6 login-left">
                                <img src="{{ asset('assets/img/login-banner.png') }}" class="img-fluid"
                                    alt="Doccure Register">
                            </div>
                            <div class="col-md-12 col-lg-6 login-right">
                                <div class="login-header">
                                    <h3>Patient Register</h3>
                                </div>
                                <form action="{{ route('register.submit') }}" method="POST">
                                    @csrf
                                    <div class="mb-3 form-focus">
                                        <input type="text" class="form-control floating" name="name" required
                                            value="{{ old('name') }}">
                                        <label class="focus-label">Name</label>
                                        @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="mb-3 form-focus">
                                        <input type="text" class="form-control floating" name="mobile" required
                                            value="{{ old('mobile') }}">
                                        <label class="focus-label">Mobile Number</label>
                                        @error('mobile') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="mb-3 form-focus">
                                        <input type="email" class="form-control floating" name="email" required
                                            value="{{ old('email') }}">
                                        <label class="focus-label">Email</label>
                                        @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="mb-3 form-focus">
                                        <input type="password" class="form-control floating" name="password" required>
                                        <label class="focus-label">Create Password</label>
                                        @error('password') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="mb-3 form-focus">
                                        <input type="password" class="form-control floating" name="password_confirmation"
                                            required>
                                        <label class="focus-label">Confirm Password</label>
                                    </div>
                                    <div class="text-end">
                                        <a class="forgot-link" href="{{ route('login') }}">Already have an account?</a>
                                    </div>
                                    <button class="btn btn-primary btn-block btn-lg login-btn" type="submit">Signup</button>
                                    <div class="login-or">
                                        <span class="or-line"></span>
                                        <span class="span-or">or</span>
                                    </div>
                                    <div class="row form-row social-login">
                                        <div class="col-6">
                                            <a href="#" class="btn btn-facebook btn-block"><i
                                                    class="fab fa-facebook-f me-1"></i> Login</a>
                                        </div>
                                        <div class="col-6">
                                            <a href="#" class="btn btn-google btn-block"><i class="fab fa-google me-1"></i>
                                                Login</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- /Register Content -->

                </div>
            </div>

        </div>
    </div>
    <!-- /Page Content -->
@endsection
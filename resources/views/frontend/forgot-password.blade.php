@extends('layouts.app')

@section('title', 'Forgot Password - ' . ($siteSettings['site_name'] ?? 'abcsheba'))

@section('body_class', 'account-page')

@section('content')
    <style>
        .main-wrapper {
            padding: 0;
            margin: 0;
            background-color: #fff;
        }

        .content {
            padding: 40px 0;
        }

        /* Centered Layout */
        .split-layout {
            display: flex;
            min-height: 100vh;
            align-items: center;
            justify-content: center;
            background: #fff;
        }

        /* Right Side - Form */
        .split-right {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #fff;
            padding: 2rem;
            width: 100%;
        }

        .split-form-container {
            width: 100%;
            max-width: 400px;
        }

        .form-header-logo {
            max-width: 130px;
            margin-bottom: 2rem;
        }

        .form-title {
            font-size: 2rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }

        .form-subtitle {
            color: #6b7280;
            margin-bottom: 2.5rem;
            font-size: 0.95rem;
        }

        /* Custom Input Styling */
        .split-input {
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            color: #1f2937;
            background-color: #fff;
            transition: border-color 0.2s;
        }

        .split-input:focus {
            border-color: #345cce;
            box-shadow: 0 0 0 3px rgba(52, 92, 206, 0.1);
            outline: none;
        }

        .split-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
            display: block;
        }

        .split-btn {
            background-color: #345cce;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 0.875rem 1rem;
            font-weight: 600;
            font-size: 1rem;
            transition: background-color 0.2s;
        }

        .split-btn:hover {
            background-color: #2a4aaa;
            color: #fff;
        }

        .forgot-link {
            font-size: 0.875rem;
            color: #345cce;
            text-decoration: none;
            font-weight: 600;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

    </style>

    <div class="split-layout">
        <div class="split-right">
            <div class="split-form-container">
                <img src="{{ !empty($siteSettings['logo']) ? asset($siteSettings['logo']) : asset('assets/img/logo.png') }}"
                    class="form-header-logo" alt="Logo">

                <h3 class="form-title">Forgot Password?</h3>
                <p class="form-subtitle">Enter your email to get a password reset link</p>

                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <form action="{{ route('password.email') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label class="split-label">Email</label>
                        <input type="email" class="form-control split-input" name="email" required
                            value="{{ old('email') }}" placeholder="example@gmail.com">
                        @error('email') <span class="text-danger small d-block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="d-grid mb-4">
                        <button class="btn split-btn" type="submit">Send Reset Link</button>
                    </div>

                    <div class="text-center mt-4">
                        <span style="font-size: 0.875rem; color: #4b5563;">Remember your password? <a
                                href="{{ route('login') }}"
                                style="color: #345cce; font-weight: 600; text-decoration: none;">Sign In</a></span>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

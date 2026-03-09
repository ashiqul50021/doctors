@extends('layouts.app')

@section('title', 'Login - ' . ($siteSettings['site_name'] ?? 'Doccure'))

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

        .split-input-group {
            position: relative;
        }

        .split-input-icon {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            cursor: pointer;
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
        <!-- Centered Login Form -->
        <div class="split-right">
            <div class="split-form-container">
                <img src="{{ !empty($siteSettings['logo']) ? asset($siteSettings['logo']) : asset('assets/img/logo.png') }}"
                    class="form-header-logo" alt="Invision Logo">

                <h3 class="form-title">Login</h3>
                <p class="form-subtitle">Enter your credentials to login to your account</p>

                <form action="{{ route('patient.login.submit') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label class="split-label">Email</label>
                        <input type="email" class="form-control split-input" name="email" required
                            value="{{ old('email') }}" placeholder="example@gmail.com">
                        @error('email') <span class="text-danger small d-block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-2">
                        <label class="split-label">Password</label>
                        <div class="split-input-group">
                            <input type="password" class="form-control split-input pe-5" name="password" required
                                placeholder="••••••••••••••">
                            <i class="far fa-eye split-input-icon"></i>
                        </div>
                        @error('password') <span class="text-danger small d-block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="text-end mb-4">
                        <a class="forgot-link" href="{{ route('forgot.password') }}">Forgot Password?</a>
                    </div>

                    <div class="d-grid mb-4">
                        <button class="btn split-btn" type="submit">Sign In</button>
                    </div>

                    <div class="text-center mt-4">
                        <span style="font-size: 0.875rem; color: #4b5563;">Don't have an account? <a
                                href="{{ route('register') }}"
                                style="color: #345cce; font-weight: 600; text-decoration: none;">Sign Up</a></span>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Manually handle floating label interactions to ensure they work reliably
        document.addEventListener('DOMContentLoaded', function () {
            const formInputs = document.querySelectorAll('.form-focus .form-control');

            formInputs.forEach(input => {
                // Initial check
                updateLabel(input);

                // Events
                input.addEventListener('focus', function () {
                    this.parentElement.classList.add('focused');
                });

                input.addEventListener('blur', function () {
                    updateLabel(this);
                });

                input.addEventListener('input', function () {
                    updateLabel(this);
                });
            });

            function updateLabel(input) {
                if (input.value.length > 0 || document.activeElement === input) {
                    input.parentElement.classList.add('focused');
                } else {
                    input.parentElement.classList.remove('focused');
                }
            }
        });
    </script>
@endsection

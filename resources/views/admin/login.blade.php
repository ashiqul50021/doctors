@extends('layouts.admin')

@section('title', 'Admin Login - ' . ($siteSettings['site_name'] ?? 'Doccure'))

@section('content')
    <style>
        .header,
        .sidebar {
            display: none !important;
        }

        .page-wrapper {
            margin-left: 0 !important;
            padding-top: 0 !important;
            min-height: 100vh;
        }

        .content.container-fluid {
            padding: 0 !important;
            min-height: 100vh;
        }

        .main-wrapper {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 28px;
            background:
                radial-gradient(900px 380px at -10% -10%, rgba(37, 99, 235, .18) 0%, rgba(37, 99, 235, 0) 68%),
                radial-gradient(700px 320px at 110% 100%, rgba(14, 165, 233, .18) 0%, rgba(14, 165, 233, 0) 70%),
                linear-gradient(135deg, #f8fbff 0%, #eef4ff 48%, #ffffff 100%);
        }

        .admin-login-card {
            width: 100%;
            max-width: 460px;
            border-radius: 20px;
            border: 1px solid rgba(148, 163, 184, 0.25);
            padding: 34px 30px 30px;
            background: rgba(255, 255, 255, 0.92);
            box-shadow: 0 30px 65px rgba(2, 6, 23, 0.12);
            backdrop-filter: blur(10px);
        }

        .admin-login-logo {
            max-width: 146px;
            margin-bottom: 22px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .admin-login-title {
            margin: 0 0 6px;
            font-size: 40px;
            line-height: 1.1;
            color: #0f172a;
            font-weight: 700;
            letter-spacing: -0.03em;
        }

        .admin-login-subtitle {
            margin-bottom: 26px;
            color: #64748b;
            font-size: 15px;
        }

        .admin-field {
            position: relative;
            margin-bottom: 14px;
        }

        .admin-field-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 14px;
            pointer-events: none;
        }

        .admin-toggle-pass {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            border: 0;
            background: transparent;
            cursor: pointer;
            padding: 0;
        }

        .admin-login-input {
            height: 50px;
            width: 100%;
            border-radius: 12px;
            border: 1px solid #cbd5e1;
            background: #fff;
            padding: 10px 40px 10px 40px;
            font-size: 15px;
            color: #0f172a;
            transition: all .2s ease;
        }

        .admin-login-input:focus {
            border-color: #2563eb;
            outline: none;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.14);
        }

        .admin-login-btn {
            margin-top: 8px;
            width: 100%;
            height: 50px;
            border-radius: 12px;
            border: 0;
            font-weight: 700;
            font-size: 15px;
            letter-spacing: .01em;
            color: #fff;
            background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 42%, #0ea5e9 100%);
            box-shadow: 0 14px 26px rgba(37, 99, 235, 0.34);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .admin-login-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 16px 30px rgba(37, 99, 235, 0.42);
        }

        .admin-login-foot {
            margin-top: 16px;
            font-size: 13px;
            color: #64748b;
            text-align: center;
        }

        .admin-login-foot strong {
            color: #334155;
        }

        @media (max-width: 575px) {
            .main-wrapper {
                padding: 16px;
            }

            .admin-login-card {
                padding: 26px 18px 20px;
                border-radius: 16px;
            }

            .admin-login-title {
                font-size: 32px;
            }
        }
    </style>

    <div class="main-wrapper">
        <div class="admin-login-card">
            <img class="admin-login-logo"
                src="{{ !empty($siteSettings['logo']) ? asset($siteSettings['logo']) : asset('assets/img/logo.png') }}"
                alt="Logo">

            <h1 class="admin-login-title">Admin Login</h1>
            <p class="admin-login-subtitle">Use your admin credentials to continue.</p>

            <form action="{{ route('admin.login.submit') }}" method="POST">
                @csrf

                <div class="admin-field">
                    <i class="fas fa-envelope admin-field-icon"></i>
                    <input class="admin-login-input" type="text" name="email" placeholder="Email" required
                        value="{{ old('email') }}">
                </div>
                @error('email')
                    <div class="text-danger small mb-2">{{ $message }}</div>
                @enderror

                <div class="admin-field">
                    <i class="fas fa-lock admin-field-icon"></i>
                    <input class="admin-login-input" id="adminPassword" type="password" name="password"
                        placeholder="Password" required>
                    <button class="admin-toggle-pass" type="button" onclick="toggleAdminPassword()">
                        <i id="adminPassIcon" class="far fa-eye"></i>
                    </button>
                </div>
                @error('password')
                    <div class="text-danger small mb-2">{{ $message }}</div>
                @enderror

                <button class="admin-login-btn" type="submit">Sign In</button>

                <p class="admin-login-foot mb-0">Protected area for <strong>administrators only</strong>.</p>
            </form>
        </div>
    </div>

    <script>
        function toggleAdminPassword() {
            const input = document.getElementById('adminPassword');
            const icon = document.getElementById('adminPassIcon');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
@endsection

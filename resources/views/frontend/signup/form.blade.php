@extends('layouts.app')

@section('title', $config['title'] . ' - ' . ($siteSettings['site_name'] ?? 'abcsheba'))

@section('body_class', 'account-page')

@section('content')
    <style>
        .main-wrapper { padding: 0; margin: 0; background-color: #fff; }
        .signup-wrap { display: flex; justify-content: center; padding: 40px 16px; }
        .signup-box { width: 100%; max-width: 440px; }
        .signup-box .form-title { font-size: 2rem; font-weight: 700; color: #1f2937; margin-bottom: .5rem; }
        .signup-box .form-subtitle { color: #6b7280; margin-bottom: 1.75rem; font-size: .95rem; }
        .signup-box .split-label { font-weight: 600; font-size: 13px; color: #374151; margin-bottom: 6px; display: block; }
        .signup-box .split-input { padding: 11px 14px; border-radius: 8px; border: 1px solid #d1d5db; }
        .signup-box .split-input:focus { border-color: #345cce; box-shadow: 0 0 0 3px rgba(52, 92, 206, .12); }
        .signup-box .split-btn { background: #345cce; color: #fff; padding: 12px; border-radius: 8px; font-weight: 600; }
        .signup-box .split-btn:hover { background: #2a4bb0; color: #fff; }
        .pw-group { position: relative; }
        .pw-group .eye { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #9ca3af; }
    </style>

    <div class="signup-wrap">
        <div class="signup-box">
            <a href="{{ route('register') }}" class="small text-decoration-none" style="color:#345cce;">
                <i class="fas fa-arrow-left me-1"></i> Choose another role
            </a>

            <h3 class="form-title mt-3">{{ $config['title'] }}</h3>
            <p class="form-subtitle">{{ $config['subtitle'] }}</p>

            <form action="{{ route('signup.submit', $role) }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="split-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control split-input" name="name" value="{{ old('name') }}" required>
                    @error('name') <span class="text-danger small d-block mt-1">{{ $message }}</span> @enderror
                </div>

                @foreach ($config['fields'] as $field)
                    <div class="mb-3">
                        <label class="split-label">
                            {{ $field['label'] }}
                            @if ($field['required']) <span class="text-danger">*</span> @endif
                        </label>
                        @if ($field['type'] === 'textarea')
                            <textarea class="form-control split-input" name="{{ $field['name'] }}" rows="2"
                                @if ($field['required']) required @endif>{{ old($field['name']) }}</textarea>
                        @else
                            <input type="text" class="form-control split-input" name="{{ $field['name'] }}"
                                value="{{ old($field['name']) }}" @if ($field['required']) required @endif>
                        @endif
                        @error($field['name']) <span class="text-danger small d-block mt-1">{{ $message }}</span> @enderror
                    </div>
                @endforeach

                <div class="mb-3">
                    <label class="split-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control split-input" name="email" value="{{ old('email') }}" required>
                    @error('email') <span class="text-danger small d-block mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label class="split-label">Password <span class="text-danger">*</span></label>
                    <div class="pw-group">
                        <input type="password" class="form-control split-input pe-5" name="password" required>
                        <i class="far fa-eye eye" onclick="togglePw(this)"></i>
                    </div>
                    @error('password') <span class="text-danger small d-block mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="split-label">Confirm Password <span class="text-danger">*</span></label>
                    <div class="pw-group">
                        <input type="password" class="form-control split-input pe-5" name="password_confirmation" required>
                        <i class="far fa-eye eye" onclick="togglePw(this)"></i>
                    </div>
                </div>

                <div class="d-grid mb-4">
                    <button class="btn split-btn" type="submit">Sign Up</button>
                </div>

                <div class="text-center">
                    <span style="font-size: .875rem; color: #4b5563;">Already have an account?
                        <a href="{{ route('login') }}" style="color:#345cce; font-weight:600; text-decoration:none;">Sign In</a></span>
                </div>
            </form>
        </div>
    </div>

    <script>
        function togglePw(icon) {
            const input = icon.previousElementSibling;
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            icon.classList.toggle('fa-eye', !show);
            icon.classList.toggle('fa-eye-slash', show);
        }
    </script>
@endsection

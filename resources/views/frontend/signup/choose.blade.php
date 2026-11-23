@extends('layouts.app')

@section('title', 'Sign Up - ' . ($siteSettings['site_name'] ?? 'abcsheba'))

@section('body_class', 'account-page')

@section('content')
    <style>
        .main-wrapper { padding: 0; margin: 0; background-color: #fff; }
        .signup-choose { max-width: 760px; margin: 0 auto; padding: 48px 16px; }
        .signup-choose h3 { font-size: 2rem; font-weight: 700; color: #1f2937; margin-bottom: .5rem; text-align: center; }
        .signup-choose .sub { color: #6b7280; text-align: center; margin-bottom: 2rem; }
        .role-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px; }
        .role-card {
            display: flex; align-items: center; gap: 14px; padding: 16px;
            border: 1px solid #e5e7eb; border-radius: 12px; background: #fff;
            text-decoration: none; transition: all .2s;
        }
        .role-card:hover { border-color: #345cce; box-shadow: 0 6px 18px rgba(52, 92, 206, .12); transform: translateY(-2px); }
        .role-icon {
            flex: 0 0 48px; height: 48px; border-radius: 12px; background: #eff6ff; color: #1d4ed8;
            display: flex; align-items: center; justify-content: center; font-size: 20px;
        }
        .role-title { font-weight: 700; color: #1f2937; font-size: 16px; }
        .role-desc { color: #6b7280; font-size: 12.5px; line-height: 1.4; margin-top: 2px; }
        @media (max-width: 575px) { .role-grid { grid-template-columns: 1fr; } }
    </style>

    <div class="signup-choose">
        <h3>Sign Up</h3>
        <p class="sub">Choose how you want to join</p>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="role-grid">
            @foreach ($cards as $card)
                <a href="{{ $card['url'] }}" class="role-card">
                    <span class="role-icon"><i class="fas {{ $card['icon'] }}"></i></span>
                    <span>
                        <span class="role-title d-block">{{ $card['title'] }}</span>
                        <span class="role-desc d-block">{{ $card['desc'] }}</span>
                    </span>
                </a>
            @endforeach
        </div>

        <div class="text-center mt-4">
            <span style="font-size: 0.875rem; color: #4b5563;">Already have an account? <a
                    href="{{ route('login') }}"
                    style="color: #345cce; font-weight: 600; text-decoration: none;">Sign In</a></span>
        </div>
    </div>
@endsection

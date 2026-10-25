@extends('layouts.app')

@section('title', 'Privacy Policy - ' . ($siteSettings['site_name'] ?? 'abcsheba'))

@section('content')

<!-- Breadcrumb -->
<div class="breadcrumb-bar" style="background: #f8fafc; padding: 25px 0; border-bottom: 1px solid #e2e8f0;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-12 col-12">
                <nav aria-label="breadcrumb" class="page-breadcrumb">
                    <ol class="breadcrumb mb-1" style="font-size: 0.875rem;">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}" style="color: #2563eb; text-decoration: none;">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Privacy Policy</li>
                    </ol>
                </nav>
                <h2 class="breadcrumb-title mb-0" style="font-weight: 700; color: #1e293b; font-size: 1.75rem;">Privacy Policy</h2>
            </div>
        </div>
    </div>
</div>
<!-- /Breadcrumb -->

<!-- Page Content -->
<div class="content py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4 p-md-5">
                        @if(!empty($siteSettings['privacy_policy']))
                            {!! $siteSettings['privacy_policy'] !!}
                        @else
                            <div class="privacy-section mb-4">
                                <h4 class="fw-bold text-dark mb-3">১. তথ্যের সুরক্ষা ও গোপনীয়তা (Data Protection)</h4>
                                <p class="text-muted leading-relaxed">
                                    {{ $siteSettings['site_name'] ?? 'abcsheba' }} ব্যবহারকারী ডাক্তার ও রোগীদের ব্যক্তিগত তথ্যের সর্বোচ্চ গোপনীয়তা রক্ষায় প্রতিশ্রুতিবদ্ধ। আপনার নাম, মোবাইল নম্বর ও প্রেসক্রিপশন সংক্রান্ত তথ্য নিরাপদ ডাটাবেসে সংরক্ষিত থাকে।
                                </p>
                            </div>

                            <div class="privacy-section mb-4">
                                <h4 class="fw-bold text-dark mb-3">২. সংগৃহীত তথ্য ও ব্যবহার (Information Collection)</h4>
                                <ul class="text-muted leading-relaxed ps-3">
                                    <li class="mb-2">অ্যাপয়েন্টমেন্ট বুকিং, টেলিমেডিসিন পরামর্শ ও অ্যাকাউন্ট ভেরিফিকেশনের জন্য মোবাইল নম্বর ও নাম ব্যবহার করা হয়।</li>
                                    <li class="mb-2">আপনার কোনো সংবেদনশীল তথ্য তৃতীয় কোনো অননুমোদিত পক্ষের সাথে বাণিজ্যিক উদ্দেশ্যে শেয়ার করা হয় না।</li>
                                </ul>
                            </div>
                        @endif

                        <div class="border-top pt-4 mt-5 text-muted small d-flex justify-content-between align-items-center">
                            <span>Last updated: {{ date('F Y') }}</span>
                            <span>{{ $siteSettings['site_name'] ?? 'abcsheba' }} &copy; {{ date('Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Page Content -->
@endsection

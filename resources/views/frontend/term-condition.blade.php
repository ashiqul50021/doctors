@extends('layouts.app')

@section('title', 'Terms and Conditions - ' . ($siteSettings['site_name'] ?? 'abcsheba'))

@section('content')

<!-- Breadcrumb -->
<div class="breadcrumb-bar" style="background: #f8fafc; padding: 25px 0; border-bottom: 1px solid #e2e8f0;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-12 col-12">
                <nav aria-label="breadcrumb" class="page-breadcrumb">
                    <ol class="breadcrumb mb-1" style="font-size: 0.875rem;">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}" style="color: #2563eb; text-decoration: none;">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Terms & Conditions</li>
                    </ol>
                </nav>
                <h2 class="breadcrumb-title mb-0" style="font-weight: 700; color: #1e293b; font-size: 1.75rem;">Terms & Conditions</h2>
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
                        @if(!empty($siteSettings['terms_conditions']))
                            {!! $siteSettings['terms_conditions'] !!}
                        @else
                            <div class="terms-section mb-4">
                                <h4 class="fw-bold text-dark mb-3">১. সাধারণ নীতিমালা ও সেবার শর্তাবলী (General Terms)</h4>
                                <p class="text-muted leading-relaxed">
                                    {{ $siteSettings['site_name'] ?? 'abcsheba' }} প্ল্যাটফর্মে আপনাকে স্বাগতম। এই ওয়েবসাইটে প্রবেশ করে বা আমাদের টেলিমেডিসিন, ডক্টর অ্যাপয়েন্টমেন্ট বুকিং ও অন্যান্য স্বাস্থ্য সেবা ব্যবহারের মাধ্যমে আপনি এই সকল নিয়ম ও শর্তাবলী মেনে নিতে সম্মত হচ্ছেন।
                                </p>
                            </div>

                            <div class="terms-section mb-4">
                                <h4 class="fw-bold text-dark mb-3">২. ডাক্তারদের জন্য নীতিমালা (Doctor Guidelines)</h4>
                                <ul class="text-muted leading-relaxed ps-3">
                                    <li class="mb-2">নিবন্ধনকারী সকল ডাক্তারকে অবশ্যই BMDC কর্তৃক অনুমোদিত ও সক্রিয় রেজিস্ট্রেশন নম্বরধারী হতে হবে।</li>
                                    <li class="mb-2">প্রোফাইল সম্পূর্ণ করার পর অ্যাডমিন কর্তৃক যাচাই-বাছাই ও অনুমোদন (Approval) সাপেক্ষে সেবা প্রদান করতে পারবেন।</li>
                                    <li class="mb-2">রোগীকে সঠিক পরামর্শ ও প্রয়োজনবোধে অফলাইন বা হাসপাতালে যাওয়ার জরুরি দিকনির্দেশনা প্রদান করতে হবে।</li>
                                </ul>
                            </div>

                            <div class="terms-section mb-4">
                                <h4 class="fw-bold text-dark mb-3">৩. অ্যাপয়েন্টমেন্ট বুকিং ও রোগী সেবা (Patient Appointments)</h4>
                                <ul class="text-muted leading-relaxed ps-3">
                                    <li class="mb-2">অনলাইন ও চেম্বার অ্যাপয়েন্টমেন্টের ক্ষেত্রে সঠিক ও নির্ভুল মোবাইল নম্বর প্রদান করা বাধ্যতামূলক।</li>
                                    <li class="mb-2">চিকিৎসকের পূর্বনির্ধারিত শিডিউল অনুযায়ী সময়মতো উপস্থিত থাকা রোগীর দায়িত্ব। কোনো কারণে শিডিউল পরিবর্তিত হলে প্ল্যাটফর্ম থেকে অবহিত করা হবে।</li>
                                </ul>
                            </div>

                            <div class="terms-section mb-4">
                                <h4 class="fw-bold text-dark mb-3">৪. পেমেন্ট ও রিফান্ড নীতি (Payment & Refund Policy)</h4>
                                <p class="text-muted leading-relaxed">
                                    পেমেন্ট গেটওয়ে (SSLCommerz) বা ম্যানুয়াল মোবাইল ব্যাংকিংয়ের (bKash, Nagad, Rocket) মাধ্যমে ফি পরিশোধ করা যাবে। যদি কোনো ডাক্তার অ্যাপয়েন্টমেন্ট বাতিল করেন, তবে যথাযথ নিয়ম অনুযায়ী প্রদত্ত ফি রিফান্ড প্রক্রিয়াকরণ করা হবে।
                                </p>
                            </div>

                            <div class="terms-section mb-4">
                                <h4 class="fw-bold text-dark mb-3">৫. জরুরি চিকিৎসা সংক্রান্ত সতর্কবার্তা (Emergency Disclaimer)</h4>
                                <div class="alert alert-warning border-0 rounded-2" style="background-color: #fef3c7; color: #92400e;">
                                    <strong>সতর্কতা:</strong> {{ $siteSettings['site_name'] ?? 'abcsheba' }} কোনো জরুরি অ্যাম্বুলেন্স বা আইসিইউ সেবা নয়। যেকোনো প্রাণঘাতী বা তীব্র জরুরি পরিস্থিতিতে কালবিলম্ব না করে নিকটস্থ হাসপাতালে সরাসরি যোগাযোগ করুন।
                                </div>
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

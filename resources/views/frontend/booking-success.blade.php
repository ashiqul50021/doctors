@extends('layouts.app')

@section('title', 'Appointment Confirmed - abcsheba')

@section('content')

    <!-- Page Content -->
    <div class="content py-5" style="background: #f8fafc; min-height: 80vh;">
        <div class="container">

            <div class="row justify-content-center">
                <div class="col-lg-7 col-md-9">

                    <!-- Success Confirmation Card -->
                    <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
                        <!-- Top Green Gradient Banner -->
                        <div class="p-4 text-center text-white" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                            <div class="mb-3">
                                <span class="d-inline-flex align-items-center justify-content-center bg-white text-success rounded-circle shadow-sm" style="width: 64px; height: 64px; font-size: 28px;">
                                    <i class="fas fa-check"></i>
                                </span>
                            </div>
                            <h3 class="fw-bold mb-1 text-white">অ্যাপয়েন্টমেন্ট সফলভাবে নিশ্চিত হয়েছে!</h3>
                            <p class="mb-0 text-white-50" style="font-size: 14px;">Appointment Confirmed Successfully</p>
                        </div>

                        <div class="card-body p-4 p-md-5">
                            
                            <!-- Token & Status Badge -->
                            @if(session('type') == 'offline' && session('token_number'))
                                <div class="card border-2 border-primary border-dashed mb-4" style="background: #eff6ff; border-radius: 16px;">
                                    <div class="card-body text-center py-4">
                                        <span class="text-muted fw-semibold small text-uppercase letter-spacing-1">Your Serial / Token Number</span>
                                        <h1 class="display-4 fw-black text-primary my-2" style="font-weight: 800; letter-spacing: 2px;">
                                            {{ session('token_number') }}
                                        </h1>
                                        <p class="text-muted small mb-0">
                                            <i class="fas fa-info-circle text-primary me-1"></i> চেম্বারে উপস্থিত হয়ে রিসেপশনে এই টোকেনটি দেখান।
                                        </p>
                                    </div>
                                </div>
                            @elseif(session('type') == 'online')
                                <div class="card border-2 border-success border-dashed mb-4" style="background: #f0fdf4; border-radius: 16px;">
                                    <div class="card-body text-center py-4">
                                        <span class="text-success fw-semibold small text-uppercase">Online Telemedicine Consultation</span>
                                        <div class="my-3">
                                            @if(session('meeting_link'))
                                                <a href="{{ session('meeting_link') }}" target="_blank" class="btn btn-success btn-lg px-4 py-3 rounded-pill shadow">
                                                    <i class="fas fa-video me-2"></i> Join Video Consultation Call
                                                </a>
                                            @else
                                                <span class="badge bg-success p-2">Video call link is activated</span>
                                            @endif
                                        </div>
                                        <p class="text-muted small mb-0">নির্ধারিত সময়ে সরাসরি উপরোক্ত লিংকে ক্লিক করে ডাক্তারের সাথে ভিডিও কলে যুক্ত হবেন।</p>
                                    </div>
                                </div>
                            @endif

                            <!-- Appointment Summary Table -->
                            <div class="border rounded-3 p-3 mb-4 bg-light">
                                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                                    <i class="far fa-calendar-alt text-primary me-1"></i> Appointment Information
                                </h6>
                                <div class="row g-2" style="font-size: 14px;">
                                    <div class="col-6 text-muted">ডাক্তারের নাম:</div>
                                    <div class="col-6 text-end fw-bold text-dark">
                                        {{ str_starts_with(strtolower(session('doctor_name') ?? ''), 'dr.') ? session('doctor_name') : 'Dr. ' . (session('doctor_name') ?? 'Doctor') }}
                                    </div>

                                    @if(session('patient_name'))
                                        <div class="col-6 text-muted">রোগীর নাম:</div>
                                        <div class="col-6 text-end fw-semibold text-dark">
                                            {{ session('patient_name') }}
                                        </div>
                                    @endif

                                    <div class="col-6 text-muted">অ্যাপয়েন্টমেন্ট তারিখ:</div>
                                    <div class="col-6 text-end fw-semibold text-dark">
                                        {{ session('date') ? \Carbon\Carbon::parse(session('date'))->format('d M Y (l)') : '-' }}
                                    </div>

                                    <div class="col-6 text-muted">সময় ও স্লট:</div>
                                    <div class="col-6 text-end fw-semibold text-primary">
                                        {{ session('time') ? \Carbon\Carbon::parse(session('time'))->format('g:i A') : '-' }}
                                    </div>

                                    <div class="col-6 text-muted">ভিজিটের ধরন:</div>
                                    <div class="col-6 text-end">
                                        @if(session('type') === 'online')
                                            <span class="badge bg-success">Online Video Call</span>
                                        @else
                                            <span class="badge bg-primary">In-Clinic Chamber Visit</span>
                                        @endif
                                    </div>

                                    @if(session('type') !== 'online' && (session('clinic_name') || session('clinic_address')))
                                        <div class="col-6 text-muted">চেম্বার / লোকেশন:</div>
                                        <div class="col-6 text-end text-dark small fw-semibold">
                                            {{ session('clinic_name') }} {{ session('clinic_address') ? '(' . session('clinic_address') . ')' : '' }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            @php
                                $docName = str_starts_with(strtolower(session('doctor_name') ?? ''), 'dr.') ? session('doctor_name') : 'Dr. ' . (session('doctor_name') ?? 'Doctor');
                                $formattedDate = session('date') ? \Carbon\Carbon::parse(session('date'))->format('d M Y (l)') : date('d M Y');
                                $formattedTime = session('time') ? \Carbon\Carbon::parse(session('time'))->format('g:i A') : '';
                                $patientName = session('patient_name') ?? 'Patient';
                                $patientPhone = session('patient_phone') ?? '';

                                // International phone formatting for WhatsApp (Bangladesh: 880...)
                                $cleanPhone = preg_replace('/[^0-9]/', '', $patientPhone);
                                if (str_starts_with($cleanPhone, '01')) {
                                    $waPhone = '88' . $cleanPhone;
                                } elseif (str_starts_with($cleanPhone, '8801')) {
                                    $waPhone = $cleanPhone;
                                } else {
                                    $waPhone = $cleanPhone ? ('880' . ltrim($cleanPhone, '0')) : '';
                                }

                                $waLines = [];
                                $waLines[] = "🩺 *abcsheba অ্যাপয়েন্টমেন্ট কনফার্মেশন* 🩺";
                                $waLines[] = "━━━━━━━━━━━━━━━━━━";
                                if (session('token_number')) {
                                    $waLines[] = "🎫 *টোকেন নম্বর:* " . session('token_number');
                                }
                                $waLines[] = "👤 *রোগী:* " . $patientName;
                                $waLines[] = "👨‍⚕️ *ডাক্তার:* " . $docName;
                                $waLines[] = "📅 *তারিখ:* " . $formattedDate;
                                if ($formattedTime) {
                                    $waLines[] = "⏰ *সময়:* " . $formattedTime;
                                }
                                $waLines[] = "🏥 *ধরন:* " . (session('type') === 'online' ? 'অনলাইন ভিডিও কনসালটেশন' : 'চেম্বার ভিজিট (In-Clinic)');
                                
                                if (session('type') === 'online' && session('meeting_link')) {
                                    $waLines[] = "🎥 *ভিডিও লিংক:* " . session('meeting_link');
                                }
                                if (session('type') !== 'online') {
                                    $clinicInfo = trim((session('clinic_name') ?? '') . ' ' . (session('clinic_address') ?? ''));
                                    if ($clinicInfo) {
                                        $waLines[] = "📍 *চেম্বার:* " . $clinicInfo;
                                    }
                                }
                                $waLines[] = "━━━━━━━━━━━━━━━━━━";
                                if (session('type') === 'online') {
                                    $waLines[] = "ℹ️ নির্ধারিত সময়ে সরাসরি লিংকে ক্লিক করে ডাক্তারের সাথে ভিডিও কলে যুক্ত হবেন।";
                                } else {
                                    $waLines[] = "ℹ️ চেম্বার রিসেপশনে পৌঁছে এই টোকেনটি দেখান।";
                                }
                                $waLines[] = "🌐 https://abcsheba.com";

                                $waMessage = implode("\n", $waLines);
                                $waTextEncoded = rawurlencode($waMessage);

                                $waDirectUrl = $waPhone ? "https://api.whatsapp.com/send?phone={$waPhone}&text={$waTextEncoded}" : "https://api.whatsapp.com/send?text={$waTextEncoded}";
                                $waShareUrl = "https://api.whatsapp.com/send?text={$waTextEncoded}";
                            @endphp

                            <!-- WhatsApp Token & Reminder Box -->
                            <div class="card mb-4 border-0 shadow-sm" style="background: linear-gradient(135deg, #e8f8ed 0%, #d1f5db 100%); border-radius: 16px; border-left: 5px solid #25D366 !important;">
                                <div class="card-body p-3 p-md-4">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="d-inline-flex align-items-center justify-content-center text-white rounded-circle me-2 shadow-sm" style="width: 38px; height: 38px; background: #25D366; font-size: 20px;">
                                            <i class="fab fa-whatsapp"></i>
                                        </span>
                                        <div>
                                            <h6 class="fw-bold mb-0 text-dark">হোয়াটসঅ্যাপে টোকেন ও স্লিপ সেভ রাখুন</h6>
                                            <small class="text-muted">আপনার বা রোগীর হোয়াটসঅ্যাপে বিস্তারিত তথ্য রিমাইন্ডার হিসেবে পাঠিয়ে রাখুন</small>
                                        </div>
                                    </div>

                                    <div class="d-flex flex-wrap gap-2 mt-3">
                                        @if($waPhone)
                                            <a href="{{ $waDirectUrl }}" target="_blank" class="btn text-white fw-bold px-3 py-2 rounded-pill shadow-sm" style="background: #25D366; font-size: 14px;">
                                                <i class="fab fa-whatsapp me-1 fs-6 align-middle"></i> আমার নম্বরে পাঠান ({{ $patientPhone }})
                                            </a>
                                        @endif
                                        <a href="{{ $waShareUrl }}" target="_blank" class="btn btn-outline-success fw-bold px-3 py-2 rounded-pill bg-white" style="font-size: 14px;">
                                            <i class="fas fa-share-alt me-1"></i> অন্য কারও সাথে শেয়ার করুন
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex flex-wrap gap-2 justify-content-center">
                                <a href="javascript:void(0);" onclick="window.print();" class="btn btn-outline-secondary px-4 rounded-pill">
                                    <i class="fas fa-print me-1"></i> Print Token Slip
                                </a>

                                @if(session('doctor_user_id'))
                                    <a href="{{ route('chat', ['user_id' => session('doctor_user_id')]) }}" class="btn btn-primary px-4 rounded-pill">
                                        <i class="far fa-comment-dots me-1"></i> Chat with Doctor
                                    </a>
                                @endif

                                <a href="{{ route('home') }}" class="btn btn-outline-primary px-4 rounded-pill">
                                    <i class="fas fa-home me-1"></i> Go to Home
                                </a>
                            </div>

                        </div>
                    </div>
                    <!-- /Success Card -->

                </div>
            </div>

        </div>
    </div>
    <!-- /Page Content -->
@endsection
@extends('layouts.app')

@section('title', 'Appointment Confirmed - ' . ($siteSettings['site_name'] ?? 'abcsheba'))

@push('styles')
<style>
    .booking-success-wrapper {
        background-color: #F8FAFC;
        min-height: 80vh;
    }
    .booking-success-card {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.06);
    }
    .booking-success-header {
        background: linear-gradient(135deg, #1D4ED8 0%, #2563EB 50%, #1E40AF 100%) !important;
        padding: 36px 20px;
        text-align: center;
        color: #FFFFFF;
    }
    .booking-success-icon {
        width: 68px;
        height: 68px;
        background: #FFFFFF;
        color: #10B981;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12);
        margin-bottom: 16px;
    }
    .booking-info-box {
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 8px;
        padding: 18px;
    }
    .badge-inclinic {
        background-color: #1D4ED8 !important;
        color: #FFFFFF !important;
        font-weight: 500;
        font-size: 12px;
        padding: 6px 12px;
        border-radius: 4px !important;
        display: inline-block;
    }
    .badge-telemedicine {
        background-color: #0284C7 !important;
        color: #FFFFFF !important;
        font-weight: 500;
        font-size: 12px;
        padding: 6px 12px;
        border-radius: 4px !important;
        display: inline-block;
    }
    .btn-brand-primary {
        background-color: #1D4ED8 !important;
        border-color: #1D4ED8 !important;
        color: #FFFFFF !important;
        border-radius: 4px !important;
        font-weight: 500;
        transition: all 0.2s ease-in-out;
    }
    .btn-brand-primary:hover {
        background-color: #1E40AF !important;
        border-color: #1E40AF !important;
        color: #FFFFFF !important;
    }
    .btn-brand-outline {
        background-color: #FFFFFF !important;
        border: 1px solid #1D4ED8 !important;
        color: #1D4ED8 !important;
        border-radius: 4px !important;
        font-weight: 500;
        transition: all 0.2s ease-in-out;
    }
    .btn-brand-outline:hover {
        background-color: #1D4ED8 !important;
        color: #FFFFFF !important;
    }
    .whatsapp-box {
        background: #F0FDF4;
        border: 1px solid #BBF7D0;
        border-left: 4px solid #25D366 !important;
        border-radius: 8px;
    }
    @media print {
        header, footer, .main-header, .header, .mobile-bottom-nav, .action-buttons-wrap, .whatsapp-box {
            display: none !important;
        }
        .content, .booking-success-wrapper {
            padding: 0 !important;
            background: #FFFFFF !important;
            min-height: auto !important;
        }
        .booking-success-card {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>
@endpush

@section('content')

    <!-- Page Content -->
    <div class="content py-5 booking-success-wrapper">
        <div class="container">

            <div class="row justify-content-center">
                <div class="col-lg-7 col-md-9">

                    <!-- Success Confirmation Card -->
                    <div class="booking-success-card">
                        
                        <!-- Top Brand Royal Blue Gradient Banner -->
                        <div class="booking-success-header">
                            <div>
                                <span class="booking-success-icon">
                                    <i class="fas fa-check"></i>
                                </span>
                            </div>
                            <h3 class="fw-bold mb-1 text-white">অ্যাপয়েন্টমেন্ট সফলভাবে নিশ্চিত হয়েছে!</h3>
                            <p class="mb-0 text-white-50" style="font-size: 14px;">Appointment Confirmed Successfully</p>
                        </div>

                        <div class="card-body p-4 p-md-5">
                            
                            <!-- Token & Status Badge -->
                            @if(session('type') == 'offline' && session('token_number'))
                                <div class="card border-2 mb-4" style="background: #EFF6FF; border: 2px dashed #BFDBFE; border-radius: 8px;">
                                    <div class="card-body text-center py-4">
                                        <span class="text-uppercase fw-semibold small" style="color: #1E40AF; letter-spacing: 1px;">Your Serial / Token Number</span>
                                        <h1 class="display-4 fw-bold my-2" style="color: #1D4ED8; font-weight: 800; letter-spacing: 2px;">
                                            {{ session('token_number') }}
                                        </h1>
                                        <p class="small mb-0" style="color: #475569;">
                                            <i class="fas fa-info-circle me-1" style="color: #1D4ED8;"></i> চেম্বারে উপস্থিত হয়ে রিসেপশনে এই টোকেনটি দেখান।
                                        </p>
                                    </div>
                                </div>
                            @elseif(session('type') == 'online')
                                <div class="card border-2 mb-4" style="background: #EFF6FF; border: 2px dashed #BFDBFE; border-radius: 8px;">
                                    <div class="card-body text-center py-4">
                                        <span class="fw-semibold small text-uppercase" style="color: #1E40AF;">Online Telemedicine Consultation</span>
                                        @if(session('token_number'))
                                            <div class="my-2">
                                                <span class="text-uppercase fw-semibold small d-block text-muted">আপনার অনলাইন সিরিয়াল নম্বর (Serial Token)</span>
                                                <h1 class="display-4 fw-bold my-1" style="color: #1D4ED8; font-weight: 800; letter-spacing: 2px;">
                                                    {{ session('token_number') }}
                                                </h1>
                                            </div>
                                        @endif
                                        <div class="my-3">
                                            @php
                                                $consultLink = session('appointment_id') 
                                                    ? route('appointment.consultation', session('appointment_id')) 
                                                    : (session('meeting_link') ?? '#');
                                            @endphp
                                            <a href="{{ $consultLink }}" class="btn btn-brand-primary btn-lg px-4 py-2 shadow-sm">
                                                <i class="fas fa-video me-2"></i> Join Video Consultation / Waiting Room
                                            </a>
                                        </div>
                                        <p class="small mb-0" style="color: #64748B;">নির্ধারিত সময়ে সরাসরি উপরোক্ত লিংকে ক্লিক করে ডাক্তারের সাথে ভিডিও কলে যুক্ত হবেন। ডাক্তার কল করলে স্বয়ংক্রিয়ভাবে ভিডিও কল শুরু হবে।</p>

                                        @if(session('transaction_id'))
                                            <div class="alert alert-info py-2 px-3 mt-3 mb-0 text-start" style="background: #EFF6FF; border: 1px solid #BFDBFE; color: #1E40AF; border-radius: 6px; font-size: 13px;">
                                                <i class="fas fa-check-circle text-primary me-1"></i> <strong>পেমেন্ট ট্রানজেকশন তথ্য গৃহীত হয়েছে:</strong><br>
                                                <span>মেথড: <strong>{{ strtoupper(session('payment_method') ?? 'bKash') }}</strong> | TrxID: <strong>{{ session('transaction_id') }}</strong></span>
                                                @if(session('sender_number'))
                                                    | প্রেরক: <strong>{{ session('sender_number') }}</strong>
                                                @endif
                                                <div class="text-muted small mt-1">অ্যাডমিন/ডাক্তার ট্রানজেকশন ভেরিফাই করে অ্যাপয়েন্টমেন্ট চূড়ান্তভাবে নিশ্চিত করবেন।</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Appointment Summary Table -->
                            <div class="booking-info-box mb-4">
                                <h6 class="fw-bold border-bottom pb-2 mb-3" style="color: #0F172A; border-color: #E2E8F0 !important;">
                                    <i class="far fa-calendar-alt me-1" style="color: #1D4ED8;"></i> Appointment Information
                                </h6>
                                <div class="row g-2" style="font-size: 14px;">
                                    <div class="col-6" style="color: #64748B;">ডাক্তারের নাম:</div>
                                    <div class="col-6 text-end fw-bold" style="color: #0F172A;">
                                        {{ str_starts_with(strtolower(session('doctor_name') ?? ''), 'dr.') ? session('doctor_name') : 'Dr. ' . (session('doctor_name') ?? 'Doctor') }}
                                    </div>

                                    @if(session('patient_name'))
                                        <div class="col-6" style="color: #64748B;">রোগীর নাম:</div>
                                        <div class="col-6 text-end fw-semibold" style="color: #0F172A;">
                                            {{ session('patient_name') }}
                                        </div>
                                    @endif

                                    <div class="col-6" style="color: #64748B;">অ্যাপয়েন্টমেন্ট তারিখ:</div>
                                    <div class="col-6 text-end fw-semibold" style="color: #0F172A;">
                                        {{ session('date') ? \Carbon\Carbon::parse(session('date'))->format('d M Y (l)') : '-' }}
                                    </div>

                                    <div class="col-6" style="color: #64748B;">সময় ও স্লট:</div>
                                    <div class="col-6 text-end fw-semibold" style="color: #1D4ED8;">
                                        {{ session('time') ? \Carbon\Carbon::parse(session('time'))->format('g:i A') : '-' }}
                                    </div>

                                    <div class="col-6" style="color: #64748B;">ভিজিটের ধরন:</div>
                                    <div class="col-6 text-end">
                                        @if(session('type') === 'online')
                                            <span class="badge-telemedicine">Online Video Call</span>
                                        @else
                                            <span class="badge-inclinic">In-Clinic Chamber Visit</span>
                                        @endif
                                    </div>

                                    @if(session('transaction_id'))
                                        <div class="col-6" style="color: #64748B;">পেমেন্ট স্ট্যাটাস:</div>
                                        <div class="col-6 text-end">
                                            <span class="badge bg-warning text-dark px-2 py-1" style="font-weight: 500; font-size: 12px;">TrxID Received (Pending Verification)</span>
                                        </div>
                                    @elseif(session('type') === 'offline')
                                        <div class="col-6" style="color: #64748B;">পেমেন্ট মেথড:</div>
                                        <div class="col-6 text-end">
                                            <span class="badge bg-secondary text-white px-2 py-1" style="font-weight: 500; font-size: 12px;">Pay at Chamber</span>
                                        </div>
                                    @endif

                                    @if(session('type') !== 'online' && (session('clinic_name') || session('clinic_address')))
                                        <div class="col-6" style="color: #64748B;">চেম্বার / লোকেশন:</div>
                                        <div class="col-6 text-end small fw-semibold" style="color: #0F172A;">
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
                                if ($patientPhone) {
                                    $waLines[] = "🔐 *ভবিষ্যৎ লগইন তথ্য:* ইউজারনেম ও পাসওয়ার্ড হিসেবে আপনার মোবাইল নম্বর ({$patientPhone}) ব্যবহার করুন।";
                                }
                                $waLines[] = "🌐 https://abcsheba.com";

                                $waMessage = implode("\n", $waLines);
                                $waTextEncoded = rawurlencode($waMessage);

                                $waDirectUrl = $waPhone ? "https://api.whatsapp.com/send?phone={$waPhone}&text={$waTextEncoded}" : "https://api.whatsapp.com/send?text={$waTextEncoded}";
                                $waShareUrl = "https://api.whatsapp.com/send?text={$waTextEncoded}";
                            @endphp

                            <!-- WhatsApp Token & Reminder Box -->
                            <div class="card mb-4 border-0 whatsapp-box">
                                <div class="card-body p-3 p-md-4">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="d-inline-flex align-items-center justify-content-center text-white rounded-circle me-2 shadow-sm" style="width: 36px; height: 36px; background: #25D366; font-size: 18px;">
                                            <i class="fab fa-whatsapp"></i>
                                        </span>
                                        <div>
                                            <h6 class="fw-bold mb-0" style="color: #0F172A;">হোয়াটসঅ্যাপে টোকেন ও স্লিপ সেভ রাখুন</h6>
                                            <small style="color: #64748B;">আপনার বা রোগীর হোয়াটসঅ্যাপে বিস্তারিত তথ্য রিমাইন্ডার হিসেবে পাঠিয়ে রাখুন</small>
                                        </div>
                                    </div>

                                    <div class="d-flex flex-wrap gap-2 mt-3">
                                        @if($waPhone)
                                            <a href="{{ $waDirectUrl }}" target="_blank" class="btn text-white fw-semibold px-3 py-2 shadow-sm" style="background: #25D366; font-size: 13px; border-radius: 4px;">
                                                <i class="fab fa-whatsapp me-1 fs-6 align-middle"></i> আমার নম্বরে পাঠান ({{ $patientPhone }})
                                            </a>
                                        @endif
                                        <a href="{{ $waShareUrl }}" target="_blank" class="btn fw-semibold px-3 py-2 bg-white" style="color: #15803D; border: 1px solid #86EFAC; font-size: 13px; border-radius: 4px;">
                                            <i class="fas fa-share-alt me-1"></i> অন্য কারও সাথে শেয়ার করুন
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Patient Account & Login Info Box -->
                            @if($patientPhone)
                                <div class="card mb-4 border-0" style="background: #F8FAFC; border: 1px dashed #93C5FD !important; border-radius: 8px;">
                                    <div class="card-body p-3 p-md-4">
                                        <div class="d-flex align-items-start">
                                            <span class="d-inline-flex align-items-center justify-content-center text-white rounded-circle me-3 flex-shrink-0" style="width: 36px; height: 36px; background: #1D4ED8; font-size: 16px;">
                                                <i class="fas fa-user-lock"></i>
                                            </span>
                                            <div>
                                                <h6 class="fw-bold mb-1" style="color: #1E293B;">রোগীর অ্যাকাউন্ট ও লগইন তথ্য</h6>
                                                <p class="mb-2 text-muted small" style="line-height: 1.5;">
                                                    পরবর্তীতে আপনার অ্যাপয়েন্টমেন্ট হিস্ট্রি, প্রেসক্রিপশন ও ভিডিও কল দেখতে লগইন করতে পারবেন:
                                                </p>
                                                <div class="d-flex flex-wrap gap-3 p-2 px-3 rounded" style="background: #EFF6FF; border: 1px solid #DBEAFE; font-size: 13px;">
                                                    <div><span class="text-muted">লগইন আইডি / ফোন:</span> <strong style="color: #1D4ED8;">{{ $patientPhone }}</strong></div>
                                                    <div><span class="text-muted">ডিফল্ট পাসওয়ার্ড:</span> <strong class="text-dark">{{ $patientPhone }}</strong></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Action Buttons -->
                            <div class="d-flex flex-wrap gap-2 justify-content-center action-buttons-wrap">
                                <a href="javascript:void(0);" onclick="window.print();" class="btn btn-outline-secondary px-4" style="border-radius: 4px; font-weight: 500;">
                                    <i class="fas fa-print me-1"></i> Print Token Slip
                                </a>

                                @if(session('doctor_user_id'))
                                    <a href="{{ route('chat', ['user_id' => session('doctor_user_id')]) }}" class="btn btn-brand-primary px-4">
                                        <i class="far fa-comment-dots me-1"></i> Chat with Doctor
                                    </a>
                                @endif

                                <a href="{{ route('home') }}" class="btn btn-brand-primary px-4">
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
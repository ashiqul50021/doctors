<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Live Video Consultation - {{ $isDoctor ? ($appointment->patient->user->name ?? 'Patient') : ('Dr. ' . ($appointment->doctor->user->name ?? 'Doctor')) }} | abcsheba</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/all.min.css') }}">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <!-- Jitsi Meet External API -->
    <script src="https://meet.jit.si/external_api.js"></script>

    <style>
        :root {
            --bg-dark: #0B1329;
            --bg-card: #151F38;
            --bg-card-light: #1E294B;
            --border-color: #263558;
            --brand-primary: #1D4ED8;
            --brand-accent: #3B82F6;
            --brand-success: #10B981;
            --brand-danger: #EF4444;
            --text-light: #F8FAFC;
            --text-muted: #94A3B8;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-light);
            height: 100vh;
            width: 100vw;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        /* Top Bar */
        .consult-topbar {
            height: 64px;
            background: #0D162F;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            z-index: 100;
            flex-shrink: 0;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .brand-logo-badge {
            font-size: 17px;
            font-weight: 800;
            color: #FFFFFF;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .live-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.4);
            color: #34D399;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .live-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #10B981;
            box-shadow: 0 0 8px #10B981;
            animation: pulse-live 1.8s infinite;
        }

        @keyframes pulse-live {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { transform: scale(1.15); box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        .consult-info-meta {
            font-size: 13px;
            color: var(--text-muted);
            border-left: 1px solid var(--border-color);
            padding-left: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .consult-info-meta strong {
            color: #FFFFFF;
            font-weight: 600;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-consult-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 8px;
            border: none;
            transition: all 0.2s ease;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-prescription {
            background: #2563EB;
            color: #FFFFFF !important;
        }
        .btn-prescription:hover {
            background: #1D4ED8;
            transform: translateY(-1px);
        }

        .btn-complete {
            background: #059669;
            color: #FFFFFF !important;
        }
        .btn-complete:hover {
            background: #047857;
            transform: translateY(-1px);
        }

        .btn-toggle-panel {
            background: var(--bg-card-light);
            color: var(--text-light);
            border: 1px solid var(--border-color);
        }
        .btn-toggle-panel:hover {
            background: #2A3B66;
            color: #FFFFFF;
        }

        .btn-leave {
            background: rgba(239, 68, 68, 0.15);
            color: #F87171 !important;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .btn-leave:hover {
            background: rgba(239, 68, 68, 0.25);
            color: #EF4444 !important;
        }

        /* Consultation Main Workspace */
        .consult-workspace {
            flex: 1;
            display: flex;
            overflow: hidden;
            position: relative;
        }

        /* Video Stage (Left) */
        .video-stage {
            flex: 1;
            height: 100%;
            background: #070B18;
            position: relative;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        #jitsi-meet-container {
            width: 100%;
            height: 100%;
            border: none;
            outline: none;
        }

        /* Jitsi Loader Overlay */
        .jitsi-loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: #0B1329;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 50;
            gap: 16px;
        }

        .jitsi-loading-overlay .spinner-border {
            width: 3rem;
            height: 3rem;
        }

        /* Waiting Room Overlay (For Patients Waiting in Queue) */
        .waiting-room-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 50% 25%, #152243 0%, #070B18 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            z-index: 60;
            overflow-y: auto;
        }

        .waiting-card {
            background: rgba(21, 31, 56, 0.96);
            border: 1px solid rgba(59, 130, 246, 0.35);
            border-radius: 20px;
            padding: 36px 32px;
            max-width: 520px;
            width: 100%;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6), 0 0 35px rgba(37, 99, 235, 0.25);
            text-align: center;
        }

        .waiting-live-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(37, 99, 235, 0.15);
            border: 1px solid rgba(59, 130, 246, 0.5);
            color: #60A5FA;
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 16px;
        }

        .waiting-live-badge .pulse-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #3B82F6;
            box-shadow: 0 0 10px #3B82F6;
            animation: pulse-live 1.6s infinite;
        }

        .waiting-title {
            color: #FFFFFF;
            font-size: 24px;
            font-weight: 800;
            margin-bottom: 6px;
        }

        .waiting-sub {
            color: #94A3B8;
            font-size: 13.5px;
            margin-bottom: 24px;
        }

        .doctor-preview-box {
            display: flex;
            align-items: center;
            gap: 16px;
            background: rgba(30, 41, 75, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.08);
            padding: 14px 18px;
            border-radius: 14px;
            text-align: left;
            margin-bottom: 24px;
        }

        .doctor-avatar-circle {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #3B82F6;
        }

        .queue-status-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 20px;
        }

        .queue-status-col {
            background: rgba(13, 22, 47, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 14px;
            padding: 16px 12px;
        }

        .queue-status-col.highlight {
            background: linear-gradient(135deg, rgba(29, 78, 216, 0.25) 0%, rgba(30, 41, 75, 0.6) 100%);
            border-color: rgba(59, 130, 246, 0.6);
        }

        .queue-status-col .label {
            display: block;
            color: #94A3B8;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .queue-status-col .token-number {
            font-size: 32px;
            font-weight: 800;
            color: #60A5FA;
            letter-spacing: 1px;
        }

        .queue-status-col .serving-number {
            font-size: 26px;
            font-weight: 700;
            color: #F8FAFC;
            letter-spacing: 1px;
        }

        .queue-position-alert {
            background: rgba(245, 158, 11, 0.12);
            border: 1px solid rgba(245, 158, 11, 0.35);
            color: #FBBF24;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 13.5px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .waiting-instructions {
            background: rgba(11, 19, 41, 0.6);
            border-radius: 12px;
            padding: 14px 18px;
            margin-bottom: 20px;
            text-align: left;
            display: flex;
            flex-direction: column;
            gap: 8px;
            font-size: 13px;
            color: #CBD5E1;
        }

        .instruction-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .waiting-pulse-indicator {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12.5px;
            color: #94A3B8;
        }

        /* Side Panel (Right) */
        .medical-side-panel {
            width: 380px;
            max-width: 420px;
            height: 100%;
            background: var(--bg-card);
            border-left: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            z-index: 90;
        }

        .medical-side-panel.collapsed {
            width: 0 !important;
            border-left: none;
        }

        .panel-nav-tabs {
            display: flex;
            border-bottom: 1px solid var(--border-color);
            background: #0F1833;
            flex-shrink: 0;
        }

        .panel-nav-btn {
            flex: 1;
            background: transparent;
            border: none;
            padding: 12px 8px;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            border-bottom: 2px solid transparent;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
        }

        .panel-nav-btn.active {
            color: #60A5FA;
            border-bottom-color: #3B82F6;
            background: rgba(59, 130, 246, 0.08);
        }

        .panel-body {
            flex: 1;
            overflow-y: auto;
            padding: 18px;
        }

        .panel-body::-webkit-scrollbar {
            width: 6px;
        }
        .panel-body::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 4px;
        }

        /* Patient / Doctor Card */
        .profile-summary-box {
            background: var(--bg-card-light);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 16px;
            text-align: center;
        }

        .summary-avatar {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #3B82F6;
            margin: 0 auto 10px;
            display: block;
        }

        .summary-name {
            font-size: 16px;
            font-weight: 700;
            color: #FFFFFF;
            margin-bottom: 2px;
        }

        .summary-sub {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 10px;
        }

        .badge-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            justify-content: center;
        }

        .badge-chip {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 12px;
            color: #CBD5E1;
        }

        /* Section Cards inside Panel */
        .panel-section-card {
            background: var(--bg-card-light);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 14px;
            margin-bottom: 14px;
        }

        .section-card-title {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #93C5FD;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .report-pill-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid var(--border-color);
            padding: 8px 12px;
            border-radius: 8px;
            color: #E2E8F0;
            text-decoration: none;
            font-size: 13px;
            margin-bottom: 6px;
            transition: all 0.2s ease;
        }

        .report-pill-link:hover {
            background: rgba(59, 130, 246, 0.15);
            border-color: #3B82F6;
            color: #FFFFFF;
        }

        /* Medicine Preview List */
        .med-preview-item {
            background: rgba(255, 255, 255, 0.04);
            border-left: 3px solid #3B82F6;
            padding: 10px 12px;
            border-radius: 0 8px 8px 0;
            margin-bottom: 8px;
        }

        .med-name {
            font-weight: 700;
            font-size: 13.5px;
            color: #FFFFFF;
        }

        .med-meta {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .status-badge-inline {
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .status-confirmed { background: rgba(16, 185, 129, 0.2); color: #34D399; }
        .status-pending { background: rgba(245, 158, 11, 0.2); color: #FBBF24; }
        .status-completed { background: rgba(59, 130, 246, 0.2); color: #60A5FA; }

        @media (max-width: 991px) {
            .consult-topbar {
                padding: 0 12px;
            }
            .consult-info-meta {
                display: none;
            }
            .medical-side-panel {
                position: absolute;
                top: 0;
                right: 0;
                bottom: 0;
                width: 320px;
                box-shadow: -10px 0 30px rgba(0, 0, 0, 0.7);
            }
            .medical-side-panel.collapsed {
                transform: translateX(100%);
                width: 320px !important;
            }
        }
    </style>
</head>
<body>

    <!-- 1. Focused Consultation Top Bar -->
    <header class="consult-topbar">
        <div class="topbar-left">
            <a href="{{ $isDoctor ? route('doctors.dashboard') : route('patient.dashboard') }}" class="brand-logo-badge" title="Back to Dashboard">
                <i class="fas fa-stethoscope text-primary"></i> abcsheba
            </a>

            <span class="live-pill">
                <span class="live-dot"></span> LIVE
            </span>

            <div class="consult-info-meta">
                @if($appointment->token_number)
                    <span class="badge bg-primary text-white fw-bold px-2.5 py-1" style="font-size: 12px; letter-spacing: 0.5px;">
                        <i class="fas fa-ticket-alt me-1"></i> {{ $appointment->token_number }}
                    </span>
                    <span>•</span>
                @endif
                <span>Appointment <strong>#{{ $appointment->id }}</strong></span>
                <span>•</span>
                <span>{{ $appointment->appointment_date->format('d M, Y') }} at {{ $appointment->appointment_time->format('h:i A') }}</span>
                <span>•</span>
                <span class="status-badge-inline status-{{ $appointment->status }}" id="apptStatusBadge">
                    {{ ucfirst($appointment->status) }}
                </span>
            </div>
        </div>

        <div class="topbar-right">
            <!-- Doctor-Specific Actions -->
            @if($isDoctor)
                @if(!empty($nextOnlineAppt))
                    <form action="{{ route('appointment.consultation.call_next', $appointment->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Complete this consultation and call next patient ({{ $nextOnlineAppt->token_number ?? 'Next' }})?');">
                        @csrf
                        <button type="submit" class="btn-consult-action" style="background:#2563EB; color:#fff;" title="Complete current and call next online patient">
                            <i class="fas fa-forward me-1"></i> Call Next ({{ $nextOnlineAppt->token_number ?? 'Next' }})
                        </button>
                    </form>
                @endif

                <a href="{{ route('doctors.add.prescription', $appointment->id) }}" target="_blank" class="btn-consult-action btn-prescription" title="Write & Issue Prescription in a new tab">
                    <i class="fas fa-file-prescription"></i> Write Prescription
                </a>

                @if($appointment->status !== 'completed')
                    <button type="button" class="btn-consult-action btn-complete" id="completeConsultBtn" onclick="markConsultationCompleted()">
                        <i class="fas fa-check-circle"></i> Complete
                    </button>
                @endif
            @endif

            <!-- Toggle Side Panel -->
            <button type="button" class="btn-consult-action btn-toggle-panel" id="togglePanelBtn" onclick="toggleSidePanel()" title="Toggle Medical Details">
                <i class="fas fa-notes-medical"></i> <span class="d-none d-sm-inline">Notes</span>
            </button>

            <!-- Exit Call -->
            <a href="{{ $isDoctor ? route('doctors.dashboard') : route('patient.dashboard') }}" class="btn-consult-action btn-leave" title="Leave Consultation">
                <i class="fas fa-phone-slash"></i> <span class="d-none d-md-inline">Leave</span>
            </a>
        </div>
    </header>

    <!-- 2. Main Consultation Workspace (Video + Side Panel) -->
    <main class="consult-workspace">
        
        <!-- Video Conference Stage -->
        <section class="video-stage" id="videoStage">
            @if($isPatient && !$isServing)
                <!-- Waiting Room for Patient Until Doctor Calls -->
                <div id="waitingRoomStage" class="waiting-room-overlay">
                    <div class="waiting-card">
                        <div class="waiting-header">
                            <span class="waiting-live-badge">
                                <span class="pulse-dot"></span> LIVE WAITING ROOM
                            </span>
                            <h3 class="waiting-title">ডাক্তারের কলিং অপেক্ষা কক্ষ</h3>
                            <p class="waiting-sub">ডাক্তার প্রস্তুত হয়ে কল করলেই স্বয়ংক্রিয়ভাবে ভিডিও কল শুরু হবে</p>
                        </div>

                        <div class="doctor-preview-box">
                            <img src="{{ $appointment->doctor?->user?->profile_image ? asset($appointment->doctor->user->profile_image) : asset('assets/img/doctors/doctor-thumb-02.jpg') }}" 
                                 alt="Doctor Avatar" class="doctor-avatar-circle">
                            <div>
                                <h5 class="mb-1 text-white fw-bold">Dr. {{ $appointment->doctor?->user?->name ?? 'Doctor' }}</h5>
                                <span class="badge bg-primary-subtle text-primary">{{ $appointment->doctor?->speciality?->name ?? 'Specialist' }}</span>
                            </div>
                        </div>

                        <div class="queue-status-grid">
                            <div class="queue-status-col highlight">
                                <span class="label">আপনার অনলাইন সিরিয়াল</span>
                                <div class="token-number">{{ $appointment->token_number ?? 'ONL-1' }}</div>
                            </div>
                            <div class="queue-status-col">
                                <span class="label">বর্তমানে ডাকা হচ্ছে</span>
                                <div class="serving-number" id="currentServingToken">{{ $servingToken ?? 'শুরু হয়নি' }}</div>
                            </div>
                        </div>

                        <div class="queue-position-alert" id="aheadCountBox">
                            <i class="fas fa-user-clock me-2"></i>
                            <span id="aheadCountText">
                                @if($aheadCount > 0)
                                    আপনার পূর্বে <strong>{{ $aheadCount }}</strong> জন রোগী সিরিয়ালে আছেন।
                                @else
                                    আপনি পরবর্তী সিরিয়ালে আছেন। ডাক্তার খুব শীঘ্রই কল করবেন।
                                @endif
                            </span>
                        </div>

                        <div class="waiting-instructions">
                            <div class="instruction-item">
                                <i class="fas fa-check-circle text-success"></i>
                                <span>অনুগ্রহ করে এই ব্রাউজার ট্যাবটি খোলা রাখুন</span>
                            </div>
                            <div class="instruction-item">
                                <i class="fas fa-check-circle text-success"></i>
                                <span>ক্যামেরা ও মাইক্রোফোন পারমিশন প্রস্তুত রাখুন</span>
                            </div>
                            <div class="instruction-item">
                                <i class="fas fa-volume-up text-info"></i>
                                <span>ডাক্তার কল দিলে অ্যালার্ম শব্দ বেজে উঠবে</span>
                            </div>
                        </div>

                        <div class="waiting-pulse-indicator">
                            <div class="spinner-grow spinner-grow-sm text-primary me-2" role="status"></div>
                            <span>লাইভ সংযোগ সক্রিয় রয়েছে...</span>
                        </div>
                    </div>
                </div>
            @endif

            <div id="jitsiLoader" class="jitsi-loading-overlay" @if($isPatient && !$isServing) style="display:none;" @endif>
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Connecting...</span>
                </div>
                <h5 class="fw-bold text-white mb-1">Connecting to Secure Video Room...</h5>
                <p class="text-muted small mb-0">Please allow camera and microphone permissions in your browser.</p>
            </div>

            <!-- Jitsi Meet Iframe Container -->
            <div id="jitsi-meet-container" @if($isPatient && !$isServing) style="display:none;" @endif></div>
        </section>

        <!-- Right Side Panel: Patient Profile & Prescription Pad -->
        <aside class="medical-side-panel" id="medicalSidePanel">
            <!-- Navigation Tabs -->
            <div class="panel-nav-tabs">
                <button type="button" class="panel-nav-btn active" onclick="switchPanelTab(event, 'patientTab')">
                    <i class="fas fa-user-injured me-1"></i> {{ $isDoctor ? 'Patient Info' : 'Doctor Info' }}
                </button>
                <button type="button" class="panel-nav-btn" onclick="switchPanelTab(event, 'rxTab')">
                    <i class="fas fa-prescription me-1"></i> Prescription
                </button>
            </div>

            <!-- Panel Body -->
            <div class="panel-body">
                
                <!-- TAB 1: Profile & Medical History -->
                <div id="patientTab" class="tab-pane-content">
                    
                    @if($isDoctor)
                        <!-- Patient Profile (Visible to Doctor) -->
                        <div class="profile-summary-box">
                            <img src="{{ $appointment->patient?->user?->profile_image ? asset($appointment->patient->user->profile_image) : asset('assets/img/patients/patient.jpg') }}" 
                                 alt="Patient Avatar" 
                                 class="summary-avatar">
                            <h4 class="summary-name">{{ $appointment->patient?->user?->name ?? 'Patient' }}</h4>
                            <p class="summary-sub">{{ $appointment->patient?->user?->email ?? 'No email provided' }}</p>

                            <div class="badge-grid">
                                @if($appointment->patient?->phone)
                                    <span class="badge-chip"><i class="fas fa-phone me-1"></i> {{ $appointment->patient->phone }}</span>
                                @endif
                                @if($appointment->patient?->blood_group)
                                    <span class="badge-chip"><i class="fas fa-tint me-1 text-danger"></i> {{ $appointment->patient->blood_group }}</span>
                                @endif
                                @if($appointment->patient?->gender)
                                    <span class="badge-chip"><i class="fas fa-venus-mars me-1"></i> {{ ucfirst($appointment->patient->gender) }}</span>
                                @endif
                                @if($appointment->patient?->age)
                                    <span class="badge-chip"><i class="fas fa-calendar-alt me-1"></i> {{ $appointment->patient->age }} Yrs</span>
                                @endif
                            </div>
                        </div>

                        <!-- Problem & Symptoms -->
                        <div class="panel-section-card">
                            <div class="section-card-title">
                                <i class="fas fa-notes-medical"></i> Patient's Problem / Symptoms
                            </div>
                            <p class="text-light small mb-0">
                                {{ $appointment->notes ?: 'No specific problem description provided during booking.' }}
                            </p>
                        </div>

                        <!-- Uploaded Reports & Previous Prescriptions -->
                        <div class="panel-section-card">
                            <div class="section-card-title">
                                <i class="fas fa-file-medical"></i> Attached Medical Reports
                            </div>
                            @if(!empty($appointment->reports) && count($appointment->reports) > 0)
                                @foreach($appointment->reports as $index => $reportPath)
                                    <a href="{{ asset('storage/' . $reportPath) }}" target="_blank" class="report-pill-link">
                                        <span><i class="far fa-file-alt text-primary me-2"></i> Report File #{{ $index + 1 }}</span>
                                        <i class="fas fa-external-link-alt text-muted small"></i>
                                    </a>
                                @endforeach
                            @else
                                <p class="text-muted small mb-0">No previous reports attached by patient.</p>
                            @endif
                        </div>

                    @else
                        <!-- Doctor Profile (Visible to Patient) -->
                        <div class="profile-summary-box">
                            <img src="{{ $appointment->doctor?->user?->profile_image ? asset($appointment->doctor->user->profile_image) : asset('assets/img/doctors/doctor-thumb-02.jpg') }}" 
                                 alt="Doctor Avatar" 
                                 class="summary-avatar">
                            <h4 class="summary-name">Dr. {{ $appointment->doctor?->user?->name ?? 'Doctor' }}</h4>
                            <p class="summary-sub text-primary fw-semibold">{{ $appointment->doctor?->speciality?->name ?? 'Specialist' }}</p>

                            <div class="badge-grid">
                                @if($appointment->doctor?->qualification)
                                    <span class="badge-chip"><i class="fas fa-graduation-cap me-1"></i> {{ $appointment->doctor->qualification }}</span>
                                @endif
                                @if($appointment->doctor?->experience)
                                    <span class="badge-chip"><i class="fas fa-briefcase me-1"></i> {{ $appointment->doctor->experience }} Years Exp</span>
                                @endif
                            </div>
                        </div>

                        <div class="panel-section-card">
                            <div class="section-card-title">
                                <i class="fas fa-clinic-medical"></i> Chamber / Hospital
                            </div>
                            <h6 class="text-white fw-bold mb-1">{{ $appointment->doctor?->clinic_name ?? 'Online Consultation Chamber' }}</h6>
                            <p class="text-muted small mb-0">{{ $appointment->doctor?->clinic_address ?? 'Consultation handled via secure video call.' }}</p>
                        </div>

                        <div class="panel-section-card">
                            <div class="section-card-title">
                                <i class="fas fa-info-circle"></i> Instructions for Patient
                            </div>
                            <ul class="text-muted small ps-3 mb-0">
                                <li>Ensure camera & mic are unmuted.</li>
                                <li>Sit in a well-lit, quiet room.</li>
                                <li>Keep any past prescriptions or lab reports handy.</li>
                            </ul>
                        </div>
                    @endif

                </div>

                <!-- TAB 2: Prescription / Clinical Advice -->
                <div id="rxTab" class="tab-pane-content" style="display: none;">
                    @if($prescription)
                        <div class="panel-section-card">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="section-card-title mb-0">
                                    <i class="fas fa-file-prescription"></i> Prescription Summary
                                </div>
                                <a href="{{ route('doctors.view.prescription', $prescription->id) }}" target="_blank" class="btn btn-sm btn-outline-info py-0 px-2" style="font-size: 11px;">
                                    <i class="fas fa-print me-1"></i> View / Print
                                </a>
                            </div>

                            @if($prescription->diagnosis)
                                <div class="mb-3">
                                    <span class="text-muted small d-block">Diagnosis:</span>
                                    <span class="text-white fw-semibold small">{{ $prescription->diagnosis }}</span>
                                </div>
                            @endif

                            @if($prescription->items && $prescription->items->count() > 0)
                                <div class="section-card-title mt-3 mb-2" style="font-size: 12px;">Prescribed Medicines:</div>
                                @foreach($prescription->items as $item)
                                    <div class="med-preview-item">
                                        <div class="med-name">{{ $item->medicine_name }}</div>
                                        <div class="med-meta">
                                            <span>Qty: {{ $item->quantity ?: '1' }}</span> • 
                                            <span>Duration: {{ $item->days ?: 'N/A' }}</span>
                                            <div class="mt-1">
                                                <span class="badge {{ $item->morning ? 'bg-primary' : 'bg-secondary' }}" style="font-size: 10px;">M</span>
                                                <span class="badge {{ $item->afternoon ? 'bg-primary' : 'bg-secondary' }}" style="font-size: 10px;">A</span>
                                                <span class="badge {{ $item->evening ? 'bg-primary' : 'bg-secondary' }}" style="font-size: 10px;">E</span>
                                                <span class="badge {{ $item->night ? 'bg-primary' : 'bg-secondary' }}" style="font-size: 10px;">N</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            @if($prescription->notes)
                                <div class="mt-3">
                                    <span class="text-muted small d-block">Doctor's Advice:</span>
                                    <p class="text-light small mb-0">{{ $prescription->notes }}</p>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-file-medical-alt fs-2 mb-2 d-block opacity-50"></i>
                            <h6 class="text-white fw-bold mb-1">No Prescription Issued Yet</h6>
                            <p class="small mb-3">
                                @if($isDoctor)
                                    You can write and issue an official digital prescription for this patient right now.
                                @else
                                    Doctor has not finalized the digital prescription yet. Once completed, it will appear here.
                                @endif
                            </p>
                            @if($isDoctor)
                                <a href="{{ route('doctors.add.prescription', $appointment->id) }}" target="_blank" class="btn btn-sm btn-primary">
                                    <i class="fas fa-pen-alt me-1"></i> Open Prescription Pad
                                </a>
                            @endif
                        </div>
                    @endif
                </div>

            </div>
        </aside>
    </main>

    <!-- SweetAlert2 & Toastr JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        const domain = 'meet.jit.si';
        const roomName = "{{ $roomName }}";
        const displayName = "{{ $displayName }}";
        const isPatient = {{ $isPatient ? 'true' : 'false' }};
        const shouldInitNow = {{ ($isServing || $isDoctor || $isAdmin) ? 'true' : 'false' }};

        let jitsiApiInstance = null;
        let pollTimer = null;

        // Sound chime when doctor calls
        function playChime() {
            try {
                const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.connect(gain);
                gain.connect(audioCtx.destination);
                osc.frequency.setValueAtTime(587.33, audioCtx.currentTime); // D5
                gain.gain.setValueAtTime(0.3, audioCtx.currentTime);
                osc.start();
                osc.frequency.exponentialRampToValueAtTime(880, audioCtx.currentTime + 0.3); // A5
                gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.6);
                osc.stop(audioCtx.currentTime + 0.6);
            } catch(e) {
                console.log('Audio chime error:', e);
            }
        }

        // 1. Initialize Jitsi Meet Embedded Session
        function initJitsi() {
            if (jitsiApiInstance) return;

            const options = {
                roomName: roomName,
                width: '100%',
                height: '100%',
                parentNode: document.querySelector('#jitsi-meet-container'),
                userInfo: {
                    displayName: displayName
                },
                configOverwrite: {
                    startWithAudioMuted: false,
                    startWithVideoMuted: false,
                    prejoinPageEnabled: false,
                    disableDeepLinking: true,
                    enableWelcomePage: false,
                    enableClosePage: false
                },
                interfaceConfigOverwrite: {
                    SHOW_JITSI_WATERMARK: false,
                    SHOW_WATERMARK_FOR_GUESTS: false,
                    SHOW_BRAND_WATERMARK: false,
                    SHOW_POWERED_BY: false,
                    DEFAULT_BACKGROUND: '#070B18',
                    TOOLBAR_BUTTONS: [
                        'microphone', 'camera', 'desktop', 'chat', 'raisehand',
                        'tileview', 'fullscreen', 'hangup', 'settings'
                    ]
                }
            };

            try {
                jitsiApiInstance = new JitsiMeetExternalAPI(domain, options);

                jitsiApiInstance.addEventListeners({
                    videoConferenceJoined: function () {
                        const loader = document.getElementById('jitsiLoader');
                        if (loader) loader.style.display = 'none';
                    },
                    readyToClose: function () {
                        // User clicked Jitsi red hangup button
                        window.location.href = "{{ $isDoctor ? route('doctors.dashboard') : route('patient.dashboard') }}";
                    }
                });
            } catch (err) {
                console.error('Failed to init Jitsi Meet:', err);
                const loader = document.getElementById('jitsiLoader');
                if (loader) {
                    loader.innerHTML = `
                        <i class="fas fa-exclamation-triangle text-warning fs-1 mb-2"></i>
                        <h5 class="text-white">Could not load video meeting engine</h5>
                        <p class="text-muted small">You can still join via direct link below:</p>
                        <a href="https://${domain}/${roomName}" target="_blank" class="btn btn-sm btn-primary">Open in New Tab</a>
                    `;
                }
            }
        }

        // 2. Poll Waiting Room Status for Patient
        function pollQueueStatus() {
            fetch("{{ route('appointment.consultation.status', $appointment->id) }}")
                .then(res => res.json())
                .then(data => {
                    if (data.is_serving) {
                        if (pollTimer) clearInterval(pollTimer);
                        playChime();
                        toastr.success('ডাক্তার আপনাকে ডেকেছেন! ভিডিও কলে যুক্ত হওয়া হচ্ছে...', 'আপনার সিরিয়াল এসেছে');
                        
                        const waitingOverlay = document.getElementById('waitingRoomStage');
                        if (waitingOverlay) waitingOverlay.style.display = 'none';

                        const loader = document.getElementById('jitsiLoader');
                        if (loader) loader.style.display = 'flex';

                        const container = document.getElementById('jitsi-meet-container');
                        if (container) container.style.display = 'block';

                        initJitsi();
                    } else {
                        const sTokenEl = document.getElementById('currentServingToken');
                        if (sTokenEl && data.serving_token) {
                            sTokenEl.textContent = data.serving_token;
                        }
                        const aheadTxt = document.getElementById('aheadCountText');
                        if (aheadTxt) {
                            if (data.ahead_count > 0) {
                                aheadTxt.innerHTML = `আপনার পূর্বে <strong>${data.ahead_count}</strong> জন রোগী সিরিয়ালে আছেন।`;
                            } else {
                                aheadTxt.innerHTML = `আপনি পরবর্তী সিরিয়ালে আছেন। ডাক্তার খুব শীঘ্রই কল করবেন।`;
                            }
                        }
                    }
                })
                .catch(err => console.error('Status poll error:', err));
        }

        document.addEventListener('DOMContentLoaded', function () {
            if (shouldInitNow) {
                initJitsi();
            } else if (isPatient) {
                pollTimer = setInterval(pollQueueStatus, 4000);
            }
        });

        // 2. Toggle Side Panel
        function toggleSidePanel() {
            const panel = document.getElementById('medicalSidePanel');
            if (panel) {
                panel.classList.toggle('collapsed');
            }
        }

        // 3. Switch Tabs in Side Panel
        function switchPanelTab(e, tabId) {
            document.querySelectorAll('.panel-nav-btn').forEach(btn => btn.classList.remove('active'));
            if (e && e.currentTarget) e.currentTarget.classList.add('active');

            document.querySelectorAll('.tab-pane-content').forEach(pane => pane.style.display = 'none');
            const target = document.getElementById(tabId);
            if (target) target.style.display = 'block';
        }

        // 4. Doctor: Mark Consultation Completed via AJAX
        function markConsultationCompleted() {
            Swal.fire({
                title: 'Complete Consultation?',
                text: "Are you sure you want to mark this consultation as completed?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#059669',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, Complete',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    const url = "{{ route('appointment.consultation.complete', $appointment->id) }}";
                    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Consultation Completed!',
                                text: 'The appointment status has been updated to completed.',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            const badge = document.getElementById('apptStatusBadge');
                            if (badge) {
                                badge.className = 'status-badge-inline status-completed';
                                badge.textContent = 'Completed';
                            }
                            const btn = document.getElementById('completeConsultBtn');
                            if (btn) btn.style.display = 'none';
                        } else {
                            Swal.fire('Error', data.message || 'Could not update status', 'error');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire('Error', 'Server connection error.', 'error');
                    });
                }
            });
        }
    </script>
</body>
</html>

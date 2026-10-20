@extends('layouts.app')

@section('title', 'Doctor Dashboard - ' . ($siteSettings['site_name'] ?? 'abcsheba'))

@section('content')

@push('styles')
<style>
    /* Global Dashboard Adjustments */
    .content {
        background-color: #f8f9fa;
        padding: 40px 0;
    }
        background-color: #f73563;
        color: #fff;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    /* Main Content Widgets (Total Patients etc.) */
    .dash-card {
        border-radius: 15px;
        border: none;
        box-shadow: 0 4px 25px rgba(0, 0, 0, 0.05);
        margin-bottom: 30px;
    }

    .dash-widget {
        display: flex;
        align-items: center;
        padding: 25px 20px;
        position: relative;
    }

    .dct-border-rht {
        border-right: 1px solid #f0f0f0;
    }

    /* Modern abstract shapes/colors for widget icons */
    .circle-bar {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 20px;
        flex-shrink: 0;
        position: relative;
        overflow: hidden;
    }

    /* Backgrounds for icons */
    .circle-bar1 { background: rgba(52, 92, 206, 0.1); } /* Blue */
    .circle-bar2 { background: rgba(9, 229, 171, 0.1); }  /* Teal */
    .circle-bar3 { background: rgba(247, 53, 99, 0.1); }  /* Red */

    .circle-bar img {
        width: 32px;
        height: 32px;
        z-index: 2;
        object-fit: contain;
    }

    .dash-widget-info {
        flex: 1;
    }

    .dash-widget-info h6 {
        font-size: 0.95rem;
        color: #6b7280;
        font-weight: 500;
        margin-bottom: 5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .dash-widget-info h3 {
        font-size: 1.8rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 3px;
    }

    .dash-widget-info p {
        font-size: 0.85rem;
        margin-bottom: 0;
        color: #9ca3af !important;
    }

    /* Data Tables Styling */
    h4.mb-4 {
        font-weight: 700;
        color: #1f2937;
        font-size: 1.5rem;
    }

    .appointment-tab .nav-tabs {
        border-bottom: 2px solid #f0f0f0;
        margin-bottom: 20px;
    }

    .appointment-tab .nav-tabs .nav-link {
        border: none;
        color: #6b7280;
        font-weight: 600;
        padding: 12px 25px;
        border-radius: 0;
        position: relative;
    }

    .appointment-tab .nav-tabs .nav-link:hover {
        color: #1D4ED8;
        background: transparent;
    }

    .appointment-tab .nav-tabs .nav-link.active,
    .appointment-tab .nav-tabs .nav-link.active:hover,
    .appointment-tab .nav-tabs .nav-link.active:focus {
        color: #1D4ED8 !important;
        background-color: transparent !important;
        border-color: transparent !important;
    }

    .appointment-tab .nav-tabs .nav-link.active::after {
        content: "";
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 100%;
        height: 2px;
        background-color: #1D4ED8;
    }

    .card-table {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 25px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .table {
        margin-bottom: 0;
    }

    .table thead th {
        border-top: none;
        border-bottom: 1px solid #f0f0f0;
        font-weight: 600;
        color: #4b5563;
        background-color: #fcfcfc;
        padding: 15px 20px;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
    }

    .table tbody tr {
        transition: all 0.2s;
        border-bottom: 1px solid #f0f0f0;
    }

    .table tbody tr:hover {
        background-color: #f9fafb;
    }

    .table tbody td {
        padding: 15px 20px;
        vertical-align: middle;
        color: #4b5563;
        font-weight: 500;
        border: none;
    }

    .table-avatar {
        display: flex;
        align-items: center;
        margin: 0;
        font-size: 1rem;
    }

    .avatar {
        white-space: nowrap;
        border-radius: 50%;
        position: relative;
        display: inline-block;
        width: 40px;
        height: 40px;
    }

    .avatar-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .table-avatar a {
        color: #1f2937;
        font-weight: 600;
        text-decoration: none;
    }
    .table-avatar a:hover {
        color: #345cce;
    }

    .table-avatar span {
        display: block;
        color: #9ca3af;
        font-size: 0.8rem;
        font-weight: 500;
        margin-top: 2px;
    }

    /* Action Buttons in Table */
    .btn-sm {
        padding: 6px 12px;
        font-size: 0.85rem;
        border-radius: 6px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.2s;
    }

    .bg-info-light { background-color: rgba(2, 182, 179, 0.1) !important; color: #02b6b3 !important; }
    .bg-info-light:hover { background-color: #02b6b3 !important; color: #fff !important; }

    .bg-success-light { background-color: rgba(15, 183, 107, 0.1) !important; color: #0fb76b !important; border: none; }
    .bg-success-light:hover { background-color: #0fb76b !important; color: #fff !important; }

    .bg-danger-light { background-color: rgba(242, 17, 54, 0.1) !important; color: #f21136 !important; border: none; }
    .bg-danger-light:hover { background-color: #f21136 !important; color: #fff !important; }

    .badge {
        font-size: 0.8rem;
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 20px;
    }

    @media (max-width: 991px) {
        .dct-border-rht {
            border-right: none;
            border-bottom: 1px solid #f0f0f0;
        }
    }
</style>
@endpush
    <div class="content">
        <div class="container">

            <div class="row">
                <div class="col-md-5 col-lg-4 col-xl-3 theiaStickySidebar">

                    @include('frontend.includes.doctor-sidebar')

                </div>

                <div class="col-md-7 col-lg-8 col-xl-9">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card dash-card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 col-lg-4">
                                            <div class="dash-widget dct-border-rht">
                                                <div class="circle-bar circle-bar1">
                                                    <div class="circle-graph1" data-percent="75">
                                                        <img src="{{ asset('assets/img/icon-01.png') }}" class="img-fluid"
                                                            alt="patient">
                                                    </div>
                                                </div>
                                                <div class="dash-widget-info">
                                                    <h6>Total Patient</h6>
                                                    <h3>{{ $totalPatients }}</h3>
                                                    <p class="text-muted">Till Today</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12 col-lg-4">
                                            <div class="dash-widget dct-border-rht">
                                                <div class="circle-bar circle-bar2">
                                                    <div class="circle-graph2" data-percent="65">
                                                        <img src="{{ asset('assets/img/icon-02.png') }}" class="img-fluid"
                                                            alt="Patient">
                                                    </div>
                                                </div>
                                                <div class="dash-widget-info">
                                                    <h6>Today Patient</h6>
                                                    <h3>{{ $todayPatients }}</h3>
                                                    <p class="text-muted">{{ now()->format('d, M Y') }}</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12 col-lg-4">
                                            <div class="dash-widget">
                                                <div class="circle-bar circle-bar3">
                                                    <div class="circle-graph3" data-percent="50">
                                                        <img src="{{ asset('assets/img/icon-03.png') }}" class="img-fluid"
                                                            alt="Patient">
                                                    </div>
                                                </div>
                                                <div class="dash-widget-info">
                                                    <h6>Appointments</h6>
                                                    <h3>{{ $totalAppointments }}</h3>
                                                    <p class="text-muted">Total Booked</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <h4 class="mb-4">Patient Appoinment</h4>
                            <div class="appointment-tab">

                                <!-- Appointment Tab -->
                                <ul class="nav nav-tabs">
                                    <li class="nav-item">
                                        <a class="nav-link active" href="#upcoming-appointments"
                                            data-bs-toggle="tab">Upcoming</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#today-appointments" data-bs-toggle="tab">Today</a>
                                    </li>
                                </ul>
                                <!-- /Appointment Tab -->

                                <div class="tab-content">

                                    <!-- Upcoming Appointment Tab -->
                                    <div class="tab-pane show active" id="upcoming-appointments">
                                        <div class="card card-table mb-0">
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-hover table-center mb-0">
                                                        <thead>
                                                            <tr>
                                                                <th>Patient Name</th>
                                                                <th>Appt Date</th>
                                                                <th>Purpose</th>
                                                                <th>Token / Serial</th>
                                                                <th>Type</th>
                                                                <th class="text-center">Paid Amount</th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse($upcomingAppointments as $appt)
                                                                <tr>
                                                                    <td>
                                                                        <h2 class="table-avatar">
                                                                            <a href="{{ route('patient.profile', $appt->patient->id ?? '') }}"
                                                                                class="avatar avatar-sm me-2"><img
                                                                                    class="avatar-img rounded-circle"
                                                                                    src="{{ optional($appt->patient)->profile_image ? asset($appt->patient->profile_image) : asset('assets/img/patients/patient.jpg') }}"
                                                                                    alt="Patient Image"></a>
                                                                            <a href="{{ route('patient.profile', $appt->patient->id ?? '') }}">
                                                                                {{ optional(optional($appt->patient)->user)->name ?? 'Unknown' }} 
                                                                                <span>#PT{{ sprintf('%04d', $appt->patient_id) }}</span>
                                                                                @if(optional($appt->patient)->phone)
                                                                                    <span class="d-block text-muted" style="font-size:12px;font-weight:normal;"><i class="fas fa-phone-alt me-1 text-success"></i>{{ $appt->patient->phone }}</span>
                                                                                @endif
                                                                            </a>
                                                                        </h2>
                                                                    </td>
                                                                    <td>{{ \Carbon\Carbon::parse($appt->appointment_date)->format('d M Y') }} <span class="d-block text-info font-weight-600">{{ \Carbon\Carbon::parse($appt->appointment_time)->format('h:i A') }}</span></td>
                                                                    <td>{{ $appt->reason ?? 'General' }}</td>
                                                                    <td>
                                                                        @if($appt->token_number)
                                                                            <span class="badge bg-warning text-dark font-weight-bold px-2 py-1" style="font-size:13px;letter-spacing:0.5px;">
                                                                                <i class="fas fa-ticket-alt me-1"></i>{{ $appt->token_number }}
                                                                            </span>
                                                                        @else
                                                                            <span class="text-muted small">-</span>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if(($appt->type ?? 'offline') === 'online')
                                                                            <span class="badge" style="background:#f3e8ff;color:#7e22ce;font-weight:600;"><i class="fas fa-video me-1"></i> Online</span>
                                                                        @else
                                                                            <span class="badge" style="background:#e0f2fe;color:#0369a1;font-weight:600;"><i class="fas fa-hospital me-1"></i> Chamber</span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <strong>৳{{ number_format($appt->fee, 2) }}</strong>
                                                                        @if($appt->transaction_id)
                                                                            <span class="d-block small mt-1 text-primary fw-semibold" title="Transaction ID">
                                                                                <i class="fas fa-receipt me-1"></i>{{ $appt->transaction_id }}
                                                                            </span>
                                                                            @if($appt->sender_number)
                                                                                <span class="d-block text-muted" style="font-size: 11px;">
                                                                                    <i class="fas fa-mobile-alt me-1"></i>{{ $appt->sender_number }} ({{ strtoupper($appt->payment_method ?? 'bkash') }})
                                                                                </span>
                                                                            @endif
                                                                            @if(($appt->payment_status ?? 'pending') === 'paid' || $appt->status === 'confirmed')
                                                                                <span class="badge bg-success-light text-success" style="font-size: 10px;">Paid</span>
                                                                            @else
                                                                                <span class="badge bg-warning-light text-dark" style="font-size: 10px;">Verify TrxID</span>
                                                                            @endif
                                                                        @elseif(($appt->type ?? 'offline') === 'offline')
                                                                            <span class="d-block small text-muted mt-1" style="font-size: 11px;">Pay at Chamber</span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-end">
                                                                        <div class="table-action">
                                                                            @if(optional($appt->patient)->phone)
                                                                                <a href="tel:{{ $appt->patient->phone }}" class="btn btn-sm bg-success-light" title="Call Patient">
                                                                                    <i class="fas fa-phone-alt"></i> Call
                                                                                </a>
                                                                                @php
                                                                                    $cleanP = preg_replace('/[^0-9]/', '', $appt->patient->phone);
                                                                                    $waNum = str_starts_with($cleanP, '01') ? ('88' . $cleanP) : (str_starts_with($cleanP, '8801') ? $cleanP : ('880' . ltrim($cleanP, '0')));
                                                                                    $patientN = optional(optional($appt->patient)->user)->name ?? 'Patient';
                                                                                    $docN = Auth::user()->name ?? 'Doctor';
                                                                                    $tknTxt = $appt->token_number ? " (টোকেন: {$appt->token_number})" : "";
                                                                                    $dateTxt = \Carbon\Carbon::parse($appt->appointment_date)->format('d M Y');
                                                                                    $timeTxt = \Carbon\Carbon::parse($appt->appointment_time)->format('h:i A');
                                                                                    $waReminder = rawurlencode("আসসালামু আলাইকুম {$patientN}, Dr. {$docN} এর চেম্বার/ক্লিনিক থেকে জানাচ্ছি: আপনার অ্যাপয়েন্টমেন্ট {$dateTxt} তারিখে {$timeTxt} এ নির্ধারিত রয়েছে{$tknTxt}। সময়মতো উপস্থিত থাকার জন্য অনুরোধ করা হচ্ছে। ধন্যবাদ, abcsheba");
                                                                                @endphp
                                                                                <a href="https://api.whatsapp.com/send?phone={{ $waNum }}&text={{ $waReminder }}" target="_blank" class="btn btn-sm bg-success-light text-success" title="Send WhatsApp Reminder">
                                                                                    <i class="fab fa-whatsapp"></i> WhatsApp
                                                                                </a>
                                                                            @endif
                                                                            @if(optional(optional($appt->patient)->user)->id)
                                                                                <a href="{{ route('chat', ['user_id' => $appt->patient->user->id]) }}"
                                                                                    class="btn btn-sm bg-info-light">
                                                                                    <i class="far fa-comment-dots"></i> Chat
                                                                                </a>
                                                                            @endif
                                                                            @if(!empty($appt->reports) && is_array($appt->reports))
                                                                                <button type="button" class="btn btn-sm bg-warning-light text-dark fw-semibold" data-bs-toggle="modal" data-bs-target="#reports_modal_{{ $appt->id }}" title="View Uploaded Reports">
                                                                                    <i class="fas fa-file-medical text-primary me-1"></i> Reports ({{ count($appt->reports) }})
                                                                                </button>
                                                                            @endif
                                                                            <a href="javascript:void(0);"
                                                                                class="btn btn-sm bg-info-light">
                                                                                <i class="far fa-eye"></i> View
                                                                            </a>

                                                                            @if(in_array($appt->status, ['confirmed', 'completed']))
                                                                                <a href="{{ route('doctors.add.prescription', $appt->id) }}" class="btn btn-sm bg-primary-light">
                                                                                    <i class="fas fa-prescription"></i> Prescription
                                                                                </a>
                                                                            @endif

                                                                            @if($appt->type === 'online' && in_array($appt->status, ['pending', 'confirmed']))
                                                                                <a href="{{ route('appointment.consultation', $appt->id) }}" class="btn btn-sm bg-success-light" title="Join Video Consultation Room">
                                                                                    <i class="fas fa-video"></i> Video Call
                                                                                </a>
                                                                            @endif

                                                                            @if($appt->status == 'pending')
                                                                                <form action="{{ route('appointment.accept', $appt->id) }}" method="POST" class="d-inline">
                                                                                    @csrf
                                                                                    <button type="submit" class="btn btn-sm bg-success-light">
                                                                                        <i class="fas fa-check"></i> Accept
                                                                                    </button>
                                                                                </form>
                                                                                <form action="{{ route('appointment.cancel', $appt->id) }}" method="POST" class="d-inline">
                                                                                    @csrf
                                                                                    <button type="submit" class="btn btn-sm bg-danger-light">
                                                                                        <i class="fas fa-times"></i> Cancel
                                                                                    </button>
                                                                                </form>
                                                                            @elseif($appt->status == 'confirmed')
                                                                                <form action="{{ route('appointment.complete', $appt->id) }}" method="POST" class="d-inline">
                                                                                    @csrf
                                                                                    <button type="submit" class="btn btn-sm bg-info-light">
                                                                                        <i class="fas fa-check-double"></i> Complete
                                                                                    </button>
                                                                                </form>
                                                                                <form action="{{ route('appointment.cancel', $appt->id) }}" method="POST" class="d-inline">
                                                                                    @csrf
                                                                                    <button type="submit" class="btn btn-sm bg-danger-light">
                                                                                        <i class="fas fa-times"></i> Cancel
                                                                                    </button>
                                                                                </form>
                                                                            @else
                                                                                <span class="badge bg-{{ $appt->status == 'completed' ? 'info' : 'danger' }}">{{ ucfirst($appt->status) }}</span>
                                                                            @endif
                                                                        </div>
                                                                    </td>
                                                                </tr>

                                                                @if(!empty($appt->reports) && is_array($appt->reports))
                                                                    <!-- Reports Modal -->
                                                                    <div class="modal fade" id="reports_modal_{{ $appt->id }}" tabindex="-1" aria-hidden="true">
                                                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h5 class="modal-title">
                                                                                        <i class="fas fa-file-medical text-primary me-2"></i> রোগীর প্রেসক্রিপশন ও রিপোর্ট সমূহ
                                                                                    </h5>
                                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                                </div>
                                                                                <div class="modal-body text-start">
                                                                                    <div class="alert alert-info py-2 px-3 small mb-3">
                                                                                        <strong>রোগীর নাম:</strong> {{ optional(optional($appt->patient)->user)->name ?? 'Patient' }} |
                                                                                        <strong>তারিখ:</strong> {{ \Carbon\Carbon::parse($appt->appointment_date)->format('d M Y') }}
                                                                                        @if($appt->token_number) | <strong>টোকেন:</strong> {{ $appt->token_number }} @endif
                                                                                    </div>
                                                                                    <div class="row g-3">
                                                                                        @foreach($appt->reports as $report)
                                                                                            <div class="col-md-6">
                                                                                                <div class="card h-100 border p-3 text-center shadow-sm">
                                                                                                    @if(($report['type'] ?? '') === 'image')
                                                                                                        <a href="{{ asset($report['path']) }}" target="_blank" class="d-block mb-2">
                                                                                                            <img src="{{ asset($report['path']) }}" alt="{{ $report['name'] }}" class="img-fluid rounded border" style="max-height: 180px; object-fit: contain;">
                                                                                                        </a>
                                                                                                        <span class="small fw-semibold text-dark text-truncate d-block">{{ $report['name'] }}</span>
                                                                                                        <div class="mt-2 d-flex gap-1 justify-content-center">
                                                                                                            <a href="{{ asset($report['path']) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill">
                                                                                                                <i class="fas fa-expand me-1"></i> বড় করে দেখুন
                                                                                                            </a>
                                                                                                            <a href="{{ asset($report['path']) }}" download class="btn btn-sm btn-outline-secondary rounded-pill">
                                                                                                                <i class="fas fa-download"></i>
                                                                                                            </a>
                                                                                                        </div>
                                                                                                    @else
                                                                                                        <div class="py-4">
                                                                                                            <i class="fas fa-file-pdf text-danger" style="font-size: 48px;"></i>
                                                                                                            <h6 class="mt-2 mb-1 text-dark fw-semibold text-truncate">{{ $report['name'] }}</h6>
                                                                                                            <small class="text-muted d-block mb-3">{{ $report['size'] ?? '' }}</small>
                                                                                                            <a href="{{ asset($report['path']) }}" target="_blank" class="btn btn-sm btn-danger rounded-pill px-3">
                                                                                                                <i class="fas fa-external-link-alt me-1"></i> PDF ফাইল ওপেন করুন
                                                                                                            </a>
                                                                                                        </div>
                                                                                                    @endif
                                                                                                </div>
                                                                                            </div>
                                                                                        @endforeach
                                                                                    </div>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">বন্ধ করুন</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @empty
                                                                <tr>
                                                                    <td colspan="7" class="text-center">No upcoming appointments found.</td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /Upcoming Appointment Tab -->

                                    <!-- Today Appointment Tab -->
                                    <div class="tab-pane" id="today-appointments">

                                        <!-- Live Chamber Queue Management Card -->
                                        <div class="card border-0 shadow-sm mb-3" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border-radius: 16px; color: #fff;">
                                            <div class="card-body p-4">
                                                <div class="row align-items-center g-3">
                                                    <!-- Currently Serving -->
                                                    <div class="col-md-5 border-end border-secondary border-opacity-50">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 me-2 rounded-pill" style="font-size: 11px;">
                                                                <span class="spinner-grow spinner-grow-sm me-1" style="width: 8px; height: 8px;" role="status"></span> CHAMBER QUEUE
                                                            </span>
                                                            <span class="text-white-50 small">Currently Serving (Chamber)</span>
                                                        </div>
                                                        @if($currentlyServing)
                                                            <div class="d-flex align-items-center">
                                                                <div class="badge bg-warning text-dark fs-5 fw-bold px-3 py-2 me-3 rounded-3 shadow-sm">
                                                                    {{ $currentlyServing->token_number ?? 'TKN-01' }}
                                                                </div>
                                                                <div>
                                                                    <h6 class="mb-0 fw-bold text-white">{{ optional(optional($currentlyServing->patient)->user)->name ?? 'Patient' }}</h6>
                                                                    <small class="text-white-50"><i class="far fa-clock me-1"></i> {{ \Carbon\Carbon::parse($currentlyServing->appointment_time)->format('h:i A') }}</small>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <div class="text-white-50 small py-1">
                                                                <i class="fas fa-stethoscope me-1 text-info"></i> চেম্বার প্রস্তুত। রোগী ডাকতে <strong>"Call Next"</strong> চাপুন।
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <!-- Next In Line -->
                                                    <div class="col-md-4">
                                                        <div class="text-white-50 small mb-1">
                                                            <i class="fas fa-user-clock text-warning me-1"></i> Next Chamber Patient (পরবর্তী রোগী)
                                                        </div>
                                                        @if($nextInLine)
                                                            <div class="d-flex align-items-center">
                                                                <span class="badge bg-secondary text-white fw-bold px-2 py-1 me-2">
                                                                    {{ $nextInLine->token_number ?? 'TKN-XX' }}
                                                                </span>
                                                                <span class="text-white fw-semibold text-truncate" style="max-width: 170px;">
                                                                    {{ optional(optional($nextInLine->patient)->user)->name ?? 'Patient' }}
                                                                </span>
                                                            </div>
                                                        @else
                                                            <span class="badge bg-dark text-muted">আর কোনো অপেক্ষমাণ রোগী নেই</span>
                                                        @endif
                                                    </div>

                                                    <!-- Call Next Button -->
                                                    <div class="col-md-3 text-md-end">
                                                        <form action="{{ route('appointment.call_next') }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="type" value="offline">
                                                            <button type="submit" class="btn btn-success btn-lg w-100 rounded-pill shadow fw-bold px-3 py-2" style="font-size: 15px;">
                                                                <i class="fas fa-bullhorn me-1"></i> Call Chamber ⏭️
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /Live Chamber Queue Management Card -->

                                        <!-- Live Online Telemedicine Queue Management Card -->
                                        <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%); border-radius: 16px; color: #fff;">
                                            <div class="card-body p-4">
                                                <div class="row align-items-center g-3">
                                                    <!-- Currently Serving Online -->
                                                    <div class="col-md-5 border-end border-secondary border-opacity-50">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 me-2 rounded-pill" style="font-size: 11px;">
                                                                <span class="spinner-grow spinner-grow-sm me-1" style="width: 8px; height: 8px;" role="status"></span> ONLINE QUEUE
                                                            </span>
                                                            <span class="text-white-50 small">Active Telemedicine Call</span>
                                                        </div>
                                                        @if($onlineCurrentlyServing)
                                                            <div class="d-flex align-items-center justify-content-between">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="badge bg-info text-dark fs-5 fw-bold px-3 py-2 me-3 rounded-3 shadow-sm">
                                                                        {{ $onlineCurrentlyServing->token_number ?? 'ONL-01' }}
                                                                    </div>
                                                                    <div>
                                                                        <h6 class="mb-0 fw-bold text-white">{{ optional(optional($onlineCurrentlyServing->patient)->user)->name ?? 'Patient' }}</h6>
                                                                        <small class="text-white-50"><i class="far fa-clock me-1"></i> {{ \Carbon\Carbon::parse($onlineCurrentlyServing->appointment_time)->format('h:i A') }}</small>
                                                                    </div>
                                                                </div>
                                                                <a href="{{ route('appointment.consultation', $onlineCurrentlyServing->id) }}" class="btn btn-sm btn-success rounded-pill px-3 py-1 shadow-sm ms-2">
                                                                    <i class="fas fa-video me-1"></i> Open Room
                                                                </a>
                                                            </div>
                                                        @else
                                                            <div class="text-white-50 small py-1">
                                                                <i class="fas fa-video me-1 text-info"></i> কোনো অনলাইন রোগী সক্রিয় নেই। কল করতে <strong>"Call Next Online"</strong> চাপুন।
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <!-- Next Online In Line -->
                                                    <div class="col-md-4">
                                                        <div class="text-white-50 small mb-1">
                                                            <i class="fas fa-user-clock text-warning me-1"></i> Next Online (পরবর্তী অনলাইন রোগী)
                                                        </div>
                                                        @if($onlineNextInLine)
                                                            <div class="d-flex align-items-center">
                                                                <span class="badge bg-primary text-white fw-bold px-2 py-1 me-2">
                                                                    {{ $onlineNextInLine->token_number ?? 'ONL-XX' }}
                                                                </span>
                                                                <span class="text-white fw-semibold text-truncate" style="max-width: 170px;">
                                                                    {{ optional(optional($onlineNextInLine->patient)->user)->name ?? 'Patient' }}
                                                                </span>
                                                            </div>
                                                        @else
                                                            <span class="badge bg-dark text-muted">কোনো অনলাইন অপেক্ষমাণ রোগী নেই</span>
                                                        @endif
                                                    </div>

                                                    <!-- Call Next Online Button -->
                                                    <div class="col-md-3 text-md-end">
                                                        <form action="{{ route('appointment.call_next') }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="type" value="online">
                                                            <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill shadow fw-bold px-3 py-2" style="font-size: 14px;">
                                                                <i class="fas fa-video me-1"></i> Call Next Online 📞
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /Live Online Telemedicine Queue Management Card -->

                                        <div class="card card-table mb-0">
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-hover table-center mb-0">
                                                        <thead>
                                                            <tr>
                                                                <th>Patient Name</th>
                                                                <th>Appt Date</th>
                                                                <th>Purpose</th>
                                                                <th>Token / Serial</th>
                                                                <th>Type</th>
                                                                <th class="text-center">Paid Amount</th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse($todayAppointments as $appt)
                                                                <tr @if($currentlyServing && $currentlyServing->id === $appt->id) class="table-success" style="background:#f0fdf4 !important;" @endif>
                                                                    <td>
                                                                        <h2 class="table-avatar">
                                                                            <a href="{{ route('patient.profile', $appt->patient->id ?? '') }}"
                                                                                class="avatar avatar-sm me-2"><img
                                                                                    class="avatar-img rounded-circle"
                                                                                    src="{{ optional($appt->patient)->profile_image ? asset($appt->patient->profile_image) : asset('assets/img/patients/patient.jpg') }}"
                                                                                    alt="Patient Image"></a>
                                                                            <a href="{{ route('patient.profile', $appt->patient->id ?? '') }}">
                                                                                {{ optional(optional($appt->patient)->user)->name ?? 'Unknown' }} 
                                                                                <span>#PT{{ sprintf('%04d', $appt->patient_id) }}</span>
                                                                                @if(optional($appt->patient)->phone)
                                                                                    <span class="d-block text-muted" style="font-size:12px;font-weight:normal;"><i class="fas fa-phone-alt me-1 text-success"></i>{{ $appt->patient->phone }}</span>
                                                                                @endif
                                                                            </a>
                                                                        </h2>
                                                                    </td>
                                                                    <td>{{ \Carbon\Carbon::parse($appt->appointment_date)->format('d M Y') }} <span class="d-block text-info font-weight-600">{{ \Carbon\Carbon::parse($appt->appointment_time)->format('h:i A') }}</span></td>
                                                                    <td>{{ $appt->reason ?? 'General' }}</td>
                                                                    <td>
                                                                        @if($appt->token_number)
                                                                            <span class="badge bg-warning text-dark font-weight-bold px-2 py-1" style="font-size:13px;letter-spacing:0.5px;">
                                                                                <i class="fas fa-ticket-alt me-1"></i>{{ $appt->token_number }}
                                                                            </span>
                                                                        @else
                                                                            <span class="text-muted small">-</span>
                                                                        @endif
                                                                        @if($currentlyServing && $currentlyServing->id === $appt->id)
                                                                            <span class="badge bg-success text-white px-2 py-1 mt-1 d-block" style="font-size:11px;">
                                                                                <i class="fas fa-door-open me-1"></i> In Chamber
                                                                            </span>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if(($appt->type ?? 'offline') === 'online')
                                                                            <span class="badge" style="background:#f3e8ff;color:#7e22ce;font-weight:600;"><i class="fas fa-video me-1"></i> Online</span>
                                                                        @else
                                                                            <span class="badge" style="background:#e0f2fe;color:#0369a1;font-weight:600;"><i class="fas fa-hospital me-1"></i> Chamber</span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <strong>৳{{ number_format($appt->fee, 2) }}</strong>
                                                                        @if($appt->transaction_id)
                                                                            <span class="d-block small mt-1 text-primary fw-semibold" title="Transaction ID">
                                                                                <i class="fas fa-receipt me-1"></i>{{ $appt->transaction_id }}
                                                                            </span>
                                                                            @if($appt->sender_number)
                                                                                <span class="d-block text-muted" style="font-size: 11px;">
                                                                                    <i class="fas fa-mobile-alt me-1"></i>{{ $appt->sender_number }} ({{ strtoupper($appt->payment_method ?? 'bkash') }})
                                                                                </span>
                                                                            @endif
                                                                            @if(($appt->payment_status ?? 'pending') === 'paid' || $appt->status === 'confirmed')
                                                                                <span class="badge bg-success-light text-success" style="font-size: 10px;">Paid</span>
                                                                            @else
                                                                                <span class="badge bg-warning-light text-dark" style="font-size: 10px;">Verify TrxID</span>
                                                                            @endif
                                                                        @elseif(($appt->type ?? 'offline') === 'offline')
                                                                            <span class="d-block small text-muted mt-1" style="font-size: 11px;">Pay at Chamber</span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-end">
                                                                        <div class="table-action">
                                                                            @if(optional($appt->patient)->phone)
                                                                                <a href="tel:{{ $appt->patient->phone }}" class="btn btn-sm bg-success-light" title="Call Patient">
                                                                                    <i class="fas fa-phone-alt"></i> Call
                                                                                </a>
                                                                                @php
                                                                                    $cleanP = preg_replace('/[^0-9]/', '', $appt->patient->phone);
                                                                                    $waNum = str_starts_with($cleanP, '01') ? ('88' . $cleanP) : (str_starts_with($cleanP, '8801') ? $cleanP : ('880' . ltrim($cleanP, '0')));
                                                                                    $patientN = optional(optional($appt->patient)->user)->name ?? 'Patient';
                                                                                    $docN = Auth::user()->name ?? 'Doctor';
                                                                                    $tknTxt = $appt->token_number ? " (টোকেন: {$appt->token_number})" : "";
                                                                                    $timeTxt = \Carbon\Carbon::parse($appt->appointment_time)->format('h:i A');
                                                                                    $waReminder = rawurlencode("আসসালামু আলাইকুম {$patientN}, Dr. {$docN} এর চেম্বার/ক্লিনিক থেকে জানাচ্ছি: আপনার অ্যাপয়েন্টমেন্ট আজ {$timeTxt} এ নির্ধারিত রয়েছে{$tknTxt}। সময়মতো উপস্থিত থাকার জন্য অনুরোধ করা হচ্ছে। ধন্যবাদ, abcsheba");
                                                                                @endphp
                                                                                <a href="https://api.whatsapp.com/send?phone={{ $waNum }}&text={{ $waReminder }}" target="_blank" class="btn btn-sm bg-success-light text-success" title="Send WhatsApp Reminder">
                                                                                    <i class="fab fa-whatsapp"></i> WhatsApp
                                                                                </a>
                                                                            @endif
                                                                            @if(optional(optional($appt->patient)->user)->id)
                                                                                <a href="{{ route('chat', ['user_id' => $appt->patient->user->id]) }}"
                                                                                    class="btn btn-sm bg-info-light">
                                                                                    <i class="far fa-comment-dots"></i> Chat
                                                                                </a>
                                                                            @endif
                                                                            @if(!empty($appt->reports) && is_array($appt->reports))
                                                                                <button type="button" class="btn btn-sm bg-warning-light text-dark fw-semibold" data-bs-toggle="modal" data-bs-target="#reports_modal_today_{{ $appt->id }}" title="View Uploaded Reports">
                                                                                    <i class="fas fa-file-medical text-primary me-1"></i> Reports ({{ count($appt->reports) }})
                                                                                </button>
                                                                            @endif

                                                                            @if($appt->status !== 'completed' && $appt->status !== 'cancelled' && (!$currentlyServing || $currentlyServing->id !== $appt->id))
                                                                                <form action="{{ route('appointment.call_specific', $appt->id) }}" method="POST" class="d-inline">
                                                                                    @csrf
                                                                                    <button type="submit" class="btn btn-sm bg-primary-light" title="চেম্বারে ডাকুন (Call to Chamber)">
                                                                                        <i class="fas fa-bullhorn"></i> Call In
                                                                                    </button>
                                                                                </form>
                                                                            @endif

                                                                            <a href="javascript:void(0);"
                                                                                class="btn btn-sm bg-info-light">
                                                                                <i class="far fa-eye"></i> View
                                                                            </a>

                                                                            @if(in_array($appt->status, ['confirmed', 'completed']))
                                                                                <a href="{{ route('doctors.add.prescription', $appt->id) }}" class="btn btn-sm bg-primary-light">
                                                                                    <i class="fas fa-prescription"></i> Prescription
                                                                                </a>
                                                                            @endif

                                                                            @if($appt->type === 'online' && in_array($appt->status, ['pending', 'confirmed']))
                                                                                <form action="{{ route('appointment.call_specific', $appt->id) }}" method="POST" class="d-inline">
                                                                                    @csrf
                                                                                    <button type="submit" class="btn btn-sm bg-success-light" title="রোগীকে কল করে ভিডিও রুমে প্রবেশ করুন">
                                                                                        <i class="fas fa-video me-1"></i> Call & Video
                                                                                    </button>
                                                                                </form>
                                                                            @endif

                                                                            @if($appt->status == 'pending')
                                                                                <form action="{{ route('appointment.accept', $appt->id) }}" method="POST" class="d-inline">
                                                                                    @csrf
                                                                                    <button type="submit" class="btn btn-sm bg-success-light">
                                                                                        <i class="fas fa-check"></i> Accept
                                                                                    </button>
                                                                                </form>
                                                                                <form action="{{ route('appointment.cancel', $appt->id) }}" method="POST" class="d-inline">
                                                                                    @csrf
                                                                                    <button type="submit" class="btn btn-sm bg-danger-light">
                                                                                        <i class="fas fa-times"></i> Cancel
                                                                                    </button>
                                                                                </form>
                                                                            @elseif($appt->status == 'confirmed')
                                                                                <form action="{{ route('appointment.complete', $appt->id) }}" method="POST" class="d-inline">
                                                                                    @csrf
                                                                                    <button type="submit" class="btn btn-sm bg-info-light">
                                                                                        <i class="fas fa-check-double"></i> Complete
                                                                                    </button>
                                                                                </form>
                                                                                <form action="{{ route('appointment.cancel', $appt->id) }}" method="POST" class="d-inline">
                                                                                    @csrf
                                                                                    <button type="submit" class="btn btn-sm bg-danger-light">
                                                                                        <i class="fas fa-times"></i> Cancel
                                                                                    </button>
                                                                                </form>
                                                                            @else
                                                                                <span class="badge bg-{{ $appt->status == 'completed' ? 'info' : 'danger' }}">{{ ucfirst($appt->status) }}</span>
                                                                            @endif
                                                                        </div>
                                                                    </td>
                                                                </tr>

                                                                @if(!empty($appt->reports) && is_array($appt->reports))
                                                                    <!-- Reports Modal (Today) -->
                                                                    <div class="modal fade" id="reports_modal_today_{{ $appt->id }}" tabindex="-1" aria-hidden="true">
                                                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h5 class="modal-title">
                                                                                        <i class="fas fa-file-medical text-primary me-2"></i> রোগীর প্রেসক্রিপশন ও রিপোর্ট সমূহ
                                                                                    </h5>
                                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                                </div>
                                                                                <div class="modal-body text-start">
                                                                                    <div class="alert alert-info py-2 px-3 small mb-3">
                                                                                        <strong>রোগীর নাম:</strong> {{ optional(optional($appt->patient)->user)->name ?? 'Patient' }} |
                                                                                        <strong>তারিখ:</strong> {{ \Carbon\Carbon::parse($appt->appointment_date)->format('d M Y') }}
                                                                                        @if($appt->token_number) | <strong>টোকেন:</strong> {{ $appt->token_number }} @endif
                                                                                    </div>
                                                                                    <div class="row g-3">
                                                                                        @foreach($appt->reports as $report)
                                                                                            <div class="col-md-6">
                                                                                                <div class="card h-100 border p-3 text-center shadow-sm">
                                                                                                    @if(($report['type'] ?? '') === 'image')
                                                                                                        <a href="{{ asset($report['path']) }}" target="_blank" class="d-block mb-2">
                                                                                                            <img src="{{ asset($report['path']) }}" alt="{{ $report['name'] }}" class="img-fluid rounded border" style="max-height: 180px; object-fit: contain;">
                                                                                                        </a>
                                                                                                        <span class="small fw-semibold text-dark text-truncate d-block">{{ $report['name'] }}</span>
                                                                                                        <div class="mt-2 d-flex gap-1 justify-content-center">
                                                                                                            <a href="{{ asset($report['path']) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill">
                                                                                                                <i class="fas fa-expand me-1"></i> বড় করে দেখুন
                                                                                                            </a>
                                                                                                            <a href="{{ asset($report['path']) }}" download class="btn btn-sm btn-outline-secondary rounded-pill">
                                                                                                                <i class="fas fa-download"></i>
                                                                                                            </a>
                                                                                                        </div>
                                                                                                    @else
                                                                                                        <div class="py-4">
                                                                                                            <i class="fas fa-file-pdf text-danger" style="font-size: 48px;"></i>
                                                                                                            <h6 class="mt-2 mb-1 text-dark fw-semibold text-truncate">{{ $report['name'] }}</h6>
                                                                                                            <small class="text-muted d-block mb-3">{{ $report['size'] ?? '' }}</small>
                                                                                                            <a href="{{ asset($report['path']) }}" target="_blank" class="btn btn-sm btn-danger rounded-pill px-3">
                                                                                                                <i class="fas fa-external-link-alt me-1"></i> PDF ফাইল ওপেন করুন
                                                                                                            </a>
                                                                                                        </div>
                                                                                                    @endif
                                                                                                </div>
                                                                                            </div>
                                                                                        @endforeach
                                                                                    </div>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">বন্ধ করুন</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @empty
                                                                <tr>
                                                                    <td colspan="7" class="text-center">No appointments for today.</td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /Today Appointment Tab -->

                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>
    <!-- /Page Content -->
@endsection

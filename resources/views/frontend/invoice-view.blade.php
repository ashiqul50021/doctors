@extends('layouts.app')

@section('title', 'Invoice View - ' . ($siteSettings['site_name'] ?? 'abcsheba'))

@section('content')

@push('styles')
<style>
    /* Premium Invoice Styles */
    .invoice-card {
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
        background: #fff;
        position: relative;
    }
    
    .invoice-card-top-bar {
        background: linear-gradient(90deg, #0e82fd 0%, #09e5ab 100%);
        height: 8px;
        width: 100%;
    }
    
    .invoice-info-box {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 20px;
        height: 100%;
        transition: all 0.2s ease-in-out;
    }
    
    .invoice-info-box:hover {
        border-color: #cbd5e1;
        background-color: #f1f5f9;
    }
    
    .invoice-badge-token {
        background: linear-gradient(135deg, #0e82fd 0%, #09e5ab 100%);
        color: white !important;
        font-weight: 700;
        letter-spacing: 0.5px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(14, 130, 253, 0.2);
    }
    
    .invoice-table-header {
        background-color: #f8fafc !important;
        border-bottom: 2px solid #e2e8f0 !important;
        color: #1e293b !important;
        font-weight: 600;
    }
    
    .invoice-highlight-box {
        background: linear-gradient(135deg, #f0fdf4 0%, #ecfeff 100%);
        border: 1px solid #ccfbf1;
        border-radius: 12px;
        padding: 20px;
    }
    
    .info-label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 4px;
        display: block;
    }
    
    .info-value {
        font-size: 14px;
        color: #334155;
        line-height: 1.5;
    }
    
    .info-title {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 12px;
        border-bottom: 2px solid #e2e8f0;
        padding-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .other-info-box {
        background-color: #fefbeb;
        border-left: 4px solid #eab308;
        border-radius: 8px;
        padding: 15px;
    }
    
    .btn-primary-gradient {
        background: linear-gradient(90deg, #0e82fd 0%, #09e5ab 100%);
        border: none;
        color: white;
        font-weight: 500;
        padding: 8px 16px;
        border-radius: 6px;
        box-shadow: 0 4px 6px -1px rgba(14, 130, 253, 0.2);
        transition: all 0.2s ease-in-out;
    }
    
    .btn-primary-gradient:hover {
        opacity: 0.95;
        color: white;
        box-shadow: 0 6px 12px -1px rgba(14, 130, 253, 0.3);
        transform: translateY(-1px);
    }
    
    .btn-primary-gradient:active {
        transform: translateY(0);
    }
    
    /* Print Layout Configuration */
    @media print {
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .no-print {
            display: none !important;
        }
        .header, .footer, .breadcrumb-bar, .main-wrapper > .breadcrumb, .bottom-nav-container {
            display: none !important;
        }
        .content {
            padding: 0 !important;
            margin: 0 !important;
        }
        body {
            background: #fff !important;
            margin: 0 !important;
            padding: 0 !important;
            font-size: 11px; /* Slightly smaller base font for printing */
        }
        .col-lg-8 {
            width: 100% !important;
            max-width: 100% !important;
            flex: 0 0 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .offset-lg-2 {
            margin-left: 0 !important;
        }
        /* Force grid columns to stay side-by-side in print */
        .row {
            display: flex !important;
            flex-flow: row wrap !important;
            margin-right: -6px !important;
            margin-left: -6px !important;
        }
        .col-6 {
            flex: 0 0 50% !important;
            max-width: 50% !important;
            width: 50% !important;
            padding-right: 6px !important;
            padding-left: 6px !important;
        }
        .invoice-card {
            border: none !important;
            box-shadow: none !important;
            border-radius: 0 !important;
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
        }
        .invoice-card > div {
            padding: 0 !important;
        }
        .invoice-card-top-bar {
            display: block !important;
            height: 4px !important;
        }
        .invoice-info-box {
            border: 1px solid #cbd5e1 !important;
            background-color: #f8fafc !important;
            padding: 12px !important;
            margin-bottom: 0 !important;
            height: 100% !important;
        }
        .invoice-highlight-box {
            border: 1px solid #cbd5e1 !important;
            background: linear-gradient(135deg, #f0fdf4 0%, #ecfeff 100%) !important;
            padding: 12px !important;
            margin-bottom: 12px !important;
        }
        .invoice-badge-token {
            background: linear-gradient(135deg, #0e82fd 0%, #09e5ab 100%) !important;
            color: white !important;
            border: none !important;
            box-shadow: none !important;
        }
        .my-4 {
            margin-top: 10px !important;
            margin-bottom: 10px !important;
        }
        .mb-4 {
            margin-bottom: 10px !important;
        }
        /* Enforce table borders in print view */
        .table {
            border-collapse: collapse !important;
            width: 100% !important;
        }
        .table th, .table td {
            border: 1px solid #cbd5e1 !important;
            padding: 6px !important;
        }
        .invoice-table-header {
            background-color: #f8fafc !important;
        }
    }
</style>
@endpush

<!-- Page Content -->
<div class="content">
    <div class="container">

        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                
                <!-- Print & Navigation Action Buttons -->
                <div class="d-flex justify-content-between align-items-center mb-4 no-print">
                    <a href="{{ Auth::user()->role === 'doctor' ? route('doctors.dashboard') : route('patient.dashboard') }}" class="btn btn-secondary shadow-sm">
                        <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                    </a>
                    <button onclick="window.print();" class="btn btn-primary-gradient shadow-sm">
                        <i class="fas fa-print me-2"></i> Print Invoice
                    </button>
                </div>

                <div class="invoice-card">
                    <div class="invoice-card-top-bar"></div>
                    <div class="p-4 p-md-5">
                        
                        <!-- Header / Logo & Invoice Info -->
                        <div class="row align-items-center mb-4">
                            <div class="col-6">
                                <div class="invoice-logo">
                                    <img src="{{ !empty($siteSettings['logo']) ? asset($siteSettings['logo']) : asset('assets/img/logo.png') }}" alt="logo" style="max-height: 50px;">
                                </div>
                            </div>
                            <div class="col-6 text-end">
                                <span class="info-label">Invoice Reference</span>
                                <h3 class="text-primary fw-bold mb-1 fs-5">#INV-{{ sprintf('%04d', $appointment->id) }}</h3>
                                <p class="text-muted small mb-0">
                                    <strong>Issued Date:</strong> {{ $appointment->created_at->format('d M Y') }}
                                </p>
                            </div>
                        </div>

                        <hr class="my-4" style="border-top: 1px solid #e2e8f0;">

                        <!-- Address Info Blocks -->
                        <div class="row g-4 mb-4">
                            <div class="col-6">
                                <div class="invoice-info-box">
                                    <div class="info-title fs-6">
                                        <i class="fas fa-user-md text-primary"></i> Invoice From
                                    </div>
                                    <div class="info-value">
                                        <strong class="text-dark fs-6">{{ str_starts_with(strtolower($appointment->doctor->user->name), 'dr.') ? $appointment->doctor->user->name : 'Dr. ' . $appointment->doctor->user->name }}</strong>
                                        @if($appointment->doctor->qualification)
                                            <div class="text-secondary small fw-medium mt-1">{{ $appointment->doctor->qualification }}</div>
                                        @endif
                                        <div class="text-primary small fw-semibold mt-0.5">{{ $appointment->doctor->speciality->name ?? 'General Practitioner' }}</div>
                                        
                                        @if($appointment->doctor->registration_number)
                                            <div class="text-muted small mt-1">BMDC Reg No: <strong>{{ $appointment->doctor->registration_number }}</strong></div>
                                        @endif
                                        
                                        <div class="mt-3 pt-2 border-top border-light">
                                            @if($appointment->doctor->primary_clinic_name)
                                                <strong class="text-dark small d-block mb-1">{{ $appointment->doctor->primary_clinic_name }}</strong>
                                            @endif
                                            @if($appointment->doctor->primary_clinic_address)
                                                <span class="small d-block text-muted">{{ $appointment->doctor->primary_clinic_address }}</span>
                                            @endif
                                            @if($appointment->doctor->area || $appointment->doctor->district)
                                                <span class="small d-block text-muted">{{ $appointment->doctor->area->name ?? '' }}{{ $appointment->doctor->area && $appointment->doctor->district ? ', ' : '' }}{{ $appointment->doctor->district->name ?? '' }}</span>
                                            @endif
                                            @if($appointment->doctor->phone)
                                                <span class="small d-block text-muted mt-1"><i class="fas fa-phone-alt me-1 text-muted"></i> {{ $appointment->doctor->phone }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="invoice-info-box">
                                    <div class="info-title fs-6">
                                        <i class="fas fa-user text-info"></i> Invoice To
                                    </div>
                                    <div class="info-value">
                                        <strong class="text-dark fs-6">{{ $appointment->patient->user->name }}</strong>
                                        
                                        <div class="mt-3 pt-2 border-top border-light">
                                            @if($appointment->patient->phone)
                                                <span class="small d-block text-muted mb-1"><strong>Phone:</strong> {{ $appointment->patient->phone }}</span>
                                            @endif
                                            @if($appointment->patient->address)
                                                <span class="small d-block text-muted mb-1"><strong>Address:</strong> {{ $appointment->patient->address }}</span>
                                            @endif
                                            @if($appointment->patient->city)
                                                <span class="small d-block text-muted mb-1"><strong>City:</strong> {{ $appointment->patient->city }}</span>
                                            @endif
                                            
                                            @if($appointment->patient->gender || $appointment->patient->blood_group)
                                                <div class="mt-2 pt-2 border-top border-light small text-secondary">
                                                    @if($appointment->patient->gender)
                                                        <span><strong>Gender:</strong> {{ ucfirst($appointment->patient->gender) }}</span>
                                                    @endif
                                                    @if($appointment->patient->gender && $appointment->patient->blood_group) <span class="mx-2">|</span> @endif
                                                    @if($appointment->patient->blood_group)
                                                        <span><strong>Blood Group:</strong> {{ $appointment->patient->blood_group }}</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Highlight Panel: Queue & Payment Details -->
                        <div class="invoice-highlight-box mb-4">
                            <div class="row g-3">
                                <div class="col-6">
                                    <span class="info-label">Payment Information</span>
                                    <div class="mt-2">
                                        <span class="text-muted small d-block">Method:</span>
                                        <strong class="text-dark">{{ $appointment->type === 'online' ? 'Online Payment (SSLCommerz)' : 'Pay at Clinic (Cash)' }}</strong>
                                    </div>
                                    <div class="mt-2">
                                        <span class="text-muted small d-block">Status:</span>
                                        <span class="badge bg-{{ $appointment->status === 'confirmed' || $appointment->status === 'completed' ? 'success' : ($appointment->status === 'pending' ? 'warning' : 'danger') }} px-2.5 py-1.5 mt-1">{{ ucfirst($appointment->status) }}</span>
                                    </div>
                                </div>
                                <div class="col-6 text-end border-start border-light ps-4">
                                    <span class="info-label text-end">Appointment Queue</span>
                                    <div class="mt-2">
                                        <span class="text-muted small d-block">Consultation Type:</span>
                                        <strong class="text-dark">{{ $appointment->type === 'online' ? 'Online Video Call' : 'In-Clinic (Offline)' }}</strong>
                                    </div>
                                    @if($appointment->token_number)
                                    <div class="mt-2">
                                        <span class="text-muted small d-block">Your Serial/Token:</span>
                                        <span class="badge invoice-badge-token fs-6 px-3 py-1.5 mt-1">{{ $appointment->token_number }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered align-middle mb-0">
                                <thead>
                                    <tr class="invoice-table-header">
                                        <th style="width: 60%;">Consultation Details</th>
                                        <th class="text-center" style="width: 15%;">Quantity</th>
                                        <th class="text-end" style="width: 25%;">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ ucfirst($appointment->type) }} Consultation</div>
                                            <div class="text-muted small mt-1">
                                                <i class="fas fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d M Y') }}
                                                <span class="mx-2">|</span>
                                                <i class="far fa-clock me-1"></i> {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
                                            </div>
                                            @if($appointment->token_number)
                                                <div class="text-primary small mt-1 fw-medium">
                                                    <i class="fas fa-ticket-alt me-1"></i> Serial/Token Number: {{ $appointment->token_number }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="text-center text-dark">1</td>
                                        <td class="text-end text-dark fw-medium">৳{{ number_format($appointment->fee, 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pricing Breakdown -->
                        <div class="row justify-content-end mb-4">
                            <div class="col-6">
                                <table class="table table-borderless align-middle mb-0">
                                    <tbody>
                                        <tr>
                                            <td class="text-muted text-end py-1">Subtotal:</td>
                                            <td class="text-dark text-end fw-medium py-1" style="width: 40%;">৳{{ number_format($appointment->fee, 2) }}</td>
                                        </tr>
                                        <tr class="border-top">
                                            <td class="text-dark text-end fw-bold py-2">Total Paid:</td>
                                            <td class="text-primary text-end fw-bold h5 mb-0 py-2">৳{{ number_format($appointment->fee, 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Footer notes -->
                        <div class="other-info-box">
                            <h6 class="text-dark fw-bold mb-1"><i class="fas fa-info-circle me-1 text-warning"></i> Note to Patient:</h6>
                            <p class="text-muted mb-0 small" style="line-height: 1.6;">
                                Please show this slip or serial number at the clinic counter upon arrival. This is a system-generated booking receipt and does not require any physical signature. For support or queries, please contact our support center.
                            </p>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
<!-- /Page Content -->

@push('scripts')
@if(request()->has('print'))
<script>
    window.addEventListener('DOMContentLoaded', (event) => {
        // Delay slightly to ensure fonts/layout are fully rendered
        setTimeout(() => {
            window.print();
        }, 500);
    });
</script>
@endif
@endpush

@endsection

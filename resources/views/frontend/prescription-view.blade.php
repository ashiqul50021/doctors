@extends('layouts.app')

@section('title', 'Prescription View - abcsheba')

@push('styles')
<style>
    /* Print Layout Configuration */
    @media print {
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        /* Hide navbar, header, footer, bottom navigation, and download/print buttons */
        .header, 
        .footer, 
        .breadcrumb-bar, 
        .main-wrapper > .breadcrumb, 
        .bottom-nav-container,
        [data-html2canvas-ignore="true"] {
            display: none !important;
        }
        
        .content {
            padding: 0 !important;
            margin: 0 !important;
        }
        
        .invoice-content {
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
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
        
        /* Clean up table formatting for print */
        .table {
            width: 100% !important;
            border-collapse: collapse !important;
        }
        
        .table th, .table td {
            background-color: transparent !important;
            color: #000 !important;
            border: 1px solid #dee2e6 !important;
        }
    }
</style>
@endpush

@section('content')

<!-- Page Content -->
<div class="content">
    <div class="container">

        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <div class="invoice-content">
                    <div class="invoice-item">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="invoice-logo">
                                    <img src="{{ !empty($siteSettings['logo']) ? asset($siteSettings['logo']) : asset('assets/img/logo.png') }}" alt="logo">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <p class="invoice-details">
                                    <strong>Prescription:</strong> #PR{{ sprintf('%04d', $prescription->id) }} <br>
                                    <strong>Issued:</strong> {{ $prescription->created_at->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Prescription Header -->
                    <div class="invoice-item">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="invoice-info">
                                    <strong class="customer-text">Doctor</strong>
                                    <p class="invoice-details invoice-details-two">
                                        Dr. {{ optional(optional($prescription->doctor)->user)->name ?? 'Unknown' }} <br>
                                        {{ optional($prescription->doctor)->specialization }} <br>
                                        {{ optional($prescription->doctor)->clinic_address }} <br>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="invoice-info invoice-info2">
                                    <strong class="customer-text">Patient</strong>
                                    <p class="invoice-details">
                                        {{ optional(optional($prescription->patient)->user)->name ?? 'Unknown' }} <br>
                                        {{ optional($prescription->patient)->phone }} <br>
                                        {{ optional($prescription->patient)->address }} <br>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Prescription Header -->

                    <!-- Diagnosis Info -->
                    <div class="invoice-item">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="invoice-info">
                                    <strong class="customer-text mb-2">Clinical Details</strong>
                                    <div class="row">
                                        @if($prescription->symptoms)
                                        <div class="col-md-4">
                                            <strong>Symptoms:</strong>
                                            <p class="text-muted">{{ $prescription->symptoms }}</p>
                                        </div>
                                        @endif
                                        @if($prescription->diagnosis)
                                        <div class="col-md-4">
                                            <strong>Diagnosis:</strong>
                                            <p class="text-muted">{{ $prescription->diagnosis }}</p>
                                        </div>
                                        @endif
                                        @if($prescription->notes)
                                        <div class="col-md-4">
                                            <strong>Notes:</strong>
                                            <p class="text-muted">{{ $prescription->notes }}</p>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Diagnosis Info -->

                    <!-- Medicine List -->
                    <div class="invoice-item invoice-table-wrap mt-3">
                        <div class="row">
                            <div class="col-md-12">
                                <strong class="customer-text mb-2">Medicines</strong>
                                <div class="table-responsive">
                                    <table class="invoice-table table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Medicine Name</th>
                                                <th class="text-center">Quantity</th>
                                                <th class="text-center">Days</th>
                                                <th class="text-center">Time (M-A-E-N)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($prescription->items as $item)
                                            <tr>
                                                <td>{{ $item->medicine_name }}</td>
                                                <td class="text-center">{{ $item->quantity ?? '-' }}</td>
                                                <td class="text-center">{{ $item->days ?? '-' }}</td>
                                                <td class="text-center">
                                                    {{ $item->morning ? '1' : '0' }} - 
                                                    {{ $item->afternoon ? '1' : '0' }} - 
                                                    {{ $item->evening ? '1' : '0' }} - 
                                                    {{ $item->night ? '1' : '0' }}
                                                </td>
                                            </tr>
                                            @endforeach
                                            @if($prescription->items->count() == 0)
                                            <tr>
                                                <td colspan="4" class="text-center">No medicines prescribed.</td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Medicine List -->
                    
                    <div class="row mt-4">
                        <div class="col-md-12 text-end">
                            <div class="signature-wrap">
                                <div class="sign-name mt-4">
                                    <p class="mb-0">( Dr. {{ optional(optional($prescription->doctor)->user)->name ?? 'Unknown' }} )</p>
                                    <span class="text-muted">Signature</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-5" data-html2canvas-ignore="true">
                        <div class="col-md-12 text-center">
                            <button class="btn btn-secondary me-2" id="download-pdf-btn">
                                <i class="fas fa-download"></i> Download PDF
                            </button>
                            <button class="btn btn-primary" onclick="window.print()">
                                <i class="fas fa-print"></i> Print Prescription
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

</div>
<!-- /Page Content -->

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const downloadBtn = document.getElementById('download-pdf-btn');
        if (downloadBtn) {
            downloadBtn.addEventListener('click', function () {
                const element = document.querySelector('.invoice-content');
                const opt = {
                    margin:       0.3,
                    filename:     'prescription-PR{{ sprintf("%04d", $prescription->id) }}.pdf',
                    image:        { type: 'jpeg', quality: 0.98 },
                    html2canvas:  { scale: 2, useCORS: true },
                    jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
                };

                html2pdf().set(opt).from(element).save();
            });
        }
    });
</script>
@endpush
@endsection

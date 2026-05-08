@extends('layouts.app')

@section('title', 'Prescription View - Doccure')

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

                    <div class="row mt-5">
                        <div class="col-md-12 text-center">
                            <button class="btn btn-primary" onclick="window.print()"><i class="fas fa-print"></i> Print Prescription</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

</div>
<!-- /Page Content -->
@endsection

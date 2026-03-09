@extends('layouts.app')

@section('title', 'Invoices - ' . ($siteSettings['site_name'] ?? 'Doccure'))

@section('content')
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-md-5 col-lg-4 col-xl-3 theiaStickySidebar">
                @include('frontend.includes.doctor-sidebar')
            </div>
            <div class="col-md-7 col-lg-8 col-xl-9">
                <div class="card card-table">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-center mb-0">
                                <thead>
                                    <tr>
                                        <th>Invoice No</th>
                                        <th>Patient</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($invoices as $invoice)
                                    <tr>
                                        <td><a href="#">#INV{{ sprintf('%04d', $invoice->id) }}</a></td>
                                        <td>
                                            <h2 class="table-avatar">
                                                <a href="#" class="avatar avatar-sm me-2">
                                                    <img class="avatar-img rounded-circle"
                                                        src="{{ optional($invoice->patient)->profile_image ? asset('storage/' . $invoice->patient->profile_image) : asset('assets/img/patients/patient.jpg') }}"
                                                        alt="Patient Image">
                                                </a>
                                                <a href="#">{{ optional(optional($invoice->patient)->user)->name ?? 'Unknown' }}</a>
                                            </h2>
                                        </td>
                                        <td>৳{{ number_format($invoice->fee, 2) }}</td>
                                        <td>{{ \Carbon\Carbon::parse($invoice->appointment_date)->format('d M Y') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $invoice->status == 'completed' ? 'success' : 'info' }}">
                                                {{ ucfirst($invoice->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <i class="fas fa-file-invoice fa-2x text-muted mb-2 d-block"></i>
                                            No invoices found.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    {{ $invoices->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

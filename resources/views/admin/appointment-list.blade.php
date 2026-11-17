@extends('layouts.admin')

@section('title', 'Appointments - ' . ($siteSettings['site_name'] ?? 'abcsheba Admin'))

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Appointments</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Appointments</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">All Appointments</h4>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table id="appointmentsTable" class="table table-hover table-center">
                            <thead>
                                <tr>
                                    <th>Doctor Name</th>
                                    <th>Speciality</th>
                                    <th>Patient Name</th>
                                    <th>Appointment Time</th>
                                    <th>Status</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($appointments ?? [] as $appointment)
                                    <tr>
                                        <td>
                                            <div class="table-avatar">
                                                <a href="#" class="avatar avatar-sm me-2">
                                                    <img class="avatar-img rounded-circle"
                                                        src="{{ $appointment->doctor?->user?->profile_image ? asset($appointment->doctor->user->profile_image) : asset('backend/img/doctors/doctor-default.jpg') }}"
                                                        alt="Doctor"
                                                        style="width: 40px; height: 40px; object-fit: cover;">
                                                </a>
                                                <a href="#" class="text-dark">Dr. {{ $appointment->doctor?->user?->name ?? 'N/A' }}</a>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $appointment->doctor?->speciality?->name ?? 'General' }}</span>
                                        </td>
                                        <td>
                                            <div class="table-avatar">
                                                <a href="#" class="avatar avatar-sm me-2">
                                                    <img class="avatar-img rounded-circle"
                                                        src="{{ $appointment->patient?->user?->profile_image ? asset($appointment->patient->user->profile_image) : asset('backend/img/patients/patient-default.jpg') }}"
                                                        alt="Patient"
                                                        style="width: 40px; height: 40px; object-fit: cover;">
                                                </a>
                                                <a href="#" class="text-dark">{{ $appointment->patient?->user?->name ?? 'N/A' }}</a>
                                            </div>
                                        </td>
                                        <td>
                                            <small class="d-block">{{ $appointment->appointment_date?->format('d M Y') ?? 'N/A' }}</small>
                                            <span class="text-primary small">{{ $appointment->appointment_time ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            @if($appointment->status === 'completed')
                                                <span class="badge bg-success">Completed</span>
                                            @elseif($appointment->status === 'cancelled')
                                                <span class="badge bg-danger">Cancelled</span>
                                            @elseif($appointment->status === 'confirmed')
                                                <span class="badge bg-info">Confirmed</span>
                                            @else
                                                <span class="badge bg-warning">{{ ucfirst($appointment->status ?? 'Pending') }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if($appointment->consultation_fee && $appointment->consultation_fee > 0)
                                                <strong>৳{{ number_format($appointment->consultation_fee, 2) }}</strong>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <p class="text-muted">No appointments found</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .table-avatar {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .table-avatar .avatar-img {
            border: 1px solid #ddd;
            padding: 2px;
        }

        .table-avatar a {
            text-decoration: none;
            font-weight: 500;
        }

        .table-avatar a:hover {
            color: #007bff;
        }

        #appointmentsTable thead th {
            background-color: #f8f9fa;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }

        #appointmentsTable tbody tr:hover {
            background-color: #f8f9fa;
        }

        .text-dark {
            color: #333;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize DataTable only once
            if ($.fn.DataTable) {
                const table = $('#appointmentsTable');
                if (table.length && !$.fn.DataTable.isDataTable(table[0])) {
                    table.DataTable({
                        "pageLength": 10,
                        "bFilter": true,
                        "bInfo": true,
                        "language": {
                            "paginate": {
                                "previous": "Previous",
                                "next": "Next"
                            }
                        }
                    });
                }
            }
        });
    </script>
@endsection
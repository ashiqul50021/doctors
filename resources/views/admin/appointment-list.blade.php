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
                        <table class="table table-hover table-center datatable">
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
                                                        alt="Doctor">
                                                </a>
                                                <a href="#">Dr. {{ $appointment->doctor?->user?->name ?? 'N/A' }}</a>
                                            </div>
                                        </td>
                                        <td>{{ $appointment->doctor?->speciality?->name ?? '-' }}</td>
                                        <td>
                                            <div class="table-avatar">
                                                <a href="#" class="avatar avatar-sm me-2">
                                                    <img class="avatar-img rounded-circle"
                                                        src="{{ $appointment->patient?->user?->profile_image ? asset($appointment->patient->user->profile_image) : asset('backend/img/patients/patient-default.jpg') }}"
                                                        alt="Patient">
                                                </a>
                                                <a href="#">{{ $appointment->patient?->user?->name ?? 'N/A' }}</a>
                                            </div>
                                        </td>
                                        <td>
                                            {{ $appointment->appointment_date?->format('d M Y') }}<br>
                                            <span class="text-primary d-block small">{{ $appointment->appointment_time ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $appointment->status === 'completed' ? 'success' : ($appointment->status === 'cancelled' ? 'danger' : 'primary') }}">
                                                {{ ucfirst($appointment->status ?? 'pending') }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            {{ $appointment->consultation_fee ? '৳' . number_format($appointment->consultation_fee, 2) : '-' }}
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
            gap: 8px;
        }

        .table-avatar img {
            width: 40px;
            height: 40px;
            object-fit: cover;
        }

        .table-avatar a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
        }

        .table-avatar a:hover {
            color: #007bff;
        }
    </style>
@endsection
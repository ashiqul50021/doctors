@extends('layouts.app')

@section('title', 'Patient Dashboard - ' . ($siteSettings['site_name'] ?? 'abcsheba'))

@section('content')

    <!-- Page Content -->
    <div class="content">
        <div class="container">

            <div class="row">

                <!-- Profile Sidebar -->
                <div class="col-md-5 col-lg-4 col-xl-3 theiaStickySidebar">
                    @include('frontend.includes.patient-sidebar')
                </div>
                <!-- / Profile Sidebar -->

                <div class="col-md-7 col-lg-8 col-xl-9">
                    <div class="card">
                        <div class="card-body pt-0">

                            <!-- Tab Menu -->
                            <nav class="user-tabs mb-4">
                                <ul class="nav nav-tabs nav-tabs-bottom nav-justified">
                                    <li class="nav-item">
                                        <a class="nav-link active" href="#pat_appointments"
                                            data-bs-toggle="tab">Appointments</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#pat_prescriptions" data-bs-toggle="tab">Prescriptions</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#pat_medical_records" data-bs-toggle="tab"><span
                                                class="med-records">Medical Records</span></a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#pat_billing" data-bs-toggle="tab">Billing</a>
                                    </li>
                                </ul>
                            </nav>
                            <!-- /Tab Menu -->

                            <!-- Tab Content -->
                            <div class="tab-content pt-0">

                                <!-- Appointment Tab -->
                                <div id="pat_appointments" class="tab-pane fade show active">
                                    <div class="card card-table mb-0">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-hover table-center mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Doctor</th>
                                                            <th>Appt Date</th>
                                                            <th>Booking Date</th>
                                                            <th>Amount</th>
                                                            <th>Follow Up</th>
                                                            <th>Status</th>
                                                            <th></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($appointments ?? [] as $appt)
                                                        <tr>
                                                            <td>
                                                                <h2 class="table-avatar">
                                                                    <a href="{{ route('doctors.profile', $appt->doctor->id) }}"
                                                                        class="avatar avatar-sm me-2">
                                                                        <img class="avatar-img rounded-circle"
                                                                            src="{{ $appt->doctor->profile_image ? asset($appt->doctor->profile_image) : asset('assets/img/doctors/doctor-thumb-01.jpg') }}"
                                                                            alt="User Image">
                                                                    </a>
                                                                    <a href="{{ route('doctors.profile', $appt->doctor->id) }}" style="text-decoration: none;">
                                                                        {{ str_starts_with(strtolower($appt->doctor->user->name), 'dr.') ? $appt->doctor->user->name : 'Dr. ' . $appt->doctor->user->name }}
                                                                        <span>{{ $appt->doctor->speciality->name ?? 'General' }}</span>
                                                                    </a>
                                                                </h2>
                                                            </td>
                                                            <td>{{ \Carbon\Carbon::parse($appt->appointment_date)->format('d M Y') }} <span class="d-block text-info">{{ \Carbon\Carbon::parse($appt->appointment_time)->format('h:i A') }}</span>
                                                            </td>
                                                            <td>{{ $appt->created_at->format('d M Y') }}</td>
                                                            <td>৳{{ $appt->fee }}</td>
                                                            <td>
                                                                @if($appt->status === 'completed')
                                                                    {{ \Carbon\Carbon::parse($appt->appointment_date)->addDays(7)->format('d M Y') }}
                                                                @else
                                                                    -
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if($appt->status === 'confirmed')
                                                                    <span class="badge badge-pill bg-success-light">Confirm</span>
                                                                @elseif($appt->status === 'pending')
                                                                    <span class="badge badge-pill bg-warning-light">Pending</span>
                                                                @elseif($appt->status === 'completed')
                                                                    <span class="badge badge-pill bg-info-light">Completed</span>
                                                                @else
                                                                    <span class="badge badge-pill bg-danger-light">{{ ucfirst($appt->status) }}</span>
                                                                @endif
                                                            </td>
                                                            <td class="text-end">
                                                                <div class="table-action">
                                                                    @if($appt->type === 'online' && $appt->meeting_link && in_array($appt->status, ['pending', 'confirmed']))
                                                                        <a href="{{ $appt->meeting_link }}" target="_blank" class="btn btn-sm bg-success-light">
                                                                            <i class="fas fa-video"></i> Join Call
                                                                        </a>
                                                                    @endif
                                                                    <a href="{{ route('chat', ['user_id' => $appt->doctor->user_id]) }}"
                                                                        class="btn btn-sm bg-success-light">
                                                                        <i class="far fa-comment-dots"></i> Chat
                                                                    </a>
                                                                    <a href="{{ route('invoice.view', $appt->id) }}"
                                                                        class="btn btn-sm bg-info-light">
                                                                        <i class="far fa-eye"></i> View
                                                                    </a>
                                                                    <a href="{{ route('invoice.view', $appt->id) }}?print=1" target="_blank"
                                                                        class="btn btn-sm bg-primary-light">
                                                                        <i class="fas fa-print"></i> Print
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        @empty
                                                        <tr>
                                                            <td colspan="7" class="text-center">No appointments found.</td>
                                                        </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /Appointment Tab -->

                                <!-- Prescription Tab -->
                                <div id="pat_prescriptions" class="tab-pane fade">
                                    <div class="card card-table mb-0">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-hover table-center mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Date </th>
                                                            <th>Name</th>
                                                            <th>Created by </th>
                                                            <th></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($prescriptions ?? [] as $prescription)
                                                        <tr>
                                                            <td>{{ $prescription->created_at->format('d M Y') }}</td>
                                                            <td>Prescription {{ $prescription->id }}</td>
                                                            <td>
                                                                <h2 class="table-avatar">
                                                                    <a href="{{ route('doctors.profile', $prescription->doctor_id) }}"
                                                                        class="avatar avatar-sm me-2">
                                                                        <img class="avatar-img rounded-circle"
                                                                            src="{{ optional($prescription->doctor)->profile_image ? asset($prescription->doctor->profile_image) : asset('assets/img/doctors/doctor-thumb-01.jpg') }}"
                                                                            alt="User Image">
                                                                    </a>
                                                                    <a href="{{ route('doctors.profile', $prescription->doctor_id) }}" style="text-decoration: none;">
                                                                        {{ str_starts_with(strtolower(optional(optional($prescription->doctor)->user)->name ?? ''), 'dr.') ? optional(optional($prescription->doctor)->user)->name : 'Dr. ' . (optional(optional($prescription->doctor)->user)->name ?? 'Unknown') }}
                                                                        <span>{{ optional($prescription->doctor)->speciality->name ?? 'General' }}</span>
                                                                    </a>
                                                                </h2>
                                                            </td>
                                                            <td class="text-end">
                                                                <div class="table-action">
                                                                    <a href="{{ route('patient.prescription.view', $prescription->id) }}"
                                                                        class="btn btn-sm bg-info-light">
                                                                        <i class="far fa-eye"></i> View
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        @empty
                                                        <tr>
                                                            <td colspan="4" class="text-center">No prescriptions found</td>
                                                        </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /Prescription Tab -->

                                <!-- Medical Records Tab -->
                                <div id="pat_medical_records" class="tab-pane fade">
                                    <div class="card card-table mb-0">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-hover table-center mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Date </th>
                                                            <th>Description</th>
                                                            <th>Attachment</th>
                                                            <th>Created</th>
                                                            <th></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td colspan="6" class="text-center">No medical records found.</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /Medical Records Tab -->

                                <!-- Billing Tab -->
                                <div id="pat_billing" class="tab-pane fade">
                                    <div class="card card-table mb-0">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-hover table-center mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Invoice No</th>
                                                            <th>Doctor</th>
                                                            <th>Amount</th>
                                                            <th>Paid On</th>
                                                            <th></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($appointments ?? [] as $appt)
                                                        <tr>
                                                            <td>
                                                                <a href="{{ route('invoice.view', $appt->id) }}">#INV-{{ sprintf('%04d', $appt->id) }}</a>
                                                            </td>
                                                            <td>
                                                                <h2 class="table-avatar">
                                                                    <a href="{{ route('doctors.profile', $appt->doctor->id) }}"
                                                                        class="avatar avatar-sm me-2">
                                                                        <img class="avatar-img rounded-circle"
                                                                            src="{{ $appt->doctor->profile_image ? asset($appt->doctor->profile_image) : asset('assets/img/doctors/doctor-thumb-01.jpg') }}"
                                                                            alt="User Image">
                                                                    </a>
                                                                    <a href="{{ route('doctors.profile', $appt->doctor->id) }}" style="text-decoration: none;">
                                                                        {{ str_starts_with(strtolower($appt->doctor->user->name), 'dr.') ? $appt->doctor->user->name : 'Dr. ' . $appt->doctor->user->name }}
                                                                        <span>{{ $appt->doctor->speciality->name ?? 'General' }}</span>
                                                                    </a>
                                                                </h2>
                                                            </td>
                                                            <td>৳{{ number_format($appt->fee, 2) }}</td>
                                                            <td>{{ $appt->created_at->format('d M Y') }}</td>
                                                            <td class="text-end">
                                                                <div class="table-action">
                                                                    <a href="{{ route('invoice.view', $appt->id) }}"
                                                                        class="btn btn-sm bg-info-light">
                                                                        <i class="far fa-eye"></i> View
                                                                    </a>
                                                                    <a href="{{ route('invoice.view', $appt->id) }}?print=1" target="_blank"
                                                                        class="btn btn-sm bg-primary-light">
                                                                        <i class="fas fa-print"></i> Print
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        @empty
                                                        <tr>
                                                            <td colspan="5" class="text-center">No billing records found.</td>
                                                        </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /Billing Tab -->

                            </div>
                            <!-- Tab Content -->

                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
    <!-- /Page Content -->
@endsection

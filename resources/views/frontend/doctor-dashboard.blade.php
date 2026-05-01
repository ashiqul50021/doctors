@extends('layouts.app')

@section('title', 'Doctor Dashboard - ' . ($siteSettings['site_name'] ?? 'Doccure'))

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
                                                                                    src="{{ optional($appt->patient)->profile_image ? asset('storage/' . $appt->patient->profile_image) : asset('assets/img/patients/patient.jpg') }}"
                                                                                    alt="Patient Image"></a>
                                                                            <a href="{{ route('patient.profile', $appt->patient->id ?? '') }}">{{ optional(optional($appt->patient)->user)->name ?? 'Unknown' }} <span>#PT{{ sprintf('%04d', $appt->patient_id) }}</span></a>
                                                                        </h2>
                                                                    </td>
                                                                    <td>{{ \Carbon\Carbon::parse($appt->appointment_date)->format('d M Y') }} <span class="d-block text-info">{{ \Carbon\Carbon::parse($appt->appointment_time)->format('h:i A') }}</span></td>
                                                                    <td>{{ $appt->reason ?? 'General' }}</td>
                                                                    <td>{{ ucfirst($appt->type ?? 'offline') }}</td>
                                                                    <td class="text-center">${{ number_format($appt->fee, 2) }}</td>
                                                                    <td class="text-end">
                                                                        <div class="table-action">
                                                                            <a href="javascript:void(0);"
                                                                                class="btn btn-sm bg-info-light">
                                                                                <i class="far fa-eye"></i> View
                                                                            </a>

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
                                                            @empty
                                                                <tr>
                                                                    <td colspan="6" class="text-center">No upcoming appointments found.</td>
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
                                        <div class="card card-table mb-0">
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-hover table-center mb-0">
                                                        <thead>
                                                            <tr>
                                                                <th>Patient Name</th>
                                                                <th>Appt Date</th>
                                                                <th>Purpose</th>
                                                                <th>Type</th>
                                                                <th class="text-center">Paid Amount</th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse($todayAppointments as $appt)
                                                                <tr>
                                                                    <td>
                                                                        <h2 class="table-avatar">
                                                                            <a href="{{ route('patient.profile', $appt->patient->id ?? '') }}"
                                                                                class="avatar avatar-sm me-2"><img
                                                                                    class="avatar-img rounded-circle"
                                                                                    src="{{ optional($appt->patient)->profile_image ? asset('storage/' . $appt->patient->profile_image) : asset('assets/img/patients/patient.jpg') }}"
                                                                                    alt="Patient Image"></a>
                                                                            <a href="{{ route('patient.profile', $appt->patient->id ?? '') }}">{{ optional(optional($appt->patient)->user)->name ?? 'Unknown' }} <span>#PT{{ sprintf('%04d', $appt->patient_id) }}</span></a>
                                                                        </h2>
                                                                    </td>
                                                                    <td>{{ \Carbon\Carbon::parse($appt->appointment_date)->format('d M Y') }} <span class="d-block text-info">{{ \Carbon\Carbon::parse($appt->appointment_time)->format('h:i A') }}</span></td>
                                                                    <td>{{ $appt->reason ?? 'General' }}</td>
                                                                    <td>{{ ucfirst($appt->type ?? 'offline') }}</td>
                                                                    <td class="text-center">${{ number_format($appt->fee, 2) }}</td>
                                                                    <td class="text-end">
                                                                        <div class="table-action">
                                                                            <a href="javascript:void(0);"
                                                                                class="btn btn-sm bg-info-light">
                                                                                <i class="far fa-eye"></i> View
                                                                            </a>

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
                                                            @empty
                                                                <tr>
                                                                    <td colspan="6" class="text-center">No appointments for today.</td>
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

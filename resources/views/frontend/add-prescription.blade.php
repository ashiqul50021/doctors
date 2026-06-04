@extends('layouts.app')

@section('title', 'Add Prescription - abcsheba')

@push('styles')
<style>
    /* Premium Add Prescription Styles */
    .widget-profile.pat-widget-profile {
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
        overflow: hidden;
        background: #fff;
    }
    .widget-profile.pat-widget-profile .card-body {
        padding: 24px;
    }
    .widget-profile.pat-widget-profile .pro-widget-content {
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 20px;
        margin-bottom: 20px;
    }
    .pro-widget-content .profile-info-widget {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    .pro-widget-content .profile-info-widget .booking-doc-img {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        overflow: hidden;
        border: 4px solid #eff6ff;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
        margin-bottom: 16px;
    }
    .pro-widget-content .profile-info-widget .booking-doc-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .pro-widget-content .profile-det-info h3 a {
        font-size: 20px;
        font-weight: 700;
        color: #0f172a;
        text-decoration: none;
    }
    .patient-details h5 {
        font-size: 13px;
        color: #64748b;
        margin-top: 6px;
        font-weight: 500;
    }
    .patient-details h5 b {
        color: #334155;
    }
    .patient-info ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .patient-info ul li {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px dashed #e2e8f0;
        font-size: 14px;
        color: #64748b;
        font-weight: 500;
    }
    .patient-info ul li:last-child {
        border-bottom: none;
    }
    .patient-info ul li span {
        color: #0f172a;
        font-weight: 700;
    }

    /* Main Form Styles */
    .card.prescription-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(15, 23, 42, 0.06);
        background: #fff;
        overflow: hidden;
    }
    .card.prescription-card .card-header {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
        padding: 24px;
        border: none;
    }
    .card.prescription-card .card-header .card-title {
        color: #fff !important;
        font-size: 22px;
        font-weight: 800;
        letter-spacing: -0.5px;
    }
    .biller-info h4 {
        font-size: 22px;
        font-weight: 800;
        color: #1e293b;
        letter-spacing: -0.5px;
        margin-bottom: 4px;
    }
    .billing-info h4 {
        font-size: 22px;
        font-weight: 800;
        color: #2563eb;
        letter-spacing: -0.5px;
        margin-bottom: 4px;
    }
    .form-group label {
        font-weight: 700;
        color: #334155;
        margin-bottom: 8px;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .form-control {
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        padding: 12px 16px;
        font-size: 15px;
        color: #0f172a;
        transition: all 0.2s ease;
        background-color: #f8fafc;
    }
    .form-control:focus {
        border-color: #2563eb;
        background-color: #fff;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
    }
    textarea.form-control {
        resize: none;
    }

    /* Medicine Table and Add Button */
    .add-more-item a {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #eff6ff;
        color: #2563eb;
        padding: 10px 20px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 14px;
        text-decoration: none;
        transition: all 0.2s ease;
        border: 1px solid #dbeafe;
    }
    .add-more-item a:hover {
        background: #2563eb;
        color: #fff;
        border-color: #2563eb;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
    }
    .card.card-table {
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: none;
        margin-top: 15px;
    }
    .table thead th {
        background: #f8fafc;
        border-bottom: 1px solid #cbd5e1;
        color: #475569;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.5px;
        padding: 16px 20px;
    }
    .table tbody td {
        padding: 16px 20px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }
    .table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Custom Checked Pill Buttons for Time selection */
    .time-check-inline {
        position: relative;
        display: inline-block;
        margin-right: 6px;
    }
    .time-check-inline input[type="checkbox"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }
    .time-check-inline label {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        color: #475569;
        border-radius: 10px;
        cursor: pointer;
        font-weight: 700;
        font-size: 14px;
        transition: all 0.2s ease;
        margin-bottom: 0;
        user-select: none;
    }
    .time-check-inline input[type="checkbox"]:checked + label {
        background: #2563eb;
        border-color: #2563eb;
        color: #ffffff;
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.25);
    }
    .time-check-inline label:hover {
        background: #e2e8f5;
    }
    .time-check-inline input[type="checkbox"]:checked + label:hover {
        background: #1d4ed8;
    }

    /* Trash button styling */
    .btn.bg-danger-light.trash {
        background: #fef2f2 !important;
        color: #ef4444 !important;
        border: 1px solid #fee2e2;
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }
    .btn.bg-danger-light.trash:hover {
        background: #ef4444 !important;
        color: #fff !important;
        border-color: #ef4444;
        box-shadow: 0 4px 10px rgba(239, 68, 68, 0.25);
    }

    /* Signature and Button */
    .sign-name p {
        font-size: 16px;
        font-weight: 700;
        color: #1e293b;
    }
    .submit-section .submit-btn {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        border: none;
        padding: 14px 40px;
        font-weight: 700;
        font-size: 16px;
        border-radius: 50px;
        box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
        transition: all 0.2s ease;
    }
    .submit-section .submit-btn:hover {
        box-shadow: 0 6px 20px rgba(37, 99, 235, 0.45);
        transform: translateY(-1px);
    }
</style>
@endpush

@section('content')
    <!-- Breadcrumb -->
    <div class="breadcrumb-bar">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-12 col-12">
                    <nav aria-label="breadcrumb" class="page-breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('doctors.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Add Prescription</li>
                        </ol>
                    </nav>
                    <h2 class="breadcrumb-title">Add Prescription</h2>
                </div>
            </div>
        </div>
    </div>
    <!-- /Breadcrumb -->

    <!-- Page Content -->
    <div class="content">
        <div class="container">

        <div class="row">
            <div class="col-md-5 col-lg-4 col-xl-3 theiaStickySidebar">

                <!-- Profile Widget -->
                <div class="card widget-profile pat-widget-profile">
                    <div class="card-body">
                        <div class="pro-widget-content">
                            <div class="profile-info-widget">
                                <a href="#" class="booking-doc-img">
                                    <img src="{{ optional($appointment->patient)->profile_image ? asset($appointment->patient->profile_image) : asset('assets/img/patients/patient.jpg') }}" alt="User Image">
                                </a>
                                <div class="profile-det-info">
                                    <h3><a href="{{ route('patient.profile', $appointment->patient_id) }}">{{ optional(optional($appointment->patient)->user)->name ?? 'Unknown' }}</a></h3>
                                    <div class="patient-details">
                                        <h5><b>Patient ID :</b> PT{{ sprintf('%04d', $appointment->patient_id) }}</h5>
                                        @if(optional($appointment->patient)->address)
                                        <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> {{ $appointment->patient->address }}</h5>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="patient-info">
                            <ul>
                                <li>Phone <span>{{ optional($appointment->patient)->phone ?? 'N/A' }}</span></li>
                                <li>Age <span>{{ optional($appointment->patient)->age ?? 'N/A' }} Years, {{ optional($appointment->patient)->gender ?? 'N/A' }}</span></li>
                                <li>Blood Group <span>{{ optional($appointment->patient)->blood_group ?? 'N/A' }}</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- /Profile Widget -->

            </div>

            <div class="col-md-7 col-lg-8 col-xl-9">
                <div class="card prescription-card">
                    <div class="card-header">
                        <h4 class="card-title mb-0 text-white">Add Prescription</h4>
                    </div>
                    
                    @if ($errors->any())
                        <div class="alert alert-danger m-3">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('doctors.store.prescription') }}" method="POST">
                    @csrf
                    <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">
                    <div class="card-body">
                        @php
                            $doctorName = optional($doctor->user)->name ?? 'Unknown';
                            if (!\Illuminate\Support\Str::startsWith(strtolower($doctorName), ['dr.', 'dr '])) {
                                $doctorName = 'Dr. ' . $doctorName;
                            }
                        @endphp
                        <div class="row align-items-center">
                            <div class="col-sm-6">
                                <div class="biller-info">
                                    <h4 class="d-block">{{ $doctorName }}</h4>
                                    <span class="d-block text-sm text-muted font-weight-bold">{{ $doctor->speciality->name ?? 'Medical Specialist' }}</span>
                                </div>
                            </div>
                            <div class="col-sm-6 text-sm-right">
                                <div class="billing-info text-end">
                                    <h4 class="d-block">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d M Y') }}</h4>
                                    <span class="d-block text-muted font-weight-bold">#APT{{ sprintf('%04d', $appointment->id) }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>Symptoms</label>
                                    <textarea class="form-control" name="symptoms" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>Diagnosis</label>
                                    <textarea class="form-control" name="diagnosis" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label>Notes</label>
                                    <textarea class="form-control" name="notes" rows="3"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Add Item -->
                        <div class="add-more-item text-end mt-4 mb-2">
                            <a href="javascript:void(0);" onclick="addPrescriptionRow()"><i class="fas fa-plus-circle"></i> Add Medicine</a>
                        </div>
                        <!-- /Add Item -->

                        <!-- Prescription Item -->
                        <div class="card card-table">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-center" id="prescription_table">
                                        <thead>
                                            <tr>
                                                <th style="min-width: 200px">Name</th>
                                                <th style="min-width: 100px">Quantity</th>
                                                <th style="min-width: 100px">Days</th>
                                                <th style="min-width: 100px;">Time</th>
                                                <th style="min-width: 80px;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <input class="form-control" name="medicine_name[0]" type="text" required>
                                                </td>
                                                <td>
                                                    <input class="form-control" name="quantity[0]" type="text" placeholder="e.g. 1 tab">
                                                </td>
                                                <td>
                                                    <input class="form-control" name="days[0]" type="text" placeholder="e.g. 7 days">
                                                </td>
                                                <td>
                                                    <div class="time-check-inline">
                                                        <input id="morning_0" name="morning[0]" type="checkbox">
                                                        <label for="morning_0">M</label>
                                                    </div>
                                                    <div class="time-check-inline">
                                                        <input id="afternoon_0" name="afternoon[0]" type="checkbox">
                                                        <label for="afternoon_0">A</label>
                                                    </div>
                                                    <div class="time-check-inline">
                                                        <input id="evening_0" name="evening[0]" type="checkbox">
                                                        <label for="evening_0">E</label>
                                                    </div>
                                                    <div class="time-check-inline">
                                                        <input id="night_0" name="night[0]" type="checkbox">
                                                        <label for="night_0">N</label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <a href="javascript:void(0);" class="btn bg-danger-light trash" onclick="removeRow(this)"><i class="far fa-trash-alt"></i></a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- /Prescription Item -->

                        <!-- Signature -->
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <div class="signature-wrap">
                                    <div class="sign-name mt-5">
                                        <p class="mb-0">( Dr. {{ optional($doctor->user)->name ?? 'Unknown' }} )</p>
                                        <span class="text-muted">Signature</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /Signature -->

                        <!-- Submit Section -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="submit-section text-center">
                                    <button type="submit" class="btn btn-primary submit-btn">Save Prescription</button>
                                </div>
                            </div>
                        </div>
                        <!-- /Submit Section -->

                    </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

</div>
<!-- /Page Content -->
@endsection

@push('scripts')
<script src="{{ asset('assets/plugins/theia-sticky-sidebar/ResizeSensor.js') }}"></script>
<script src="{{ asset('assets/plugins/theia-sticky-sidebar/theia-sticky-sidebar.js') }}"></script>

<script>
    let rowIdx = 1;
    function addPrescriptionRow() {
        let tr = `
            <tr>
                <td>
                    <input class="form-control" name="medicine_name[${rowIdx}]" type="text" required>
                </td>
                <td>
                    <input class="form-control" name="quantity[${rowIdx}]" type="text" placeholder="e.g. 1 tab">
                </td>
                <td>
                    <input class="form-control" name="days[${rowIdx}]" type="text" placeholder="e.g. 7 days">
                </td>
                <td>
                    <div class="time-check-inline">
                        <input id="morning_${rowIdx}" name="morning[${rowIdx}]" type="checkbox">
                        <label for="morning_${rowIdx}">M</label>
                    </div>
                    <div class="time-check-inline">
                        <input id="afternoon_${rowIdx}" name="afternoon[${rowIdx}]" type="checkbox">
                        <label for="afternoon_${rowIdx}">A</label>
                    </div>
                    <div class="time-check-inline">
                        <input id="evening_${rowIdx}" name="evening[${rowIdx}]" type="checkbox">
                        <label for="evening_${rowIdx}">E</label>
                    </div>
                    <div class="time-check-inline">
                        <input id="night_${rowIdx}" name="night[${rowIdx}]" type="checkbox">
                        <label for="night_${rowIdx}">N</label>
                    </div>
                </td>
                <td>
                    <a href="javascript:void(0);" class="btn bg-danger-light trash" onclick="removeRow(this)"><i class="far fa-trash-alt"></i></a>
                </td>
            </tr>
        `;
        $('#prescription_table tbody').append(tr);
        rowIdx++;
    }

    function removeRow(btn) {
        $(btn).closest('tr').remove();
    }
</script>
@endpush

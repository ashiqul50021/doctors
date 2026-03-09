@extends('layouts.app')

@section('title', 'Patient Profile - Doccure')

@section('content')

<!-- Page Content -->
<div class="content">
    <div class="container">

        <div class="row">
            <div class="col-md-5 col-lg-4 col-xl-3 theiaStickySidebar dct-dashbd-lft">

                <!-- Profile Widget -->
                <div class="card widget-profile pat-widget-profile">
                    <div class="card-body">
                        <div class="pro-widget-content">
                            <div class="profile-info-widget">
                                <a href="#" class="booking-doc-img">
                                    <img src="{{ asset('assets/img/patients/patient.jpg') }}" alt="User Image">
                                </a>
                                <div class="profile-det-info">
                                    <h3>Richard Wilson</h3>

                                    <div class="patient-details">
                                        <h5><b>Patient ID :</b> PT0016</h5>
                                        <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> Newyork, United States</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="patient-info">
                            <ul>
                                <li>Phone <span>+1 952 001 8563</span></li>
                                <li>Age <span>38 Years, Male</span></li>
                                <li>Blood Group <span>AB+</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- /Profile Widget -->

                <!-- Last Booking -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Last Booking</h4>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <div class="media align-items-center">
                                <div class="me-3">
                                    <img alt="Image placeholder" src="{{ asset('assets/img/doctors/doctor-thumb-02.jpg') }}" class="avatar  rounded-circle">
                                </div>
                                <div class="media-body">
                                    <h5 class="d-block mb-0">Dr. Darren Elder </h5>
                                    <span class="d-block text-sm text-muted">Dentist</span>
                                    <span class="d-block text-sm text-muted">14 Nov 2019 5.00 PM</span>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="media align-items-center">
                                <div class="me-3">
                                    <img alt="Image placeholder" src="{{ asset('assets/img/doctors/doctor-thumb-02.jpg') }}" class="avatar  rounded-circle">
                                </div>
                                <div class="media-body">
                                    <h5 class="d-block mb-0">Dr. Darren Elder </h5>
                                    <span class="d-block text-sm text-muted">Dentist</span>
                                    <span class="d-block text-sm text-muted">12 Nov 2019 11.00 AM</span>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <!-- /Last Booking -->

            </div>

            <div class="col-md-7 col-lg-8 col-xl-9 dct-appoinment">
                <div class="card">
                    <div class="card-body pt-0">
                        <div class="user-tabs">
                            <ul class="nav nav-tabs nav-tabs-bottom nav-justified flex-wrap">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#pat_appointments" data-bs-toggle="tab">Appointments</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#pres" data-bs-toggle="tab"><span>Prescription</span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#medical" data-bs-toggle="tab"><span class="med-records">Medical Records</span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#billing" data-bs-toggle="tab"><span>Billing</span></a>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-content">

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
                                                    <tr>
                                                        <td>
                                                            <h2 class="table-avatar">
                                                                <a href="{{ route('doctor.profile') }}" class="avatar avatar-sm me-2">
                                                                    <img class="avatar-img rounded-circle" src="{{ asset('assets/img/doctors/doctor-thumb-02.jpg') }}" alt="User Image">
                                                                </a>
                                                                <a href="{{ route('doctor.profile') }}">Dr. Darren Elder <span>Dental</span></a>
                                                            </h2>
                                                        </td>
                                                        <td>14 Nov 2019 <span class="d-block text-info">10.00 AM</span></td>
                                                        <td>12 Nov 2019</td>
                                                        <td>$160</td>
                                                        <td>16 Nov 2019</td>
                                                        <td><span class="badge badge-pill bg-success-light">Confirm</span></td>
                                                        <td class="text-end">
                                                            <div class="table-action">
                                                                <a href="javascript:void(0);" class="btn btn-sm bg-success-light">
                                                                    <i class="far fa-edit"></i> Edit
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <!-- More rows as needed -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /Appointment Tab -->

                            <!-- Prescription Tab -->
                            <div class="tab-pane fade" id="pres">
                                <div class="text-end">
                                    <a href="{{ route('add.prescription') }}" class="add-new-btn">Add Prescription</a>
                                </div>
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
                                                    <tr>
                                                        <td>14 Nov 2019</td>
                                                        <td>Prescription 1</td>
                                                        <td>
                                                            <h2 class="table-avatar">
                                                                <a href="{{ route('doctor.profile') }}" class="avatar avatar-sm me-2">
                                                                    <img class="avatar-img rounded-circle" src="{{ asset('assets/img/doctors/doctor-thumb-01.jpg') }}" alt="User Image">
                                                                </a>
                                                                <a href="{{ route('doctor.profile') }}">Dr. Ruby Perrin <span>Dental</span></a>
                                                            </h2>
                                                        </td>
                                                        <td class="text-end">
                                                            <div class="table-action">
                                                                <a href="javascript:void(0);" class="btn btn-sm bg-primary-light">
                                                                    <i class="fas fa-print"></i> Print
                                                                </a>
                                                                <a href="javascript:void(0);" class="btn btn-sm bg-info-light">
                                                                    <i class="far fa-eye"></i> View
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <!-- More rows as needed -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /Prescription Tab -->

                            <!-- Medical Records Tab -->
                            <div class="tab-pane fade" id="medical">
                                <div class="text-end">
                                    <a href="#" class="add-new-btn" data-bs-toggle="modal" data-bs-target="#add_medical_records">Add Medical Records</a>
                                </div>
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
                                                        <td><a href="javascript:void(0);">#MR-0010</a></td>
                                                        <td>14 Nov 2019</td>
                                                        <td>Dental Filling</td>
                                                        <td><a href="#">dental-test.pdf</a></td>
                                                        <td>
                                                            <h2 class="table-avatar">
                                                                <a href="{{ route('doctor.profile') }}" class="avatar avatar-sm me-2">
                                                                    <img class="avatar-img rounded-circle" src="{{ asset('assets/img/doctors/doctor-thumb-01.jpg') }}" alt="User Image">
                                                                </a>
                                                                <a href="{{ route('doctor.profile') }}">Dr. Ruby Perrin <span>Dental</span></a>
                                                            </h2>
                                                        </td>
                                                        <td class="text-end">
                                                            <div class="table-action">
                                                                <a href="javascript:void(0);" class="btn btn-sm bg-primary-light">
                                                                    <i class="fas fa-print"></i> Print
                                                                </a>
                                                                <a href="javascript:void(0);" class="btn btn-sm bg-info-light">
                                                                    <i class="far fa-eye"></i> View
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <!-- More rows as needed -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /Medical Records Tab -->

                            <!-- Billing Tab -->
                            <div class="tab-pane" id="billing">
                                <div class="text-end">
                                    <a class="add-new-btn" href="{{ route('add.billing') }}">Add Billing</a>
                                </div>
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
                                                    <tr>
                                                        <td>
                                                            <a href="{{ route('invoice.view', ['id' => 1]) }}">#INV-0010</a>
                                                        </td>
                                                        <td>
                                                            <h2 class="table-avatar">
                                                                <a href="{{ route('doctor.profile') }}" class="avatar avatar-sm me-2">
                                                                    <img class="avatar-img rounded-circle" src="{{ asset('assets/img/doctors/doctor-thumb-01.jpg') }}" alt="User Image">
                                                                </a>
                                                                <a href="{{ route('doctor.profile') }}">Ruby Perrin <span>Dental</span></a>
                                                            </h2>
                                                        </td>
                                                        <td>$450</td>
                                                        <td>14 Nov 2019</td>
                                                        <td class="text-end">
                                                            <div class="table-action">
                                                                <a href="javascript:void(0);" class="btn btn-sm bg-primary-light">
                                                                    <i class="fas fa-print"></i> Print
                                                                </a>
                                                                <a href="javascript:void(0);" class="btn btn-sm bg-info-light">
                                                                    <i class="far fa-eye"></i> View
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <!-- More rows as needed -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Billing Tab -->

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
<!-- /Page Content -->

<!-- Add Medical Records Modal -->
<div class="modal fade custom-modal" id="add_medical_records">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Medical Records</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Date</label>
                        <input type="text" class="form-control datetimepicker" value="31-10-2019">
                    </div>
                    <div class="mb-3">
                        <label>Description ( Optional )</label>
                        <textarea class="form-control"></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Upload File</label>
                        <input type="file" class="form-control">
                    </div>
                    <div class="submit-section text-center">
                        <button type="submit" class="btn btn-primary submit-btn">Submit</button>
                        <button type="button" class="btn btn-secondary submit-btn" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Add Medical Records Modal -->
@endsection

@push('scripts')
<script src="{{ asset('assets/plugins/theia-sticky-sidebar/ResizeSensor.js') }}"></script>
<script src="{{ asset('assets/plugins/theia-sticky-sidebar/theia-sticky-sidebar.js') }}"></script>
@endpush

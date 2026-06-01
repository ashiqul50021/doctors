@extends('layouts.app')

@section('title', 'Add Prescription - abcsheba')

@section('content')

<!-- Page Content -->
<div class="content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-md-5 col-lg-4 col-xl-3 theiaStickySidebar">

                <!-- Profile Widget -->
                <div class="card widget-profile pat-widget-profile">
                    <div class="card-body">
                        <div class="pro-widget-content">
                            <div class="profile-info-widget">
                                <a href="#" class="booking-doc-img">
                                    <img src="{{ optional($appointment->patient)->profile_image ? asset('storage/' . $appointment->patient->profile_image) : asset('assets/img/patients/patient.jpg') }}" alt="User Image">
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
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Add Prescription</h4>
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
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="biller-info">
                                    <h4 class="d-block">Dr. {{ optional($doctor->user)->name ?? 'Unknown' }}</h4>
                                    <span class="d-block text-sm text-muted">{{ $doctor->specialization ?? 'Doctor' }}</span>
                                </div>
                            </div>
                            <div class="col-sm-6 text-sm-right">
                                <div class="billing-info text-end">
                                    <h4 class="d-block">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d M Y') }}</h4>
                                    <span class="d-block text-muted">#APT{{ sprintf('%04d', $appointment->id) }}</span>
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
                                                    <input class="form-control" name="medicine_name[0]" type="text">
                                                </td>
                                                <td>
                                                    <input class="form-control" name="quantity[0]" type="text" placeholder="e.g. 1 tab">
                                                </td>
                                                <td>
                                                    <input class="form-control" name="days[0]" type="text" placeholder="e.g. 7 days">
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-inline">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" name="morning[0]" type="checkbox"> M
                                                        </label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" name="afternoon[0]" type="checkbox"> A
                                                        </label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" name="evening[0]" type="checkbox"> E
                                                        </label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" name="night[0]" type="checkbox"> N
                                                        </label>
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
                    <div class="form-check form-check-inline">
                        <label class="form-check-label">
                            <input class="form-check-input" name="morning[${rowIdx}]" type="checkbox"> M
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <label class="form-check-label">
                            <input class="form-check-input" name="afternoon[${rowIdx}]" type="checkbox"> A
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <label class="form-check-label">
                            <input class="form-check-input" name="evening[${rowIdx}]" type="checkbox"> E
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <label class="form-check-label">
                            <input class="form-check-input" name="night[${rowIdx}]" type="checkbox"> N
                        </label>
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

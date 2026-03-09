@extends('layouts.admin')

@section('title', 'Patients List - ' . ($siteSettings['site_name'] ?? 'Doccure Admin'))

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">List of Patients</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:(0);">Users</a></li>
                    <li class="breadcrumb-item active">Patients</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="datatable table table-hover table-center mb-0">
                            <thead>
                                <tr>
                                    <th>Patient ID</th>
                                    <th>Patient Name</th>
                                    <th>Age</th>
                                    <th>Address</th>
                                    <th>Phone</th>
                                    <th>Last Visit</th>
                                    <th class="text-end">Paid</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>#PT001</td>
                                    <td>
                                        <h2 class="table-avatar">
                                            <a href="profile.html" class="avatar avatar-sm me-2"><img
                                                    class="avatar-img rounded-circle"
                                                    src="{{ asset('backend/img/patients/patient1.jpg') }}"
                                                    alt="User Image"></a>
                                            <a href="profile.html">Charlene Reed </a>
                                        </h2>
                                    </td>
                                    <td>29</td>
                                    <td>4417 Goosetown Drive, Taylorsville, NC, 28681</td>
                                    <td>8286329170</td>
                                    <td>20 Oct 2019</td>
                                    <td class="text-end">$100.00</td>
                                </tr>
                                <tr>
                                    <td>#PT002</td>
                                    <td>
                                        <h2 class="table-avatar">
                                            <a href="profile.html" class="avatar avatar-sm me-2"><img
                                                    class="avatar-img rounded-circle"
                                                    src="{{ asset('backend/img/patients/patient2.jpg') }}"
                                                    alt="User Image"></a>
                                            <a href="profile.html">Travis Trimble </a>
                                        </h2>
                                    </td>
                                    <td>23</td>
                                    <td>4026 Dodge Street, Prescott, AZ, 86301</td>
                                    <td>9283440479</td>
                                    <td>22 Oct 2019</td>
                                    <td class="text-end">$200.00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
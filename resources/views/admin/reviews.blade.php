@extends('layouts.admin')

@section('title', 'Reviews - ' . ($siteSettings['site_name'] ?? 'Doccure Admin'))

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Reviews</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Reviews</li>
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
                                    <th>Patient Name</th>
                                    <th>Doctor Name</th>
                                    <th>Ratings</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                    class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <h2 class="table-avatar">
                                            <a href="profile.html" class="avatar avatar-sm me-2"><img
                                                    class="avatar-img rounded-circle"
                                                    src="{{ asset('backend/img/patients/patient1.jpg') }}"
                                                    alt="User Image"></a>
                                            <a href="profile.html">Charlene Reed </a>
                                        </h2>
                                    </td>
                                    <td>
                                        <h2 class="table-avatar">
                                            <a href="profile.html" class="avatar avatar-sm me-2"><img
                                                    class="avatar-img rounded-circle"
                                                    src="{{ asset('backend/img/doctors/doctor-thumb-01.jpg') }}"
                                                    alt="User Image"></a>
                                            <a href="profile.html">Dr. Ruby Perrin</a>
                                        </h2>
                                    </td>
                                    <td>
                                        <i class="fe fe-star text-warning"></i>
                                        <i class="fe fe-star text-warning"></i>
                                        <i class="fe fe-star text-warning"></i>
                                        <i class="fe fe-star text-warning"></i>
                                        <i class="fe fe-star-o text-secondary"></i>
                                    </td>
                                    <td>
                                        Lorem ipsum dolor sit amet, consectetur adipiscing elit
                                    </td>
                                    <td>3 Nov 2019 <br><small>09.59 AM</small></td>
                                    <td class="text-end">
                                        <div class="actions">
                                            <a class="btn btn-sm bg-danger-light" data-bs-toggle="modal"
                                                href="#delete_modal">
                                                <i class="fe fe-trash"></i> Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
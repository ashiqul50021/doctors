@extends('layouts.admin')

@section('title', 'Specialities - Doccure Admin')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="row">
        <div class="col-sm-7 col-auto">
            <h3 class="page-title">Specialities</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Specialities</li>
            </ul>
        </div>
        <div class="col-sm-5 col">
            <a href="#Add_Specialities_details" data-bs-toggle="modal" class="btn btn-primary float-end mt-2">Add</a>
        </div>
    </div>
</div>
<!-- /Page Header -->

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="datatable table table-hover table-center mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Specialities</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>#SP001</td>
                                <td>
                                    <h2 class="table-avatar">
                                        <a href="profile.html" class="avatar avatar-sm me-2">
                                            <img class="avatar-img" src="{{ asset('assets/img/specialities/specialities-01.png') }}" alt="Speciality">
                                        </a>
                                        <a href="profile.html">Urology</a>
                                    </h2>
                                </td>
                                <td class="text-end">
                                    <div class="actions">
                                        <a class="btn btn-sm bg-success-light" data-bs-toggle="modal" href="#edit_specialities_details">
                                            <i class="fe fe-pencil"></i> Edit
                                        </a>
                                        <a  data-bs-toggle="modal" href="#delete_modal" class="btn btn-sm bg-danger-light">
                                            <i class="fe fe-trash"></i> Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>#SP002</td>
                                <td>
                                    <h2 class="table-avatar">
                                        <a href="profile.html" class="avatar avatar-sm me-2">
                                            <img class="avatar-img" src="{{ asset('assets/img/specialities/specialities-02.png') }}" alt="Speciality">
                                        </a>
                                        <a href="profile.html">Neurology</a>
                                    </h2>
                                </td>
                                <td class="text-end">
                                    <div class="actions">
                                        <a class="btn btn-sm bg-success-light" data-bs-toggle="modal" href="#edit_specialities_details">
                                            <i class="fe fe-pencil"></i> Edit
                                        </a>
                                        <a  data-bs-toggle="modal" href="#delete_modal" class="btn btn-sm bg-danger-light">
                                            <i class="fe fe-trash"></i> Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>#SP003</td>
                                <td>
                                    <h2 class="table-avatar">
                                        <a href="profile.html" class="avatar avatar-sm me-2">
                                            <img class="avatar-img" src="{{ asset('assets/img/specialities/specialities-03.png') }}" alt="Speciality">
                                        </a>
                                        <a href="profile.html">Orthopedic</a>
                                    </h2>
                                </td>
                                <td class="text-end">
                                    <div class="actions">
                                        <a class="btn btn-sm bg-success-light" data-bs-toggle="modal" href="#edit_specialities_details">
                                            <i class="fe fe-pencil"></i> Edit
                                        </a>
                                        <a  data-bs-toggle="modal" href="#delete_modal" class="btn btn-sm bg-danger-light">
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

@extends('layouts.admin')

@section('title', 'Transactions - ' . ($siteSettings['site_name'] ?? 'Doccure Admin'))

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Transactions</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Transactions</li>
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
                                    <th>Invoice Number</th>
                                    <th>Patient Name</th>
                                    <th>Total Amount</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><a href="invoice.html">#IN0001</a></td>
                                    <td>
                                        <h2 class="table-avatar">
                                            <a href="profile.html" class="avatar avatar-sm me-2"><img
                                                    class="avatar-img rounded-circle"
                                                    src="{{ asset('backend/img/patients/patient1.jpg') }}"
                                                    alt="User Image"></a>
                                            <a href="profile.html">Charlene Reed </a>
                                        </h2>
                                    </td>
                                    <td>$100.00</td>
                                    <td class="text-center">
                                        <span class="badge badge-pill bg-success inv-badge">Paid</span>
                                    </td>
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
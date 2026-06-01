@extends('layouts.admin')

@section('title', 'Manage Agents - ' . ($siteSettings['site_name'] ?? 'Doccure Admin'))

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-7 col-auto">
                <h3 class="page-title">Sales & Booking Agents</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Agents</li>
                </ul>
            </div>
            <div class="col-sm-5 col">
                <a href="{{ route('admin.agents.create') }}" class="btn btn-primary float-end mt-2">Add New Agent</a>
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
                                    <th>Agent Name</th>
                                    <th>Phone</th>
                                    <th>Referral Code</th>
                                    <th>Permissions</th>
                                    <th>Commission Rates</th>
                                    <th>Wallet Balance</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($agents as $agent)
                                    <tr>
                                        <td>
                                            <h2 class="table-avatar">
                                                <span class="avatar avatar-sm me-2">
                                                    <img class="avatar-img rounded-circle" src="{{ asset('assets/img/patients/patient.jpg') }}" alt="User Image">
                                                </span>
                                                <span>
                                                    <strong>{{ $agent->user->name ?? 'N/A' }}</strong><br>
                                                    <small class="text-muted">{{ $agent->user->email ?? 'N/A' }}</small>
                                                </span>
                                            </h2>
                                        </td>
                                        <td>{{ $agent->phone }}</td>
                                        <td><code class="text-primary">{{ $agent->referral_code }}</code></td>
                                        <td>
                                            <span class="badge bg-{{ $agent->can_book_appointments ? 'success' : 'secondary' }} mb-1">
                                                {{ $agent->can_book_appointments ? 'Appointments' : 'No Appointments' }}
                                            </span><br>
                                            <span class="badge bg-{{ $agent->can_sell_products ? 'success' : 'secondary' }} mb-1">
                                                {{ $agent->can_sell_products ? 'Products' : 'No Products' }}
                                            </span><br>
                                            <span class="badge bg-{{ $agent->can_sell_courses ? 'success' : 'secondary' }}">
                                                {{ $agent->can_sell_courses ? 'Courses' : 'No Courses' }}
                                            </span>
                                        </td>
                                        <td>
                                            <small class="d-block">Bookings: ৳{{ number_format($agent->booking_commission_rate, 2) }}</small>
                                            <small class="d-block">Products: {{ $agent->product_commission_rate }}%</small>
                                            <small class="d-block">Courses: {{ $agent->course_commission_rate }}%</small>
                                        </td>
                                        <td><strong>৳{{ number_format($agent->wallet_balance, 2) }}</strong></td>
                                        <td>
                                            @if ($agent->status === 'active')
                                                <span class="badge rounded-pill bg-success-light">Active</span>
                                            @elseif ($agent->status === 'pending')
                                                <span class="badge rounded-pill bg-warning-light">Pending Approval</span>
                                            @else
                                                <span class="badge rounded-pill bg-danger-light">Suspended</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="actions">
                                                <a class="btn btn-sm bg-success-light" href="{{ route('admin.agents.edit', $agent->id) }}">
                                                    <i class="fe fe-pencil"></i> Edit
                                                </a>
                                                <form action="{{ route('admin.agents.destroy', $agent->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this agent?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm bg-danger-light">
                                                        <i class="fe fe-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

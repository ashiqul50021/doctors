@extends('layouts.admin')

@section('title', 'Suppliers - Admin')

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Suppliers</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Suppliers</li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="datatable table table-hover table-center mb-0">
                        <thead>
                            <tr>
                                <th>Company / Contact</th>
                                <th>Phone</th>
                                <th>Trade License</th>
                                <th>Supplies</th>
                                <th>Applied</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($suppliers as $supplier)
                            <tr>
                                <td>
                                    <strong>{{ $supplier->company_name }}</strong><br>
                                    <small class="text-muted">{{ $supplier->user->name ?? 'N/A' }} ({{ $supplier->user->email ?? 'N/A' }})</small>
                                </td>
                                <td>{{ $supplier->phone }}</td>
                                <td>{{ $supplier->trade_license_no ?: '-' }}</td>
                                <td style="max-width: 240px;">{{ \Illuminate\Support\Str::limit($supplier->products_description, 80) ?: '-' }}</td>
                                <td>{{ $supplier->created_at->format('d/m/Y') }}</td>
                                <td>
                                    @if($supplier->status === 'approved')
                                        <span class="badge rounded-pill bg-success-light">Approved</span>
                                    @elseif($supplier->status === 'pending')
                                        <span class="badge rounded-pill bg-warning-light">Pending</span>
                                    @elseif($supplier->status === 'rejected')
                                        <span class="badge rounded-pill bg-danger-light">Rejected</span>
                                    @else
                                        <span class="badge rounded-pill bg-danger-light">Suspended</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <form action="{{ route('admin.suppliers.update-status', $supplier->id) }}" method="POST" class="d-inline-block">
                                        @csrf
                                        @method('PATCH')
                                        @if($supplier->status !== 'approved')
                                            <button type="submit" name="status" value="approved" class="btn btn-sm bg-success-light">Approve</button>
                                        @endif
                                        @if($supplier->status === 'pending')
                                            <button type="submit" name="status" value="rejected" class="btn btn-sm bg-danger-light">Reject</button>
                                        @elseif($supplier->status === 'approved')
                                            <button type="submit" name="status" value="suspended" class="btn btn-sm bg-danger-light">Suspend</button>
                                        @endif
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No suppliers found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

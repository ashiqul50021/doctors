@extends('layouts.admin')

@section('title', 'Sellers Management - Admin')

@section('content')
<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title">Sellers</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Sellers</li>
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
                    <table class="table table-hover table-center mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Store Name</th>
                                <th>Owner Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Products</th>
                                <th>Status</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sellers as $seller)
                            <tr>
                                <td>#{{ $seller->id }}</td>
                                <td>
                                    <strong>{{ $seller->store_name }}</strong>
                                </td>
                                <td>{{ $seller->user->name ?? 'N/A' }}</td>
                                <td>{{ $seller->user->email ?? 'N/A' }}</td>
                                <td>{{ $seller->phone ?? 'N/A' }}</td>
                                <td>{{ $seller->products_count }}</td>
                                <td>
                                    @if($seller->status === 'approved')
                                        <span class="badge badge-pill bg-success-light">Approved</span>
                                    @elseif($seller->status === 'pending')
                                        <span class="badge badge-pill bg-warning-light">Pending</span>
                                    @else
                                        <span class="badge badge-pill bg-danger-light">Suspended</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <form action="{{ route('ecommerce.admin.sellers.update-status', $seller->id) }}" method="POST" class="d-inline-block">
                                        @csrf
                                        @method('PATCH')
                                        @if($seller->status !== 'approved')
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                        @else
                                            <input type="hidden" name="status" value="suspended">
                                            <button type="submit" class="btn btn-sm btn-danger">Suspend</button>
                                        @endif
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">No sellers found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $sellers->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

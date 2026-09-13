@extends('layouts.admin')

@section('title', 'Sellers Management - Admin')

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Sellers</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Sellers</li>
            </ul>
        </div>
        <div class="col-auto">
            <a href="{{ route('ecommerce.admin.sellers.create') }}" class="btn btn-primary">Add New Seller</a>
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
                                <th>Seller / Store Name</th>
                                <th>Phone</th>
                                <th>Commission Rate</th>
                                <th>Products</th>
                                <th>Product Sales</th>
                                <th>Wallet Balance</th>
                                <th>Pending Payout</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sellers as $seller)
                            <tr>
                                <td>
                                    <h2 class="table-avatar">
                                        <span class="avatar avatar-sm me-2 d-inline-block">
                                            <img class="avatar-img rounded-circle"
                                                 src="{{ $seller->store_logo ? asset($seller->store_logo) : asset('assets/img/features/feature-01.jpg') }}"
                                                 alt="{{ $seller->store_name }}"
                                                 style="width: 40px; height: 40px; object-fit: cover; border: 1px solid #e2e8f0;">
                                        </span>
                                        <span>
                                            <strong>{{ $seller->store_name }}</strong><br>
                                            <small class="text-muted">{{ $seller->user->name ?? 'N/A' }} ({{ $seller->user->email ?? 'N/A' }})</small>
                                        </span>
                                    </h2>
                                </td>
                                <td>{{ $seller->phone ?? 'N/A' }}</td>
                                <td><code class="text-primary">{{ $seller->commission_rate }}%</code></td>
                                <td>
                                    <strong>{{ $seller->products_count }}</strong>
                                    <small class="d-block text-muted">items</small>
                                </td>
                                <td>
                                    <strong>৳{{ number_format($seller->total_product_sales ?? 0, 2) }}</strong>
                                    <small class="d-block text-muted">{{ $seller->total_orders_count ?? 0 }} orders</small>
                                </td>
                                <td><strong>৳{{ number_format($seller->wallet_balance ?? 0, 2) }}</strong></td>
                                <td>
                                    <strong class="{{ ($seller->pending_payout_amount ?? 0) > 0 ? 'text-warning' : 'text-muted' }}">
                                        ৳{{ number_format($seller->pending_payout_amount ?? 0, 2) }}
                                    </strong>
                                </td>
                                <td>
                                    @if($seller->status === 'approved')
                                        <span class="badge rounded-pill bg-success-light">Approved</span>
                                    @elseif($seller->status === 'pending')
                                        <span class="badge rounded-pill bg-warning-light">Pending</span>
                                    @else
                                        <span class="badge rounded-pill bg-danger-light">Suspended</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="actions">
                                        <a href="{{ route('ecommerce.admin.sellers.edit', $seller->id) }}" class="btn btn-sm bg-primary-light me-1">
                                            <i class="fe fe-pencil"></i> Edit
                                        </a>
                                        <form action="{{ route('ecommerce.admin.sellers.update-status', $seller->id) }}" method="POST" class="d-inline-block">
                                            @csrf
                                            @method('PATCH')
                                            @if($seller->status !== 'approved')
                                                <input type="hidden" name="status" value="approved">
                                                <button type="submit" class="btn btn-sm bg-success-light">Approve</button>
                                            @else
                                                <input type="hidden" name="status" value="suspended">
                                                <button type="submit" class="btn btn-sm bg-danger-light">Suspend</button>
                                            @endif
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">No sellers found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if ($sellers->count() > 0)
                            <tfoot style="background-color: #F8FAFC; border-top: 2px solid #E2E8F0; font-weight: 700;">
                                <tr>
                                    <td colspan="4" class="text-end" style="color: #475569; font-size: 13px; text-transform: uppercase;">
                                        Total Summary:
                                    </td>
                                    <td style="font-size: 14px;">
                                        <span class="text-success">৳{{ number_format($sellers->sum('total_product_sales'), 2) }}</span>
                                        <small class="d-block text-muted" style="font-size: 11px; font-weight: normal;">{{ $sellers->sum('total_orders_count') }} orders</small>
                                    </td>
                                    <td style="font-size: 14px;">
                                        <span class="text-primary">৳{{ number_format($sellers->sum('wallet_balance'), 2) }}</span>
                                    </td>
                                    <td style="font-size: 14px;">
                                        <span class="text-warning">৳{{ number_format($sellers->sum('pending_payout_amount'), 2) }}</span>
                                    </td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

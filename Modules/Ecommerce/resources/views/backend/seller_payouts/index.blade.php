@extends('layouts.admin')

@section('title', 'Seller Payout Requests')

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Seller Payout Requests</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('ecommerce.admin.products.index') }}">Ecommerce Admin</a></li>
                <li class="breadcrumb-item active">Seller Payouts</li>
            </ul>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="row mb-3">
    <div class="col-md-12">
        <div class="btn-group" role="group">
            <a href="{{ route('ecommerce.admin.seller-payouts.index') }}" class="btn btn-outline-primary {{ !request('status') ? 'active' : '' }}">All</a>
            <a href="{{ route('ecommerce.admin.seller-payouts.index', ['status' => 'pending']) }}" class="btn btn-outline-warning {{ request('status') === 'pending' ? 'active' : '' }}">Pending</a>
            <a href="{{ route('ecommerce.admin.seller-payouts.index', ['status' => 'approved']) }}" class="btn btn-outline-success {{ request('status') === 'approved' ? 'active' : '' }}">Approved / Paid</a>
            <a href="{{ route('ecommerce.admin.seller-payouts.index', ['status' => 'rejected']) }}" class="btn btn-outline-danger {{ request('status') === 'rejected' ? 'active' : '' }}">Rejected</a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">All Payout Requests</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-center mb-0">
                        <thead>
                            <tr>
                                <th>#ID</th>
                                <th>Seller / Store</th>
                                <th>Amount</th>
                                <th>Payment Method</th>
                                <th>Account Details</th>
                                <th>Status</th>
                                <th>Date Requested</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payouts as $payout)
                            <tr>
                                <td>#{{ $payout->id }}</td>
                                <td>
                                    <strong>{{ $payout->sellerProfile->store_name ?? $payout->seller->name ?? 'Unknown Seller' }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $payout->seller->email ?? '' }}</small>
                                </td>
                                <td><strong class="text-success">৳{{ number_format($payout->amount, 2) }}</strong></td>
                                <td><span class="badge badge-secondary text-uppercase">{{ $payout->payment_method }}</span></td>
                                <td><small>{!! nl2br(e($payout->account_details)) !!}</small></td>
                                <td>
                                    @if($payout->status === 'approved')
                                        <span class="badge badge-success">Approved / Paid</span>
                                    @elseif($payout->status === 'rejected')
                                        <span class="badge badge-danger">Rejected</span>
                                    @else
                                        <span class="badge badge-warning">Pending Review</span>
                                    @endif
                                </td>
                                <td>{{ $payout->created_at->format('d M Y, h:i A') }}</td>
                                <td>
                                    @if($payout->status === 'pending')
                                        <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#approveModal{{ $payout->id }}">
                                            Approve
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#rejectModal{{ $payout->id }}">
                                            Reject
                                        </button>

                                        <!-- Approve Modal -->
                                        <div class="modal fade" id="approveModal{{ $payout->id }}" tabindex="-1" role="dialog">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <form action="{{ route('ecommerce.admin.seller-payouts.update-status', $payout->id) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="approved">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Approve Payout Request #{{ $payout->id }}</h5>
                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Are you sure you want to mark this payout of <strong>৳{{ number_format($payout->amount, 2) }}</strong> for <strong>{{ $payout->sellerProfile->store_name ?? 'Seller' }}</strong> as Paid / Approved?</p>
                                                            <div class="form-group mb-3">
                                                                <label class="fw-bold">Transaction Reference / Admin Note (Optional)</label>
                                                                <textarea name="admin_note" class="form-control" rows="2" placeholder="e.g. Bank Trx ID #12345 or bKash Trx ID"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-success">Confirm Approve</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Reject Modal -->
                                        <div class="modal fade" id="rejectModal{{ $payout->id }}" tabindex="-1" role="dialog">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <form action="{{ route('ecommerce.admin.seller-payouts.update-status', $payout->id) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="rejected">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title text-danger">Reject Payout Request #{{ $payout->id }}</h5>
                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Are you sure you want to reject this payout? The amount <strong>৳{{ number_format($payout->amount, 2) }}</strong> will be refunded back to the seller's wallet balance.</p>
                                                            <div class="form-group mb-3">
                                                                <label class="fw-bold">Reason for Rejection (Admin Note)</label>
                                                                <textarea name="admin_note" class="form-control" rows="2" placeholder="Explain why the payout request was rejected" required></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-danger">Confirm Reject</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <small class="text-muted">{{ $payout->processed_at ? $payout->processed_at->format('d M Y') : 'Processed' }}</small>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">No payout requests found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $payouts->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('title', 'Seller Payouts & Wallet')

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Payouts & Wallet Balance</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('ecommerce.seller.dashboard') }}">Seller Dashboard</a></li>
                <li class="breadcrumb-item active">Payouts</li>
            </ul>
        </div>
        <div class="col-auto">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#payoutModal" data-bs-toggle="modal" data-bs-target="#payoutModal" {{ $walletBalance < 500 ? 'disabled' : '' }}>
                <i class="fe fe-plus-circle mr-1"></i> Request Payout
            </button>
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

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row">
    <div class="col-xl-4 col-sm-6 col-12">
        <div class="card">
            <div class="card-body">
                <div class="dash-widget-header">
                    <span class="dash-widget-icon text-success">
                        <i class="fe fe-check-circle"></i>
                    </span>
                    <div class="dash-count">
                        <h3>৳{{ number_format($walletBalance, 2) }}</h3>
                    </div>
                </div>
                <div class="dash-widget-info">
                    <h6 class="text-muted mb-0">Available Balance (Withdrawable)</h6>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-sm-6 col-12">
        <div class="card">
            <div class="card-body">
                <div class="dash-widget-header">
                    <span class="dash-widget-icon text-warning">
                        <i class="fe fe-clock"></i>
                    </span>
                    <div class="dash-count">
                        <h3>৳{{ number_format($pendingEarnings, 2) }}</h3>
                    </div>
                </div>
                <div class="dash-widget-info">
                    <h6 class="text-muted mb-0">Pending Earnings (Undelivered Orders)</h6>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-sm-6 col-12">
        <div class="card">
            <div class="card-body">
                <div class="dash-widget-header">
                    <span class="dash-widget-icon text-primary">
                        <i class="fe fe-credit-card"></i>
                    </span>
                    <div class="dash-count">
                        <h3>৳{{ number_format($totalWithdrawn, 2) }}</h3>
                    </div>
                </div>
                <div class="dash-widget-info">
                    <h6 class="text-muted mb-0">Total Paid Out</h6>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Payout Requests & History</h4>
                @if($walletBalance < 500)
                    <small class="text-muted"><i class="fe fe-alert-circle"></i> Minimum withdrawal limit is ৳500</small>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-center mb-0">
                        <thead>
                            <tr>
                                <th>#ID</th>
                                <th>Requested Amount</th>
                                <th>Payment Method</th>
                                <th>Account Info</th>
                                <th>Status</th>
                                <th>Date Requested</th>
                                <th>Admin Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payouts as $payout)
                            <tr>
                                <td>#{{ $payout->id }}</td>
                                <td><strong class="text-dark">৳{{ number_format($payout->amount, 2) }}</strong></td>
                                <td><span class="badge badge-secondary text-uppercase">{{ $payout->payment_method }}</span></td>
                                <td><small>{{ Str::limit($payout->account_details, 40) }}</small></td>
                                <td>
                                    @if($payout->status === 'approved')
                                        <span class="badge badge-success">Paid / Approved</span>
                                    @elseif($payout->status === 'rejected')
                                        <span class="badge badge-danger">Rejected & Refunded</span>
                                    @else
                                        <span class="badge badge-warning">Pending Review</span>
                                    @endif
                                </td>
                                <td>{{ $payout->created_at->format('d M Y, h:i A') }}</td>
                                <td>{{ $payout->admin_note ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No payout requests found.</td>
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

<!-- Request Payout Modal -->
<div class="modal fade" id="payoutModal" tabindex="-1" role="dialog" aria-labelledby="payoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{ route('ecommerce.seller.payouts.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="payoutModalLabel">Request Payout</h5>
                    <button type="button" class="close btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Available Balance:</strong> ৳{{ number_format($walletBalance, 2) }}
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="amount" class="fw-bold">Withdrawal Amount (৳) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="500" max="{{ $walletBalance }}" name="amount" id="amount" class="form-control" value="{{ old('amount', min(500, $walletBalance)) }}" required>
                        <small class="form-text text-muted">Minimum amount is ৳500.</small>
                    </div>

                    <div class="form-group mb-3">
                        <label for="payment_method" class="fw-bold">Payment Method <span class="text-danger">*</span></label>
                        <select name="payment_method" id="payment_method" class="form-control" required>
                            <option value="bank" {{ old('payment_method') === 'bank' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="bkash" {{ old('payment_method') === 'bkash' ? 'selected' : '' }}>bKash</option>
                            <option value="nagad" {{ old('payment_method') === 'nagad' ? 'selected' : '' }}>Nagad</option>
                            <option value="rocket" {{ old('payment_method') === 'rocket' ? 'selected' : '' }}>Rocket</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="account_details" class="fw-bold">Account Details <span class="text-danger">*</span></label>
                        <textarea name="account_details" id="account_details" rows="3" class="form-control" placeholder="Enter bank name, account number, or mobile banking number" required>{{ old('account_details', $sellerProfile->bank_name ? "Bank: {$sellerProfile->bank_name}\nAccount Name: {$sellerProfile->bank_account_name}\nAccount No: {$sellerProfile->bank_account_number}" : '') }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Submit Payout Request</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

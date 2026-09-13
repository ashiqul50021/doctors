@extends('layouts.admin')

@section('title', 'Seller Payout Requests - ' . ($siteSettings['site_name'] ?? 'abcsheba Admin'))

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Seller Payout Requests</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('ecommerce.admin.sellers.index') }}">Sellers</a></li>
                    <li class="breadcrumb-item active">Payout Requests</li>
                </ul>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Summary Cards (Matching Agent Payouts Exact Layout) -->
    <div class="row mb-4">
        <div class="col-xl-4 col-sm-6 col-12">
            <div class="card bg-white shadow-sm mb-0">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase mb-1" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">Total Payout Requests</h6>
                            <h3 class="mb-0 text-dark font-weight-bold" style="font-size: 22px;">৳{{ number_format($totalRequestedAmount, 2) }}</h3>
                            <small class="text-muted">{{ $allPayouts->count() }} requests</small>
                        </div>
                        <div class="avatar avatar-md rounded-circle bg-primary-light d-flex align-items-center justify-content-center">
                            <i class="fe fe-activity text-primary" style="font-size: 20px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6 col-12">
            <div class="card bg-white shadow-sm mb-0">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase mb-1" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">Pending Payouts</h6>
                            <h3 class="mb-0 text-warning font-weight-bold" style="font-size: 22px;">৳{{ number_format($allPayouts->where('status', 'pending')->sum('amount'), 2) }}</h3>
                            <small class="text-muted">{{ $allPayouts->where('status', 'pending')->count() }} pending</small>
                        </div>
                        <div class="avatar avatar-md rounded-circle bg-warning-light d-flex align-items-center justify-content-center">
                            <i class="fe fe-clock text-warning" style="font-size: 20px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6 col-12">
            <div class="card bg-white shadow-sm mb-0">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase mb-1" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">Approved / Paid</h6>
                            <h3 class="mb-0 text-success font-weight-bold" style="font-size: 22px;">৳{{ number_format($allPayouts->where('status', 'approved')->sum('amount'), 2) }}</h3>
                            <small class="text-muted">{{ $allPayouts->where('status', 'approved')->count() }} approved</small>
                        </div>
                        <div class="avatar avatar-md rounded-circle bg-success-light d-flex align-items-center justify-content-center">
                            <i class="fe fe-check-circle text-success" style="font-size: 20px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card (Matching Agent Payouts Exact Filter Layout) -->
    <div class="card mb-4">
        <div class="card-body py-3">
            <form action="{{ route('ecommerce.admin.seller-payouts.index') }}" method="GET" class="row align-items-end g-3">
                <div class="col-md-4 col-sm-6">
                    <label class="form-label mb-1" style="font-size: 12px; font-weight: 600;">Filter by Seller</label>
                    <select name="seller_id" class="form-control form-select">
                        <option value="">All Sellers</option>
                        @foreach ($sellers as $sel)
                            <option value="{{ $sel->user_id }}" {{ request('seller_id') == $sel->user_id ? 'selected' : '' }}>
                                {{ $sel->store_name }} ({{ $sel->user->name ?? 'User #' . $sel->user_id }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label mb-1" style="font-size: 12px; font-weight: 600;">Status</label>
                    <select name="status" class="form-control form-select">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved / Paid</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-3 col-sm-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary" style="padding: 8px 18px;">
                        <i class="fe fe-filter"></i> Filter
                    </button>
                    @if (request()->hasAny(['seller_id', 'status']))
                        <a href="{{ route('ecommerce.admin.seller-payouts.index') }}" class="btn btn-secondary" style="padding: 8px 16px;">
                            <i class="fe fe-refresh-cw"></i> Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Payout Requests Table matching Agent Payouts table design -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-center mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Seller / Store Name</th>
                                    <th>Email & Phone</th>
                                    <th>Current Wallet Balance</th>
                                    <th>Requested Amount</th>
                                    <th>Payment Details</th>
                                    <th>Description / Txn Info</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($payouts as $payout)
                                    <tr>
                                        <td>{{ $payout->created_at->format('d M Y, h:i A') }}</td>
                                        <td>
                                            <strong>{{ $payout->sellerProfile->store_name ?? 'N/A' }}</strong><br>
                                            <small class="text-muted">{{ $payout->seller->name ?? '' }}</small>
                                        </td>
                                        <td>
                                            <small class="d-block">{{ $payout->seller->email ?? 'N/A' }}</small>
                                            <small class="text-muted">{{ $payout->sellerProfile->phone ?? '' }}</small>
                                        </td>
                                        <td><strong>৳{{ number_format($payout->sellerProfile->wallet_balance ?? 0, 2) }}</strong></td>
                                        <td><strong class="text-danger">৳{{ number_format($payout->amount, 2) }}</strong></td>
                                        <td>
                                            <span class="badge bg-info-light text-uppercase">{{ $payout->payment_method }}</span><br>
                                            <code class="text-dark">{{ $payout->account_details }}</code>
                                        </td>
                                        <td>
                                            <div style="max-width: 260px; font-size: 12.5px; line-height: 1.4;">
                                                <div class="text-secondary mb-1">
                                                    Payout request via {{ $payout->payment_method }} to {{ $payout->account_details }}
                                                </div>

                                                @if (!empty($payout->transaction_number))
                                                    <div class="mt-1">
                                                        <small class="text-muted font-weight-bold">Txn / Ref:</small>
                                                        <code class="text-success font-weight-bold" style="font-size: 12px;">{{ $payout->transaction_number }}</code>
                                                    </div>
                                                @endif

                                                @if ($payout->admin_note)
                                                    <div class="mt-1 p-1 px-2 rounded bg-light border" style="font-size: 11.5px; color: #475569;">
                                                        <strong class="text-primary"><i class="fe fe-file-text"></i> Admin Note:</strong> {{ $payout->admin_note }}
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if ($payout->status === 'pending')
                                                <span class="badge rounded-pill bg-warning-light">Pending</span>
                                            @elseif ($payout->status === 'approved')
                                                <span class="badge rounded-pill bg-success-light">Approved</span>
                                            @else
                                                <span class="badge rounded-pill bg-danger-light">Rejected</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if ($payout->status === 'pending')
                                                <div class="actions">
                                                    <form action="{{ route('ecommerce.admin.seller-payouts.update-status', $payout->id) }}" method="POST" class="d-inline approve-payout-form">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="approved">
                                                        <button type="button" class="btn btn-sm bg-success-light btn-approve-payout">
                                                            <i class="fe fe-check"></i> Approve
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('ecommerce.admin.seller-payouts.update-status', $payout->id) }}" method="POST" class="d-inline reject-payout-form">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="rejected">
                                                        <button type="button" class="btn btn-sm bg-danger-light btn-reject-payout">
                                                            <i class="fe fe-close"></i> Reject
                                                        </button>
                                                    </form>
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4 text-muted">No payout requests found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if ($payouts->count() > 0)
                                <tfoot style="background-color: #F8FAFC; border-top: 2px solid #E2E8F0; font-weight: 700;">
                                    <tr>
                                        <td colspan="3" class="text-end" style="color: #475569; font-size: 13px; text-transform: uppercase;">
                                            Total:
                                        </td>
                                        <td style="font-size: 14px;">
                                            @php
                                                $uniqueSellersBalance = $payouts->pluck('sellerProfile')->filter()->unique('id')->sum('wallet_balance');
                                            @endphp
                                            <span class="text-primary">৳{{ number_format($uniqueSellersBalance, 2) }}</span>
                                            <small class="d-block text-muted" style="font-size: 10px; font-weight: normal;">(Unique Sellers)</small>
                                        </td>
                                        <td style="font-size: 14px;">
                                            <span class="text-danger">৳{{ number_format($payouts->sum('amount'), 2) }}</span>
                                        </td>
                                        <td colspan="4"></td>
                                    </tr>
                                </tfoot>
                            @endif
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

@push('scripts')
<script>
    $(document).ready(function () {
        // SweetAlert for Payout Approval (Identical layout to Agent Payouts modal)
        $(document).on('click', '.btn-approve-payout', function (e) {
            e.preventDefault();
            var form = $(this).closest('form');
            
            Swal.fire({
                title: 'Approve Payout Request',
                html: `
                    <div class="text-start mb-3">
                        <label class="form-label font-weight-bold" style="font-weight: 600; font-size: 14px;">Transaction Number / ID <span class="text-danger">*</span></label>
                        <input id="swal-txn-id" class="form-control" placeholder="Enter Transaction ID or Reference" style="border-radius: 8px; padding: 10px;">
                    </div>
                    <div class="text-start mb-2">
                        <label class="form-label font-weight-bold" style="font-weight: 600; font-size: 14px;">Additional Details / Remarks</label>
                        <textarea id="swal-notes" class="form-control" placeholder="Enter payout details (optional)" rows="3" style="border-radius: 8px; padding: 10px;"></textarea>
                    </div>
                `,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Approve & Save',
                cancelButtonText: 'Cancel',
                preConfirm: () => {
                    const txnId = Swal.getPopup().querySelector('#swal-txn-id').value.trim();
                    const notes = Swal.getPopup().querySelector('#swal-notes').value.trim();
                    if (!txnId) {
                        Swal.showValidationMessage(`Transaction Number is required`);
                        return false;
                    }
                    return { transaction_number: txnId, notes: notes }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.find('input[name="transaction_number"]').remove();
                    form.find('input[name="admin_note"]').remove();
                    
                    form.append('<input type="hidden" name="transaction_number" value="' + result.value.transaction_number + '">');
                    form.append('<input type="hidden" name="admin_note" value="' + result.value.notes + '">');
                    
                    form.submit();
                }
            });
        });

        // SweetAlert for Payout Rejection
        $(document).on('click', '.btn-reject-payout', function (e) {
            e.preventDefault();
            var form = $(this).closest('form');
            
            Swal.fire({
                title: 'Reject Payout Request?',
                text: 'Are you sure you want to reject this payout request? The seller\'s wallet balance will be refunded.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Reject',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush

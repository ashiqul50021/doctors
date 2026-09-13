@extends('layouts.admin')

@section('title', 'Payout Requests - ' . ($siteSettings['site_name'] ?? 'abcsheba Admin'))

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Agent Payout Requests</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.agents.index') }}">Agents</a></li>
                    <li class="breadcrumb-item active">Payout Requests</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-4 col-sm-6 col-12">
            <div class="card bg-white shadow-sm mb-0">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase mb-1" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">Total Payout Requests</h6>
                            <h3 class="mb-0 text-dark font-weight-bold" style="font-size: 22px;">৳{{ number_format($totalRequestedAmount, 2) }}</h3>
                            <small class="text-muted">{{ $payouts->count() }} requests</small>
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
                            <h3 class="mb-0 text-warning font-weight-bold" style="font-size: 22px;">৳{{ number_format($payouts->where('status', 'pending')->sum('amount'), 2) }}</h3>
                            <small class="text-muted">{{ $payouts->where('status', 'pending')->count() }} pending</small>
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
                            <h3 class="mb-0 text-success font-weight-bold" style="font-size: 22px;">৳{{ number_format($payouts->where('status', 'completed')->sum('amount'), 2) }}</h3>
                            <small class="text-muted">{{ $payouts->where('status', 'completed')->count() }} approved</small>
                        </div>
                        <div class="avatar avatar-md rounded-circle bg-success-light d-flex align-items-center justify-content-center">
                            <i class="fe fe-check-circle text-success" style="font-size: 20px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card mb-4">
        <div class="card-body py-3">
            <form action="{{ route('admin.agents.payouts') }}" method="GET" class="row align-items-end g-3">
                <div class="col-md-4 col-sm-6">
                    <label class="form-label mb-1" style="font-size: 12px; font-weight: 600;">Filter by Agent</label>
                    <select name="agent_id" class="form-control form-select">
                        <option value="">All Agents</option>
                        @foreach ($agents as $ag)
                            <option value="{{ $ag->id }}" {{ request('agent_id') == $ag->id ? 'selected' : '' }}>
                                {{ $ag->user->name ?? 'Agent #' . $ag->id }} ({{ $ag->referral_code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label mb-1" style="font-size: 12px; font-weight: 600;">Status</label>
                    <select name="status" class="form-control form-select">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Approved / Paid</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-3 col-sm-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary" style="padding: 8px 18px;">
                        <i class="fe fe-filter"></i> Filter
                    </button>
                    @if (request()->hasAny(['agent_id', 'status']))
                        <a href="{{ route('admin.agents.payouts') }}" class="btn btn-secondary" style="padding: 8px 16px;">
                            <i class="fe fe-refresh-cw"></i> Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-center mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Agent Name</th>
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
                                            <strong>{{ $payout->agent->user->name ?? 'N/A' }}</strong><br>
                                            <code class="text-primary">{{ $payout->agent->referral_code ?? '' }}</code>
                                        </td>
                                        <td>
                                            <small class="d-block">{{ $payout->agent->user->email ?? 'N/A' }}</small>
                                            <small class="text-muted">{{ $payout->agent->phone }}</small>
                                        </td>
                                        <td><strong>৳{{ number_format($payout->agent->wallet_balance, 2) }}</strong></td>
                                        <td><strong class="text-danger">৳{{ number_format($payout->amount, 2) }}</strong></td>
                                        <td>
                                            @php
                                                $paymentDetails = htmlspecialchars($payout->description);
                                                if (preg_match('/Payout request via (.*) to (.*)/i', $payout->description, $matches)) {
                                                    $paymentDetails = '<span class="badge bg-info-light">' . htmlspecialchars($matches[1]) . '</span><br><code class="text-dark">' . htmlspecialchars($matches[2]) . '</code>';
                                                }
                                            @endphp
                                            {!! $paymentDetails !!}
                                        </td>
                                        <td>
                                            @php
                                                // Find related approval log if completed
                                                $approvedLog = null;
                                                if ($payout->status === 'completed') {
                                                    if ($payout->reference_id && isset($approvalLogsMap[$payout->reference_id])) {
                                                        $approvedLog = $approvalLogsMap[$payout->reference_id];
                                                    } elseif (isset($approvalLogsMap[(string)$payout->id])) {
                                                        $approvedLog = $approvalLogsMap[(string)$payout->id];
                                                    }
                                                }

                                                // Check for note inside approved log description
                                                $adminNote = null;
                                                if ($approvedLog && preg_match('/Note:\s*(.*)$/i', $approvedLog->description, $noteMatches)) {
                                                    $adminNote = trim($noteMatches[1]);
                                                }
                                            @endphp

                                            <div style="max-width: 260px; font-size: 12.5px; line-height: 1.4;">
                                                <div class="text-secondary mb-1">
                                                    {{ $payout->description ?? '-' }}
                                                </div>

                                                @if ($payout->reference_id)
                                                    <div class="mt-1">
                                                        <small class="text-muted font-weight-bold">Txn / Ref:</small>
                                                        <code class="text-success font-weight-bold" style="font-size: 12px;">{{ $payout->reference_id }}</code>
                                                    </div>
                                                @endif

                                                @if ($adminNote)
                                                    <div class="mt-1 p-1 px-2 rounded bg-light border" style="font-size: 11.5px; color: #475569;">
                                                        <strong class="text-primary"><i class="fe fe-file-text"></i> Admin Note:</strong> {{ $adminNote }}
                                                    </div>
                                                @elseif ($approvedLog && $approvedLog->description)
                                                    <div class="mt-1 text-muted" style="font-size: 11px;">
                                                        <i class="fe fe-info"></i> {{ $approvedLog->description }}
                                                    </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($payout->status === 'pending')
                                                <span class="badge rounded-pill bg-warning-light">Pending</span>
                                            @elseif ($payout->status === 'completed')
                                                <span class="badge rounded-pill bg-success-light">Approved</span>
                                            @else
                                                <span class="badge rounded-pill bg-danger-light">Rejected</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if ($payout->status === 'pending')
                                                <div class="actions">
                                                    <form action="{{ route('admin.agents.payouts.approve', $payout->id) }}" method="POST" class="d-inline approve-payout-form">
                                                        @csrf
                                                        <button type="button" class="btn btn-sm bg-success-light btn-approve-payout">
                                                            <i class="fe fe-check"></i> Approve
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('admin.agents.payouts.reject', $payout->id) }}" method="POST" class="d-inline reject-payout-form">
                                                        @csrf
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
                                                $uniqueAgentsBalance = $payouts->pluck('agent')->filter()->unique('id')->sum('wallet_balance');
                                            @endphp
                                            <span class="text-primary">৳{{ number_format($uniqueAgentsBalance, 2) }}</span>
                                            <small class="d-block text-muted" style="font-size: 10px; font-weight: normal;">(Unique Agents)</small>
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
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        // SweetAlert for Payout Approval
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
                    // Remove existing dynamically added fields if any
                    form.find('input[name="transaction_number"]').remove();
                    form.find('input[name="notes"]').remove();
                    
                    form.append('<input type="hidden" name="transaction_number" value="' + result.value.transaction_number + '">');
                    form.append('<input type="hidden" name="notes" value="' + result.value.notes + '">');
                    
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
                text: 'Are you sure you want to reject this payout request? The agent\'s wallet balance will be refunded.',
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

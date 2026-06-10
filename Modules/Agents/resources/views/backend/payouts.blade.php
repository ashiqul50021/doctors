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

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="datatable table table-hover table-center mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Agent Name</th>
                                    <th>Email & Phone</th>
                                    <th>Requested Amount</th>
                                    <th>Payment Details</th>
                                    <th>Current Wallet Balance</th>
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
                                        <td><strong>৳{{ number_format($payout->agent->wallet_balance, 2) }}</strong></td>
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
                                        <td colspan="8" class="text-center py-4 text-muted">No payout requests found.</td>
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

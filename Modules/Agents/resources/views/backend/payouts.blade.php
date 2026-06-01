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
                                                    <form action="{{ route('admin.agents.payouts.approve', $payout->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Confirm payout approval? Make sure you have paid the agent via their mobile banking / bank account.');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm bg-success-light">
                                                            <i class="fe fe-check"></i> Approve
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('admin.agents.payouts.reject', $payout->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to reject this payout request?');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm bg-danger-light">
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
                                        <td colspan="7" class="text-center py-4 text-muted">No payout requests found.</td>
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

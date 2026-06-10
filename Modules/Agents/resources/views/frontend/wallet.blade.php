@extends('layouts.app')

@section('title', 'Wallet & Payouts - ' . ($siteSettings['site_name'] ?? 'abcsheba'))

@section('content')
    <!-- Breadcrumb -->
    <div class="breadcrumb-bar">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-12 col-12">
                    <nav aria-label="breadcrumb" class="page-breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('agent.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Wallet</li>
                        </ol>
                    </nav>
                    <h2 class="breadcrumb-title">Wallet & Payouts</h2>
                </div>
            </div>
        </div>
    </div>
    <!-- /Breadcrumb -->

    <!-- Page Content -->
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-md-5 col-lg-4 col-xl-3 theiaStickySidebar">
                    @include('agents::frontend.includes.agent-sidebar')
                </div>

                <div class="col-md-7 col-lg-8 col-xl-9">
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

                    <div class="row">
                        <!-- Balance Card -->
                        <div class="col-md-6 col-sm-12">
                            <div class="card shadow-sm h-100">
                                <div class="card-body d-flex flex-column justify-content-center p-4">
                                    <h6 class="text-muted text-uppercase mb-2" style="font-size: 0.85rem; letter-spacing: 1px;">Available Balance</h6>
                                    <h1 class="text-primary font-weight-bold display-5 mb-3">৳{{ number_format($agent->wallet_balance, 2) }}</h1>
                                    <p class="text-muted small mb-0"><i class="fas fa-info-circle"></i> Minimum withdrawal limit is <strong>৳500.00</strong>. Payouts are processed within 24-48 hours.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Payout Request Form -->
                        <div class="col-md-6 col-sm-12">
                            <div class="card shadow-sm h-100">
                                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                                    <h5 class="card-title font-weight-bold mb-0">Request Cashout</h5>
                                </div>
                                <div class="card-body p-4">
                                    <form action="{{ route('agent.payout.request') }}" method="POST">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label small font-weight-bold text-muted">Amount (৳)</label>
                                                <input type="number" name="amount" class="form-control" min="500" step="0.01" required placeholder="Min 500">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label small font-weight-bold text-muted">Method</label>
                                                <select name="payment_method" class="form-select" required>
                                                    <option value="bKash">bKash (Personal)</option>
                                                    <option value="Nagad">Nagad (Personal)</option>
                                                    <option value="Rocket">Rocket (Personal)</option>
                                                    <option value="Bank Transfer">Bank Account</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label small font-weight-bold text-muted">Account Number</label>
                                                <input type="text" name="account_number" class="form-control" required placeholder="e.g. 01712345678">
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100 mt-2 py-2 font-weight-bold" {{ $agent->wallet_balance < 500 ? 'disabled' : '' }}>
                                            Submit Cashout Request
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ledger / Transaction Log -->
                    <div class="card mt-4 shadow-sm">
                        <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                            <h4 class="card-title font-weight-bold mb-0">Detailed Transaction Ledger</h4>
                            <p class="text-muted small mb-0">A complete statement of your commissions earned and cashouts requested.</p>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <div class="table-responsive">
                                <table class="table table-hover table-center mb-0">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Transaction Type</th>
                                            <th>Reference ID</th>
                                            <th>Description</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($transactions as $tx)
                                            <tr>
                                                <td>{{ $tx->created_at->format('d M Y, h:i A') }}</td>
                                                <td>
                                                    <span class="badge bg-{{ in_array($tx->type, ['commission_booking', 'commission_product', 'commission_course']) ? 'success' : 'danger' }}">
                                                        {{ str_replace('_', ' ', ucfirst($tx->type)) }}
                                                    </span>
                                                </td>
                                                <td>{{ $tx->reference_id ?? '-' }}</td>
                                                <td>
                                                    @if (in_array($tx->type, ['payout_request', 'payout_approved', 'payout_rejected']))
                                                        @php
                                                            $desc = htmlspecialchars($tx->description);
                                                            if (preg_match('/Payout request via (.*) to (.*)/i', $tx->description, $matches)) {
                                                                $desc = '<span class="badge bg-info-light">' . htmlspecialchars($matches[1]) . '</span> to <code class="text-dark">' . htmlspecialchars($matches[2]) . '</code>';
                                                            }
                                                        @endphp
                                                        {!! $desc !!}
                                                    @else
                                                        {{ $tx->description }}
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="font-weight-bold text-{{ in_array($tx->type, ['commission_booking', 'commission_product', 'commission_course']) ? 'success' : 'danger' }}">
                                                        {{ in_array($tx->type, ['commission_booking', 'commission_product', 'commission_course']) ? '+' : '-' }}৳{{ number_format($tx->amount, 2) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge rounded-pill bg-{{ $tx->status === 'completed' ? 'success' : ($tx->status === 'pending' ? 'warning' : 'danger') }}-light">
                                                        {{ ucfirst($tx->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4 text-muted">No transactions found in ledger.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            <div class="mt-4">
                                {{ $transactions->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Page Content -->
@endsection

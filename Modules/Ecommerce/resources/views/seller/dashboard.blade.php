@extends('layouts.admin')

@section('title', 'Seller Dashboard')

@section('content')
<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title">Welcome, {{ auth()->user()->sellerProfile->store_name ?? auth()->user()->name }}!</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item active">Seller Dashboard</li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-3 col-sm-6 col-12">
        <div class="card">
            <div class="card-body">
                <div class="dash-widget-header">
                    <span class="dash-widget-icon text-primary border-primary">
                        <i class="fe fe-shopping-bag"></i>
                    </span>
                    <div class="dash-count">
                        <h3>{{ $totalProducts }}</h3>
                    </div>
                </div>
                <div class="dash-widget-info">
                    <h6 class="text-muted">Total Products</h6>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 col-12">
        <div class="card">
            <div class="card-body">
                <div class="dash-widget-header">
                    <span class="dash-widget-icon text-success border-success">
                        <i class="fe fe-star"></i>
                    </span>
                    <div class="dash-count">
                        <h3>{{ $totalItemsSold }}</h3>
                    </div>
                </div>
                <div class="dash-widget-info">
                    <h6 class="text-muted">Items Sold</h6>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 col-12">
        <div class="card">
            <div class="card-body">
                <div class="dash-widget-header">
                    <span class="dash-widget-icon text-info border-info">
                        <i class="fe fe-money"></i>
                    </span>
                    <div class="dash-count">
                        <h3>৳{{ number_format($totalEarnings, 2) }}</h3>
                    </div>
                </div>
                <div class="dash-widget-info">
                    <h6 class="text-muted">Total Sales</h6>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 col-12">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="dash-widget-header">
                    <span class="dash-widget-icon text-white border-white">
                        <i class="fe fe-credit-card"></i>
                    </span>
                    <div class="dash-count">
                        <h3 class="text-white">৳{{ number_format($walletBalance, 2) }}</h3>
                    </div>
                </div>
                <div class="dash-widget-info d-flex justify-content-between align-items-center mt-2">
                    <span class="text-white-50">Available Wallet Balance</span>
                    <a href="{{ route('ecommerce.seller.payouts.index') }}" class="btn btn-sm btn-light font-weight-bold text-success">
                        Withdraw
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Recent Sold Items</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-center mb-0">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentItems as $item)
                            <tr>
                                <td>#{{ $item->order_id }}</td>
                                <td>{{ $item->product->name ?? 'Deleted Product' }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>৳{{ number_format($item->total, 2) }}</td>
                                <td>{{ $item->created_at->format('d M Y, h:i A') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No sales yet.</td>
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

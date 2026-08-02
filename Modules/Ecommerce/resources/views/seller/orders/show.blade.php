@extends('layouts.admin')

@section('title', 'Order Details')

@section('content')
<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title">Order Details</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('ecommerce.seller.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('ecommerce.seller.orders.index') }}">Orders</a></li>
                <li class="breadcrumb-item active">#{{ $order->order_number }}</li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">My Sold Items</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-center table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <h2 class="table-avatar">
                                            @php
                                                $productImage = $item->product->image ?? null;
                                            @endphp
                                            <a href="#" class="avatar avatar-sm me-2">
                                                <img class="avatar-img rounded"
                                                    src="{{ $productImage ? asset($productImage) : asset('assets/img/products/product.jpg') }}"
                                                    alt="Product Image">
                                            </a>
                                            <div>
                                                <a href="#">{{ $item->product->name ?? 'Deleted Product' }}</a>
                                                @if($item->display_variant_label)
                                                    <div class="text-muted small">{{ $item->display_variant_label }}</div>
                                                @endif
                                            </div>
                                        </h2>
                                    </td>
                                    <td>৳{{ number_format($item->price, 2) }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>৳{{ number_format($item->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4 class="card-title">General Status</h4>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <h5 class="mb-0 me-3">Order Status:</h5>
                    @if($order->status == 'pending')
                        <span class="badge bg-warning-light fs-6">Pending</span>
                    @elseif($order->status == 'processing')
                        <span class="badge bg-info-light fs-6">Processing</span>
                    @elseif($order->status == 'shipped')
                        <span class="badge bg-primary-light fs-6">Shipped</span>
                    @elseif($order->status == 'delivered')
                        <span class="badge bg-success-light fs-6">Delivered</span>
                    @elseif($order->status == 'cancelled')
                        <span class="badge bg-danger-light fs-6">Cancelled</span>
                    @else
                        <span class="badge bg-secondary-light fs-6">{{ ucfirst($order->status) }}</span>
                    @endif
                </div>
                <small class="text-muted d-block mt-2">
                    * Status updates are handled by site administrators. Once marked as "Delivered", earnings will credit to your wallet automatically.
                </small>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Earnings Breakdown</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <td>Gross Sales</td>
                                <td class="text-end">৳{{ number_format($sellerSubtotal, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Admin Commission ({{ $commissionRate }}%)</td>
                                <td class="text-end text-danger">-৳{{ number_format($commissionAmount, 2) }}</td>
                            </tr>
                            <tr class="border-top">
                                <td><strong>Net Earnings</strong></td>
                                <td class="text-end text-success"><strong>৳{{ number_format($netEarnings, 2) }}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Shipping Info</h4>
            </div>
            <div class="card-body">
                <h5>{{ $order->customer_name }}</h5>
                <p class="text-muted mb-1">{{ $order->customer_phone }}</p>
                <p class="text-muted mb-1">{{ $order->customer_email }}</p>
                <hr>
                <p class="mb-0">{{ $order->shipping_address }}</p>
                @if($order->notes)
                    <div class="mt-3">
                        <h6><strong>Notes:</strong></h6>
                        <p class="text-muted small">{{ $order->notes }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

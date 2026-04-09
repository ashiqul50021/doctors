@extends('layouts.app')

@section('title', 'Order Successful - abcsheba.com')

@section('content')

<!-- Page Content -->
<div class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div class="success-icon mb-4">
                            <i class="fas fa-check-circle fa-5x text-success"></i>
                        </div>
                        <h2 class="mb-3">Thank You for Your Order!</h2>
                        <p class="text-muted mb-4">Your order has been placed successfully. We'll send you a confirmation email shortly.</p>

                        <div class="order-number mb-4">
                            <h4>Order #{{ $order->id }}</h4>
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Order Details</h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Customer Information</h6>
                                <p class="mb-1"><strong>Name:</strong> {{ $order->customer_name }}</p>
                                <p class="mb-1"><strong>Email:</strong> {{ $order->customer_email }}</p>
                                <p class="mb-1"><strong>Phone:</strong> {{ $order->customer_phone }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Shipping Address</h6>
                                <p class="mb-0">{{ $order->shipping_address }}</p>
                            </div>
                        </div>

                        <h6 class="text-muted mb-3">Order Items</h6>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($item->product && $item->product->image)
                                                    <img src="{{ asset('storage/'.$item->product->image) }}" alt="{{ $item->product->name ?? 'Product' }}" class="me-3" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                                @endif
                                                <div>
                                                    <div>{{ $item->product->name ?? 'Product' }}</div>
                                                    @if($item->display_variant_label)
                                                        <small class="text-muted">{{ $item->display_variant_label }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>৳{{ number_format($item->price, 2) }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td class="text-end">৳{{ number_format($item->price * $item->quantity, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Total</strong></td>
                                        <td class="text-end"><strong class="text-primary">৳{{ number_format($order->total, 2) }}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="mt-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-warning text-dark">{{ ucfirst($order->status) }}</span>
                                    <small class="text-muted ms-2">Payment: Cash on Delivery</small>
                                </div>
                                <div>
                                    <a href="{{ route('ecommerce.products') }}" class="btn btn-primary">
                                        <i class="fas fa-shopping-bag me-2"></i>Continue Shopping
                                    </a>
                                    <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-home me-2"></i>Back to Home
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Page Content -->
@endsection

@push('styles')
<style>
    .success-icon {
        animation: scaleIn 0.5s ease-out;
    }
    @keyframes scaleIn {
        0% { transform: scale(0); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }
</style>
@endpush

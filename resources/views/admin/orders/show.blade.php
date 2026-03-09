@extends('layouts.admin')

@section('title', 'Order Details - Doccure Admin')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Order Details</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Orders</a></li>
                    <li class="breadcrumb-item active">#{{ $order->order_number }}</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Order Items</h4>
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
                                                <a href="#" class="avatar avatar-sm me-2">
                                                    <img class="avatar-img rounded"
                                                        src="{{ $item->product->image ? asset($item->product->image) : asset('assets/img/products/product.jpg') }}"
                                                        alt="Product Image">
                                                </a>
                                                <a href="#">{{ $item->product->name }}</a>
                                            </h2>
                                        </td>
                                        <td>৳{{ number_format($item->price, 2) }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>৳{{ number_format($item->price * $item->quantity, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Order Status</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.orders.status', $order->id) }}" method="POST">
                        @csrf
                        <div class="row align-items-end">
                            <div class="col-md-6">
                                <label class="form-label">Update Status</label>
                                <select name="status" class="form-select">
                                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending
                                    </option>
                                    <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>
                                        Processing</option>
                                    <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed
                                    </option>
                                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary">Update Status</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Order Summary</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <td>Subtotal</td>
                                    <td class="text-end">৳{{ number_format($order->subtotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Shipping</td>
                                    <td class="text-end">৳{{ number_format($order->shipping, 2) }}</td>
                                </tr>
                                @if($order->discount > 0)
                                    <tr>
                                        <td>
                                            Discount
                                            @if($order->coupon_code)
                                                <small class="text-muted">({{ $order->coupon_code }})</small>
                                            @endif
                                        </td>
                                        <td class="text-end text-danger">-৳{{ number_format($order->discount, 2) }}</td>
                                    </tr>
                                @endif
                                <tr class="border-top">
                                    <td><strong>Total</strong></td>
                                    <td class="text-end"><strong>৳{{ number_format($order->total, 2) }}</strong></td>
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
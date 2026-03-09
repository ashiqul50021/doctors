@extends('layouts.app')

@section('title', 'Shopping Cart - abcsheba.com')

@section('content')

<!-- Page Content -->
<div class="content">
    <div class="container-fluid">

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(count($cart) > 0)
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Cart Items ({{ count($cart) }})</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Subtotal</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cart as $productId => $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $item['image'] ? asset('storage/'.$item['image']) : asset('assets/img/products/product-1.jpg') }}" alt="{{ $item['name'] }}" class="me-3" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                                                <div>
                                                    <h6 class="mb-0">{{ $item['name'] }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>৳{{ number_format($item['price'], 2) }}</td>
                                        <td>
                                            <form action="{{ route('cart.update') }}" method="POST" class="d-flex align-items-center">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $productId }}">
                                                <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" class="form-control" style="width: 80px;">
                                                <button type="submit" class="btn btn-sm btn-outline-primary ms-2">
                                                    <i class="fas fa-sync-alt"></i>
                                                </button>
                                            </form>
                                        </td>
                                        <td class="fw-bold">৳{{ number_format($item['price'] * $item['quantity'], 2) }}</td>
                                        <td>
                                            <form action="{{ route('cart.remove') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $productId }}">
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Order Summary</h4>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <span>Subtotal</span>
                            <span class="fw-bold">৳{{ number_format($total, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Shipping</span>
                            <span class="text-success">Free</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="h5">Total</span>
                            <span class="h5 text-primary fw-bold">৳{{ number_format($total, 2) }}</span>
                        </div>
                        <a href="{{ route('ecommerce.checkout') }}" class="btn btn-primary w-100 btn-lg">
                            <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                        </a>
                        <a href="{{ route('ecommerce.products') }}" class="btn btn-outline-secondary w-100 mt-3">
                            <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
                <h3>Your cart is empty</h3>
                <p class="text-muted mb-4">Looks like you haven't added any products to your cart yet.</p>
                <a href="{{ route('ecommerce.products') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-shopping-bag me-2"></i>Browse Products
                </a>
            </div>
        </div>
        @endif

    </div>
</div>
<!-- /Page Content -->
@endsection

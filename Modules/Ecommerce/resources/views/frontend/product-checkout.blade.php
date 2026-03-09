@extends('layouts.app')

@section('title', 'Checkout - abcsheba.com')

@section('content')

<!-- Page Content -->
<div class="content">
    <div class="container-fluid">

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('order.place') }}" method="POST">
            @csrf
            <div class="row">
                <!-- Billing Details -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Shipping Information</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Shipping Address <span class="text-danger">*</span></label>
                                        <textarea name="address" class="form-control" rows="3" required>{{ old('address') }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Order Notes (Optional)</label>
                                        <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-4">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Payment Method</h4>
                        </div>
                        <div class="card-body">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" checked>
                                <label class="form-check-label" for="cod">
                                    <strong>Cash on Delivery</strong>
                                    <p class="text-muted mb-0 small">Pay when you receive your order</p>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Billing Details -->

                <!-- Order Summary -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Order Summary</h4>
                        </div>
                        <div class="card-body">
                            @foreach($cart as $productId => $item)
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $item['image'] ? asset('storage/'.$item['image']) : asset('assets/img/products/product-1.jpg') }}" alt="{{ $item['name'] }}" class="me-2" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                    <div>
                                        <small class="fw-bold">{{ Str::limit($item['name'], 20) }}</small>
                                        <br>
                                        <small class="text-muted">x{{ $item['quantity'] }}</small>
                                    </div>
                                </div>
                                <span>৳{{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                            </div>
                            @endforeach

                            <hr>

                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal</span>
                                <span>৳{{ number_format($total, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Shipping</span>
                                <span class="text-success">Free</span>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between mb-4">
                                <span class="h5 mb-0">Total</span>
                                <span class="h5 mb-0 text-primary fw-bold">৳{{ number_format($total, 2) }}</span>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 btn-lg">
                                <i class="fas fa-check-circle me-2"></i>Place Order
                            </button>
                            <a href="{{ route('cart') }}" class="btn btn-outline-secondary w-100 mt-3">
                                <i class="fas fa-arrow-left me-2"></i>Back to Cart
                            </a>
                        </div>
                    </div>
                </div>
                <!-- /Order Summary -->
            </div>
        </form>

    </div>
</div>
<!-- /Page Content -->
@endsection

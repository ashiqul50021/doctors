@extends('layouts.app')

@section('title', 'Checkout - abcsheba.com')

@section('content')

<div class="content checkout-page-modern">
    <div class="container">

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('ecommerce.order.place') }}" method="POST">
            @csrf
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="checkout-card">
                        <div class="checkout-card-header">
                            <h4 class="card-title mb-0">Shipping Information</h4>
                        </div>
                        <div class="checkout-card-body">
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

                    <div class="checkout-card mt-4">
                        <div class="checkout-card-header">
                            <h4 class="card-title mb-0">Payment Method</h4>
                        </div>
                        <div class="checkout-card-body">
                            <div class="payment-option-box">
                                <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" checked>
                                <label class="form-check-label" for="cod">
                                    <strong>Cash on Delivery</strong>
                                    <p class="text-muted mb-0 small">Pay when you receive your order</p>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="checkout-card checkout-summary-card">
                        <div class="checkout-card-header">
                            <h4 class="card-title mb-0">Order Summary</h4>
                        </div>
                        <div class="checkout-card-body">
                            @foreach($cart as $productId => $item)
                                @php
                                    $imagePath = $item['image'] ?? null;
                                    $imageUrl = $imagePath
                                        ? (\Illuminate\Support\Str::startsWith($imagePath, ['http://', 'https://']) ? $imagePath : asset($imagePath))
                                        : asset('assets/img/products/product-1.jpg');
                                @endphp
                                <div class="checkout-product-box">
                                    <div class="checkout-product-info">
                                        <img src="{{ $imageUrl }}"
                                            alt="{{ $item['name'] }}"
                                            class="checkout-product-image"
                                            onerror="this.onerror=null;this.src='{{ asset('assets/img/products/product-1.jpg') }}';">
                                        <div>
                                            <h6>{{ Str::limit($item['name'], 24) }}</h6>
                                            <span>x{{ $item['quantity'] }}</span>
                                        </div>
                                    </div>
                                    <strong>৳{{ number_format($item['price'] * $item['quantity'], 2) }}</strong>
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
                            <a href="{{ route('ecommerce.cart') }}" class="btn btn-outline-secondary w-100 mt-3">
                                <i class="fas fa-arrow-left me-2"></i>Back to Cart
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>
</div>
@endsection

@push('styles')
<style>
    .checkout-page-modern {
        background: linear-gradient(180deg, #f7fbff 0%, #ffffff 45%, #f8fbff 100%);
        padding: 28px 0 70px;
    }

    .checkout-card {
        background: #fff;
        border: 1px solid #e8eef8;
        border-radius: 24px;
        box-shadow: 0 20px 45px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }

    .checkout-card-header {
        padding: 22px 24px;
        border-bottom: 1px solid #edf2f7;
    }

    .checkout-card-header h4 {
        font-size: 28px;
        font-weight: 700;
        color: #0f172a;
    }

    .checkout-card-body {
        padding: 24px;
    }

    .checkout-card .form-label {
        color: #0f172a;
        font-weight: 600;
        font-size: 16px;
        margin-bottom: 10px;
    }

    .checkout-card .form-control {
        border: 1px solid #dbe5f3;
        border-radius: 14px;
        min-height: 54px;
        box-shadow: none;
        padding: 12px 16px;
    }

    .checkout-card textarea.form-control {
        min-height: 84px;
    }

    .checkout-card .form-control:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.10);
    }

    .payment-option-box {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 18px 18px 16px;
        border: 1px solid #dbe5f3;
        border-radius: 18px;
        background: #f8fbff;
    }

    .payment-option-box .form-check-input {
        margin-top: 6px;
    }

    .payment-option-box .form-check-label strong {
        display: block;
        color: #0f172a;
        font-size: 20px;
        margin-bottom: 4px;
    }

    .checkout-product-box {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 14px 16px;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        background: #f8fbff;
        margin-bottom: 14px;
    }

    .checkout-product-info {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }

    .checkout-product-image {
        width: 52px;
        height: 52px;
        object-fit: cover;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        background: #fff;
        flex-shrink: 0;
    }

    .checkout-product-info h6 {
        margin: 0 0 4px;
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.3;
    }

    .checkout-product-info span {
        color: #64748b;
        font-size: 15px;
        font-weight: 600;
    }

    .checkout-product-box strong {
        white-space: nowrap;
        color: #0f172a;
        font-size: 20px;
        font-weight: 800;
    }

    @media (max-width: 991.98px) {
        .checkout-card-header h4 {
            font-size: 24px;
        }
    }

    @media (max-width: 767.98px) {
        .checkout-page-modern {
            padding: 18px 0 50px;
        }

        .checkout-card-body,
        .checkout-card-header {
            padding-left: 16px;
            padding-right: 16px;
        }

        .checkout-product-box {
            padding: 12px 14px;
        }

        .checkout-product-info h6 {
            font-size: 16px;
        }

        .checkout-product-box strong {
            font-size: 18px;
        }
    }
</style>
@endpush

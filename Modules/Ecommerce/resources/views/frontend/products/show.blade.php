@extends('layouts.app')

@section('title', $product->name . ' - abcsheba.com')

@section('content')

<!-- Page Content -->
<div class="content">
    <div class="container-fluid">

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <!-- Product Image -->
                    <div class="col-md-5">
                        <div class="product-image-main">
                            <img src="{{ $product->image ? asset('storage/'.$product->image) : asset('assets/img/products/product-1.jpg') }}" class="img-fluid rounded" alt="{{ $product->name }}" style="width: 100%; max-height: 400px; object-fit: cover;">
                        </div>
                        @if($product->gallery && count($product->gallery) > 0)
                        <div class="product-gallery mt-3">
                            <div class="row">
                                @foreach($product->gallery as $image)
                                <div class="col-3">
                                    <img src="{{ asset('storage/'.$image) }}" class="img-fluid rounded" alt="Gallery" style="height: 80px; object-fit: cover; cursor: pointer;">
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                    <!-- /Product Image -->

                    <!-- Product Details -->
                    <div class="col-md-7">
                        <div class="product-details">
                            <span class="badge bg-primary mb-3">{{ $product->category->name ?? 'General' }}</span>
                            <h2 class="product-title">{{ $product->name }}</h2>

                            <div class="product-price mb-4">
                                @if($product->sale_price)
                                    <span class="h3 text-muted text-decoration-line-through me-2">৳{{ number_format($product->price, 2) }}</span>
                                    <span class="h2 text-primary fw-bold">৳{{ number_format($product->sale_price, 2) }}</span>
                                    <span class="badge bg-danger ms-2">{{ $product->discount_percentage }}% OFF</span>
                                @else
                                    <span class="h2 text-primary fw-bold">৳{{ number_format($product->price, 2) }}</span>
                                @endif
                            </div>

                            <div class="product-description mb-4">
                                <h5>Description</h5>
                                <p>{!! nl2br(e($product->description)) !!}</p>
                            </div>

                            @if($product->stock_quantity > 0)
                                <p class="text-success"><i class="fas fa-check-circle me-2"></i>In Stock ({{ $product->stock_quantity }} available)</p>
                            @else
                                <p class="text-danger"><i class="fas fa-times-circle me-2"></i>Out of Stock</p>
                            @endif

                            <form action="{{ route('cart.add') }}" method="POST" class="mt-4">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <div class="row align-items-center">
                                    <div class="col-md-3">
                                        <label class="form-label">Quantity</label>
                                        <input type="number" name="quantity" class="form-control" value="1" min="1" max="{{ $product->stock_quantity ?? 99 }}">
                                    </div>
                                    <div class="col-md-9">
                                        <button type="submit" class="btn btn-primary btn-lg mt-4" {{ ($product->stock_quantity ?? 0) < 1 ? 'disabled' : '' }}>
                                            <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                                        </button>
                                        <a href="{{ route('cart') }}" class="btn btn-outline-primary btn-lg mt-4">
                                            <i class="fas fa-eye me-2"></i>View Cart
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- /Product Details -->
                </div>
            </div>
        </div>

        <!-- Related Products -->
        @if($relatedProducts->count() > 0)
        <div class="card mt-4">
            <div class="card-header">
                <h4 class="card-title mb-0">Related Products</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($relatedProducts as $relProduct)
                    <div class="col-md-6 col-lg-3">
                        <div class="card product-card h-100">
                            <a href="{{ route('products.show', $relProduct->id) }}">
                                <img src="{{ $relProduct->image ? asset('storage/'.$relProduct->image) : asset('assets/img/products/product-1.jpg') }}" class="card-img-top" alt="{{ $relProduct->name }}" style="height: 150px; object-fit: cover;">
                            </a>
                            <div class="card-body">
                                <h6 class="card-title">
                                    <a href="{{ route('products.show', $relProduct->id) }}">{{ $relProduct->name }}</a>
                                </h6>
                                <div class="product-price">
                                    @if($relProduct->sale_price)
                                        <span class="text-primary fw-bold">৳{{ number_format($relProduct->sale_price, 2) }}</span>
                                    @else
                                        <span class="text-primary fw-bold">৳{{ number_format($relProduct->price, 2) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
        <!-- /Related Products -->

    </div>
</div>
<!-- /Page Content -->
@endsection

@push('styles')
<style>
    .product-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }
    .product-card .card-title a {
        color: #272b41;
        text-decoration: none;
    }
    .product-card .card-title a:hover {
        color: #09e5ab;
    }
</style>
@endpush

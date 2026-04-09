@extends('layouts.app')

@section('title', 'Products - abcsheba.com')

@section('content')
<div class="content product-page-wrap">
    <div class="container">
        <div class="product-page-head mb-4">
            <div>
                <p class="page-kicker mb-1">Online Pharmacy</p>
                <h2 class="mb-0">Find the right health products faster</h2>
            </div>
            <div class="head-count">
                <span>{{ $products->total() }}</span> items found
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-12 col-lg-4 col-xl-3">
                <div class="filter-panel card border-0">
                    <div class="card-header border-0">
                        <h4 class="mb-0">Filter Products</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('ecommerce.products') }}" method="GET">
                            <div class="filter-widget mb-4">
                                <label class="filter-label">Search</label>
                                <input type="text" name="search" class="form-control"
                                    placeholder="Type product name..." value="{{ request('search') }}">
                            </div>

                            <div class="filter-widget mb-4">
                                <label class="filter-label d-block">Categories</label>
                                <div class="category-list">
                                    <label class="category-item">
                                        <input type="radio" name="category" value=""
                                            {{ request('category') ? '' : 'checked' }}>
                                        <span>All Categories</span>
                                    </label>
                                    @foreach($categories as $category)
                                        <label class="category-item">
                                            <input type="radio" name="category" value="{{ $category->id }}"
                                                {{ (string) request('category') === (string) $category->id ? 'checked' : '' }}>
                                            <span>{{ $category->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-filter">Apply Filter</button>
                                <a href="{{ route('ecommerce.products') }}" class="btn btn-clear">Reset</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-12 col-lg-8 col-xl-9">
                @if(session('success'))
                    <div class="alert alert-success shadow-sm border-0">{{ session('success') }}</div>
                @endif

                @if(request('search') || request('category'))
                    <div class="active-filters mb-3">
                        @if(request('search'))
                            <span class="chip">Search: {{ request('search') }}</span>
                        @endif
                        @if(request('category'))
                            <span class="chip">Category:
                                {{ optional($categories->firstWhere('id', (int) request('category')))->name ?? 'Selected' }}
                            </span>
                        @endif
                    </div>
                @endif

                <div class="row">
                    @forelse($products as $product)
                        <div class="col-lg-4 col-md-6 col-sm-6 mb-4 product-grid-item">
                            <div class="product-card-modern">
                                <div class="stock-badge {{ $product->stock > 0 ? 'in-stock' : 'out-of-stock' }}">
                                    {{ $product->stock > 0 ? 'IN STOCK' : 'OUT OF STOCK' }}
                                </div>

                                <div class="product-image-container">
                                    <a href="{{ route('ecommerce.products.show', $product->id) }}" class="product-image-link">
                                        @php
                                            $image = $product->image;
                                            if (!$image && !empty($product->gallery) && is_array($product->gallery)) {
                                                $image = $product->gallery[0] ?? null;
                                            }
                                        @endphp
                                        <img src="{{ $image ? asset($image) : asset('assets/img/products/default-product.png') }}"
                                            class="product-main-img" alt="{{ $product->name }}">
                                    </a>
                                </div>

                                <div class="product-details">
                                    <div class="product-rating">
                                        <i class="fas fa-star"></i>
                                        <span class="rating-value">{{ number_format($product->rating ?? 4.5, 1) }}</span>
                                        <span class="review-count">({{ $product->reviews_count ?? rand(10, 200) }})</span>
                                    </div>

                                    <div class="product-brand">{{ $product->category->name ?? 'Medicine' }}</div>

                                    <h4 class="product-name">
                                        <a href="{{ route('ecommerce.products.show', $product->id) }}">{{ $product->name }}</a>
                                    </h4>

                                    <div class="product-footer">
                                        <div class="product-price-tag">
                                            @if($product->sale_price)
                                                <span class="price-current">৳{{ number_format($product->sale_price, 0) }}</span>
                                                <span class="price-original">৳{{ number_format($product->price, 0) }}</span>
                                            @else
                                                <span class="price-current">৳{{ number_format($product->price, 0) }}</span>
                                            @endif
                                        </div>
                                        <form action="{{ route('ecommerce.cart.add') }}" method="POST" class="product-actions-form">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <input type="hidden" name="quantity" value="1">
                                            <div class="btn-group-modern">
                                                <button type="submit" class="btn-cart-modern" title="Add to Cart">
                                                    <i class="fas fa-shopping-cart"></i>
                                                </button>
                                                <button type="submit" name="buy_now" value="1" class="btn-buy-modern">
                                                    Buy
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="empty-state text-center">
                                <h4 class="mb-2">No products found</h4>
                                <p class="mb-3">Try another keyword or clear your filters.</p>
                                <a href="{{ route('ecommerce.products') }}" class="btn btn-filter">View All Products</a>
                            </div>
                        </div>
                    @endforelse
                </div>

                <div class="load-more text-center mt-3">
                    {{ $products->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .product-page-wrap {
        background: radial-gradient(circle at top right, #eaf4ff 0%, #f7fbff 45%, #ffffff 100%);
    }

    .product-page-head {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        color: #0f172a;
        padding: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
    }

    .page-kicker {
        letter-spacing: .08em;
        text-transform: uppercase;
        color: #475569;
        font-size: 12px;
    }

    .head-count {
        background: #f8fafc;
        border: 1px solid #dbe2ea;
        padding: 10px 14px;
        border-radius: 12px;
        font-weight: 500;
        color: #0f172a;
    }

    .head-count span {
        font-weight: 700;
        font-size: 20px;
        margin-right: 4px;
    }

    .filter-panel {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        position: sticky;
        top: 90px;
    }

    .filter-panel .card-header {
        background: #fff;
        color: #0f172a;
        border-bottom: 1px solid #e2e8f0 !important;
        border-radius: 16px 16px 0 0;
        padding: 16px 18px;
    }

    .filter-label {
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 10px;
    }

    .filter-panel .form-control {
        background: #fff;
        border: 1px solid #dbe2ea;
    }

    .filter-panel .form-control:focus {
        border-color: #93c5fd;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
    }

    .category-list {
        max-height: 280px;
        overflow: auto;
        padding-right: 4px;
    }

    .category-item {
        display: flex;
        align-items: center;
        gap: 10px;
        border: 1px solid #dbe2ea;
        background: #fff;
        border-radius: 10px;
        padding: 10px 12px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: all .2s ease;
    }

    .category-item:hover {
        border-color: #93c5fd;
        background: #fff;
    }

    .btn-filter {
        background: linear-gradient(120deg, #2563eb, #3b82f6);
        border: 0;
        color: #fff;
        border-radius: 10px;
        padding: 10px 14px;
        font-weight: 600;
    }

    .btn-clear {
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        color: #1e293b;
        padding: 10px 14px;
        font-weight: 600;
        background: #fff;
    }

    .active-filters {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .chip {
        background: #e2e8f0;
        color: #0f172a;
        border-radius: 999px;
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .product-grid-item {
        padding: 0 10px;
    }

    .product-card-modern {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        overflow: hidden;
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        position: relative;
        border: 1px solid #f0f0f0;
    }

    .product-card-modern:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 35px rgba(0, 102, 255, 0.12);
    }

    .stock-badge {
        position: absolute;
        top: 15px;
        left: 15px;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.5px;
        z-index: 10;
        text-transform: uppercase;
    }

    .stock-badge.in-stock {
        background: #e8f5e9;
        color: #2e7d32;
    }

    .stock-badge.out-of-stock {
        background: #ffebee;
        color: #c62828;
    }

    .product-image-container {
        position: relative;
        height: 180px;
        overflow: hidden;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }

    .product-image-link {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }

    .product-main-img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        object-position: center;
        transition: transform 0.3s ease;
    }

    .product-card-modern:hover .product-main-img {
        transform: scale(1.03);
    }

    .product-details {
        padding: 16px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .product-rating {
        display: flex;
        align-items: center;
        gap: 4px;
        margin-bottom: 8px;
        font-size: 13px;
    }

    .product-rating i {
        color: #ffc107;
        font-size: 12px;
    }

    .product-rating .rating-value {
        font-weight: 600;
        color: #333;
    }

    .product-rating .review-count {
        color: #999;
        font-size: 12px;
    }

    .product-brand {
        font-size: 11px;
        color: #1D4ED8;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }

    .product-name {
        font-size: 14px;
        font-weight: 600;
        line-height: 1.4;
        margin-bottom: 12px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 40px;
    }

    .product-name a {
        color: #272b41;
        text-decoration: none;
        transition: color 0.2s;
    }

    .product-name a:hover {
        color: #1D4ED8;
    }

    .product-footer {
        margin-top: auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .product-price-tag {
        display: flex;
        flex-direction: column;
    }

    .price-current {
        font-size: 18px;
        font-weight: 700;
        color: #272b41;
    }

    .price-original {
        font-size: 12px;
        color: #999;
        text-decoration: line-through;
    }

    .product-actions-form {
        display: flex;
    }

    .btn-group-modern {
        display: flex;
        gap: 6px;
    }

    .btn-cart-modern {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        border: 2px solid #1D4ED8;
        background: transparent;
        color: #1D4ED8;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-cart-modern:hover {
        background: #1D4ED8;
        color: #fff;
    }

    .btn-buy-modern {
        padding: 0 20px;
        height: 38px;
        border-radius: 8px;
        border: none;
        background: linear-gradient(135deg, #1D4ED8 0%, #60A5FA 100%);
        color: #fff;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-buy-modern:hover {
        background: linear-gradient(135deg, #1E40AF 0%, #3B82F6 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 102, 255, 0.3);
    }

    .empty-state {
        border: 1px dashed #cbd5e1;
        background: #f8fafc;
        border-radius: 16px;
        padding: 42px 18px;
    }

    @media (max-width: 991px) {
        .product-page-head {
            flex-direction: column;
            align-items: flex-start;
        }

        .filter-panel {
            position: static;
        }
    }
</style>
@endpush

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
                        <div class="col-md-6 col-lg-6 col-xl-4 mb-4">
                            <div class="card product-card h-100 border-0">
                                <a href="{{ route('ecommerce.products.show', $product->id) }}" class="product-image-wrap">
                                    <img src="{{ $product->image ? asset($product->image) : asset('assets/img/products/default-product.png') }}"
                                        class="card-img-top" alt="{{ $product->name }}">
                                </a>
                                <div class="card-body d-flex flex-column">
                                    <span class="badge product-badge mb-2">{{ $product->category->name ?? 'General' }}</span>
                                    <h5 class="card-title mb-2">
                                        <a href="{{ route('ecommerce.products.show', $product->id) }}">{{ $product->name }}</a>
                                    </h5>

                                    <div class="product-price mb-3 mt-auto">
                                        @if($product->sale_price)
                                            <span class="old-price">৳{{ number_format($product->price, 2) }}</span>
                                            <span class="new-price">৳{{ number_format($product->sale_price, 2) }}</span>
                                        @else
                                            <span class="new-price">৳{{ number_format($product->price, 2) }}</span>
                                        @endif
                                    </div>

                                    <form action="{{ route('ecommerce.cart.add') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn btn-cart w-100">
                                            <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                                        </button>
                                    </form>
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

    .product-card {
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(2, 6, 23, 0.08);
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 30px rgba(2, 6, 23, 0.14);
    }

    .product-image-wrap {
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }

    .product-card .card-img-top {
        height: 220px;
        object-fit: contain;
        object-position: center;
        width: 100%;
    }

    .product-badge {
        width: max-content;
        background: #dbeafe;
        color: #1d4ed8;
        font-weight: 600;
    }

    .product-card .card-title a {
        color: #0f172a;
        text-decoration: none;
    }

    .product-card .card-title a:hover {
        color: #2563eb;
    }

    .product-price {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .old-price {
        color: #64748b;
        text-decoration: line-through;
        font-size: 14px;
    }

    .new-price {
        color: #0f172a;
        font-weight: 700;
        font-size: 18px;
    }

    .btn-cart {
        background: #0f172a;
        color: #fff;
        border: 0;
        border-radius: 10px;
        font-weight: 600;
        padding: 10px 14px;
    }

    .btn-cart:hover {
        background: #1e293b;
        color: #fff;
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

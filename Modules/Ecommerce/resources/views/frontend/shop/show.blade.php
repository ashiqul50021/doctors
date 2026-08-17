@extends('layouts.app')

@section('title', $sellerProfile->store_name . ' - Store')

@push('styles')
@include('ecommerce::components.skeletons.styles')
<style>
    .store-page-wrap {
        background: #f8fafc;
        min-height: 80vh;
        padding-bottom: 60px;
    }

    /* Store Header / Banner */
    .store-hero-banner {
        position: relative;
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border-radius: 20px;
        overflow: hidden;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
    }

    .store-cover-img {
        width: 100%;
        height: 220px;
        object-fit: cover;
        opacity: 0.85;
    }

    .store-cover-placeholder {
        width: 100%;
        height: 180px;
        background: radial-gradient(circle at 80% 20%, rgba(37, 99, 235, 0.3) 0%, rgba(15, 23, 42, 0.95) 70%);
    }

    .store-hero-content {
        padding: 0 30px 25px;
        margin-top: -60px;
        position: relative;
        z-index: 2;
    }

    .store-profile-flex {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 20px;
    }

    .store-logo-info-wrap {
        display: flex;
        align-items: flex-end;
        gap: 20px;
    }

    .store-avatar-img {
        width: 110px;
        height: 110px;
        border-radius: 20px;
        object-fit: cover;
        background: #ffffff;
        padding: 4px;
        border: 3px solid #ffffff;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    }

    .store-title-box {
        margin-bottom: 5px;
    }

    .store-name-title {
        font-size: 24px;
        font-weight: 800;
        color: #ffffff;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .verified-shop-badge {
        background: #10b981;
        color: #ffffff;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        letter-spacing: 0.3px;
    }

    .store-contact-meta {
        display: flex;
        align-items: center;
        gap: 18px;
        color: #cbd5e1;
        font-size: 13px;
        flex-wrap: wrap;
    }

    .store-contact-meta span {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .store-stats-card-group {
        display: flex;
        align-items: center;
        gap: 12px;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        padding: 10px 18px;
        border-radius: 14px;
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: #ffffff;
    }

    .store-stat-item {
        text-align: center;
        padding: 0 8px;
    }

    .store-stat-val {
        font-size: 16px;
        font-weight: 800;
        color: #38bdf8;
        display: block;
        line-height: 1.2;
    }

    .store-stat-lbl {
        font-size: 11px;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-divider {
        width: 1px;
        height: 24px;
        background: rgba(255, 255, 255, 0.2);
    }

    /* Filter Bar */
    .shop-filter-bar {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 16px 20px;
        margin-bottom: 25px;
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.03);
    }

    .category-pills-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
        overflow-x: auto;
        padding-bottom: 4px;
    }

    .category-pill {
        padding: 6px 14px;
        border-radius: 20px;
        background: #f1f5f9;
        color: #475569;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        white-space: nowrap;
        transition: all 0.2s ease;
        border: 1px solid transparent;
    }

    .category-pill:hover {
        background: #e2e8f0;
        color: #1e293b;
    }

    .category-pill.active {
        background: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
    }

    /* Product Card Styling */
    .store-product-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.25s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.03);
    }

    .store-product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(37, 99, 235, 0.09);
        border-color: #cbd5e1;
    }

    .store-card-img-wrap {
        position: relative;
        background: #f8fafc;
        padding: 16px;
        text-align: center;
        aspect-ratio: 1 / 1;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .store-card-img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        transition: transform 0.3s ease;
    }

    .store-product-card:hover .store-card-img {
        transform: scale(1.05);
    }

    .store-badge-discount {
        position: absolute;
        top: 12px;
        left: 12px;
        background: #ef4444;
        color: #ffffff;
        font-size: 11px;
        font-weight: 800;
        padding: 3px 8px;
        border-radius: 6px;
    }

    .store-card-body {
        padding: 16px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .store-card-cat {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }

    .store-card-title {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.4;
        margin-bottom: 10px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-decoration: none;
    }

    .store-card-title a {
        color: inherit;
        text-decoration: none;
    }

    .store-card-title a:hover {
        color: #2563eb;
    }

    .store-card-footer {
        margin-top: auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding-top: 12px;
        border-top: 1px solid #f1f5f9;
    }

    .store-price-cur {
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
    }

    .store-price-old {
        font-size: 13px;
        color: #94a3b8;
        text-decoration: line-through;
        margin-left: 4px;
    }

    .btn-store-cart {
        background: #eff6ff;
        color: #2563eb;
        border: 1px solid #dbeafe;
        border-radius: 10px;
        padding: 8px 14px;
        font-size: 13px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .btn-store-cart:hover {
        background: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
    }

    @media (max-width: 768px) {
        .store-hero-content {
            padding: 0 16px 20px;
            margin-top: -45px;
        }

        .store-avatar-img {
            width: 85px;
            height: 85px;
        }

        .store-name-title {
            font-size: 20px;
        }

        .store-profile-flex {
            align-items: flex-start;
        }

        .store-stats-card-group {
            width: 100%;
            justify-content: space-around;
        }
    }
</style>
@endpush

@section('content')
<div class="store-page-wrap">
    <div class="container pt-4">
        <!-- Store Hero Banner -->
        <div class="store-hero-banner">
            @if($sellerProfile->store_banner)
                <img src="{{ asset($sellerProfile->store_banner) }}" alt="{{ $sellerProfile->store_name }} Cover" class="store-cover-img">
            @else
                <div class="store-cover-placeholder"></div>
            @endif

            <div class="store-hero-content">
                <div class="store-profile-flex">
                    <div class="store-logo-info-wrap">
                        <img src="{{ $sellerProfile->store_logo ? asset($sellerProfile->store_logo) : asset('assets/img/patients/patient.jpg') }}" 
                             alt="{{ $sellerProfile->store_name }}" class="store-avatar-img">
                        
                        <div class="store-title-box">
                            <h1 class="store-name-title">
                                {{ $sellerProfile->store_name }}
                                <span class="verified-shop-badge">
                                    <i class="fas fa-check-circle"></i> Verified Seller
                                </span>
                            </h1>
                            <div class="store-contact-meta">
                                @if($sellerProfile->address)
                                    <span><i class="fas fa-map-marker-alt text-danger"></i> {{ $sellerProfile->address }}</span>
                                @endif
                                @if($sellerProfile->phone)
                                    <span><i class="fas fa-phone-alt text-success"></i> {{ $sellerProfile->phone }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Store Stats -->
                    <div class="store-stats-card-group">
                        <div class="store-stat-item">
                            <span class="store-stat-val">{{ number_format($totalProducts) }}</span>
                            <span class="store-stat-lbl">Products</span>
                        </div>
                        <div class="stat-divider"></div>
                        <div class="store-stat-item">
                            <span class="store-stat-val">{{ number_format($totalSold) }}</span>
                            <span class="store-stat-lbl">Items Sold</span>
                        </div>
                        <div class="stat-divider"></div>
                        <div class="store-stat-item">
                            <span class="store-stat-val">{{ $sellerProfile->created_at ? $sellerProfile->created_at->format('M Y') : 'Active' }}</span>
                            <span class="store-stat-lbl">Member Since</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Search Controls -->
        <div class="shop-filter-bar">
            <div class="row g-3 align-items-center">
                <div class="col-lg-6">
                    <div class="category-pills-wrap">
                        <a href="{{ route('ecommerce.shop.show', $sellerProfile->store_slug) }}" 
                           class="category-pill {{ !request('category') ? 'active' : '' }}">
                            All Products ({{ $totalProducts }})
                        </a>
                        @foreach($categories as $category)
                            <a href="{{ route('ecommerce.shop.show', ['slug' => $sellerProfile->store_slug, 'category' => $category->id]) }}" 
                               class="category-pill {{ request('category') == $category->id ? 'active' : '' }}">
                                {{ $category->name }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="col-lg-6">
                    <form method="GET" action="{{ route('ecommerce.shop.show', $sellerProfile->store_slug) }}" class="d-flex align-items-center gap-2 justify-content-lg-end">
                        @if(request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif
                        
                        <div class="input-group" style="max-width: 260px;">
                            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search in store..." value="{{ request('search') }}">
                            <button class="btn btn-primary btn-sm" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>

                        <select name="sort" class="form-select form-select-sm" style="max-width: 160px;" onchange="this.form.submit()">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Newest</option>
                            <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Popular</option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                        </select>
                    </form>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="row g-3">
            @forelse($products as $product)
                @php
                    $availableStock = $product->availableStock();
                    $hasVariants = $product->hasActiveVariants();
                    $displayPrice = $product->effectivePrice();
                    $displayRegularPrice = $product->effectiveRegularPrice();
                    $discountPct = ($displayRegularPrice > $displayPrice && $displayRegularPrice > 0)
                        ? round((($displayRegularPrice - $displayPrice) / $displayRegularPrice) * 100)
                        : 0;
                    
                    $image = $product->image;
                    if (!$image && !empty($product->gallery) && is_array($product->gallery)) {
                        $image = $product->gallery[0] ?? null;
                    }
                @endphp
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="store-product-card">
                        <div class="store-card-img-wrap">
                            <a href="{{ route('ecommerce.products.show', $product->id) }}">
                                <img src="{{ $image ? asset($image) : asset('assets/img/products/default-product.png') }}" 
                                     alt="{{ $product->name }}" class="store-card-img">
                            </a>

                            @if($discountPct > 0)
                                <span class="store-badge-discount">-{{ $discountPct }}%</span>
                            @endif
                        </div>

                        <div class="store-card-body">
                            <span class="store-card-cat">{{ $product->category->name ?? 'Healthcare' }}</span>
                            <h3 class="store-card-title">
                                <a href="{{ route('ecommerce.products.show', $product->id) }}">
                                    {{ $product->name }}
                                </a>
                            </h3>

                            <div class="store-card-footer">
                                <div>
                                    <span class="store-price-cur">৳{{ number_format($displayPrice, 0) }}</span>
                                    @if($displayPrice < $displayRegularPrice)
                                        <span class="store-price-old">৳{{ number_format($displayRegularPrice, 0) }}</span>
                                    @endif
                                </div>

                                <a href="{{ route('ecommerce.products.show', $product->id) }}" class="btn-store-cart">
                                    <i class="fas fa-eye"></i> Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card p-5 text-center border-0 shadow-sm" style="border-radius: 16px;">
                        <i class="fas fa-box-open text-muted mb-3" style="font-size: 48px;"></i>
                        <h4 class="fw-bold mb-2">No Products Available</h4>
                        <p class="text-muted mb-3">This store does not have any matching products at the moment.</p>
                        <div>
                            <a href="{{ route('ecommerce.shop.show', $sellerProfile->store_slug) }}" class="btn btn-outline-primary btn-sm">
                                Clear Filters
                            </a>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($products->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

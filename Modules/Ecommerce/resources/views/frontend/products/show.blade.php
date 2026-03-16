@extends('layouts.app')

@section('title', ($product->meta_title ?: $product->name) . ' - abcsheba.com')

@section('content')
@php
    $fallbackImage = asset('assets/img/specialities/specialities-01.png');
    $stockQty = (int) ($product->stock ?? 0);
    $regularPrice = (float) ($product->price ?? 0);
    $displayPrice = (float) ($product->sale_price ?: $product->price);
    $discountAmount = max(0, $regularPrice - $displayPrice);
    $galleryImages = collect($product->gallery ?? [])
        ->prepend($product->image)
        ->filter()
        ->unique()
        ->values();
    $mainImage = $galleryImages->first();
    $brandName = $product->brand ?: ($product->category->name ?? 'ABCSheba');
    $sku = $product->sku ?: strtoupper($product->slug ?: ('PRO-' . $product->id));
    $tagList = collect(preg_split('/\s*,\s*/', (string) $product->tags))
        ->map(fn ($tag) => trim($tag))
        ->filter()
        ->values();
    $summaryPoints = collect([
        $product->brand ? 'Brand: ' . $brandName : null,
        $product->category ? 'Category: ' . $product->category->name : null,
        $stockQty > 0 ? 'Stock available: ' . $stockQty . ' units' : 'Currently out of stock',
        $product->sale_price ? 'Discounted price is active' : 'Regular price item',
    ])->filter()->values();
@endphp

<div class="content product-single-page">
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
        @endif

        <nav class="product-breadcrumb">
            <a href="{{ route('home') }}">Home</a>
            <span><i class="fas fa-angle-right"></i></span>
            <a href="{{ route('ecommerce.products') }}">Products</a>
            <span><i class="fas fa-angle-right"></i></span>
            <a href="{{ route('ecommerce.products', ['category' => $product->product_category_id]) }}">
                {{ $product->category->name ?? 'General' }}
            </a>
            <span><i class="fas fa-angle-right"></i></span>
            <span>{{ $product->name }}</span>
        </nav>

        <section class="product-hero-card">
            <div class="row g-4 align-items-start">
                <div class="col-lg-7">
                    <div class="product-media-panel">
                        <div class="media-topbar">
                            <span class="product-chip">{{ $product->category->name ?? 'Healthcare' }}</span>
                            @if($product->sale_price)
                                <span class="offer-chip">{{ $product->discount_percentage }}% OFF</span>
                            @endif
                        </div>

                        <div class="product-image-main">
                            <img id="activeProductImage"
                                src="{{ $mainImage ? asset($mainImage) : $fallbackImage }}"
                                alt="{{ $product->name }}"
                                onerror="this.onerror=null;this.src='{{ $fallbackImage }}';">
                        </div>

                        @if($galleryImages->isNotEmpty())
                            <div class="product-thumb-strip">
                                @foreach($galleryImages as $image)
                                    <button type="button"
                                        class="product-thumb {{ $loop->first ? 'is-active' : '' }}"
                                        data-image="{{ asset($image) }}"
                                        aria-label="Preview image {{ $loop->iteration }}">
                                        <img src="{{ asset($image) }}"
                                            alt="{{ $product->name }} thumbnail {{ $loop->iteration }}"
                                            onerror="this.onerror=null;this.src='{{ $fallbackImage }}';">
                                    </button>
                                @endforeach
                            </div>
                        @endif

                        <div class="product-trust-row">
                            <div class="trust-pill">
                                <i class="fas fa-check-circle"></i>
                                <span>Authentic healthcare listing</span>
                            </div>
                            <div class="trust-pill">
                                <i class="fas fa-truck"></i>
                                <span>{{ $stockQty > 0 ? 'Quick dispatch available' : 'Notify on restock' }}</span>
                            </div>
                            <div class="trust-pill">
                                <i class="fas fa-shield-alt"></i>
                                <span>Standard return support</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <aside class="product-summary-card">
                        <div class="summary-head">
                            <p class="summary-kicker">{{ $brandName }}</p>
                            <h1>{{ $product->name }}</h1>
                        </div>

                        <div class="summary-price-box">
                            <div class="price-primary">৳{{ number_format($displayPrice, 0) }}</div>
                            <div class="price-meta">
                                @if($product->sale_price)
                                    <span class="price-old">৳{{ number_format($regularPrice, 0) }}</span>
                                    <span class="price-save">Save ৳{{ number_format($discountAmount, 0) }}</span>
                                @else
                                    <span class="price-note">Best available regular price</span>
                                @endif
                            </div>
                        </div>

                        <div class="summary-status-grid">
                            <div class="status-card">
                                <span class="status-label">Availability</span>
                                <strong class="{{ $stockQty > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $stockQty > 0 ? 'In stock' : 'Out of stock' }}
                                </strong>
                            </div>
                            <div class="status-card">
                                <span class="status-label">SKU</span>
                                <strong>{{ $sku }}</strong>
                            </div>
                            <div class="status-card">
                                <span class="status-label">Category</span>
                                <strong>{{ $product->category->name ?? 'General' }}</strong>
                            </div>
                            <div class="status-card">
                                <span class="status-label">Price Type</span>
                                <strong>{{ $product->sale_price ? 'Discounted' : 'Regular' }}</strong>
                            </div>
                        </div>

                        @if($summaryPoints->isNotEmpty())
                            <ul class="summary-feature-list">
                                @foreach($summaryPoints as $point)
                                    <li><i class="fas fa-check"></i> {{ $point }}</li>
                                @endforeach
                            </ul>
                        @endif

                        @if($tagList->isNotEmpty())
                            <div class="product-tag-list">
                                @foreach($tagList->take(6) as $tag)
                                    <span>{{ ucfirst($tag) }}</span>
                                @endforeach
                            </div>
                        @endif

                        <form action="{{ route('ecommerce.cart.add') }}" method="POST" class="purchase-form">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">

                            <div class="purchase-controls">
                                <div class="quantity-box">
                                    <label for="productQuantity">Quantity</label>
                                    <div class="quantity-control">
                                        <button type="button" class="qty-btn" data-action="decrease" {{ $stockQty < 1 ? 'disabled' : '' }}>-</button>
                                        <input id="productQuantity"
                                            type="number"
                                            name="quantity"
                                            class="qty-input"
                                            value="1"
                                            min="1"
                                            max="{{ max(1, $stockQty) }}"
                                            {{ $stockQty < 1 ? 'disabled' : '' }}>
                                        <button type="button" class="qty-btn" data-action="increase" {{ $stockQty < 1 ? 'disabled' : '' }}>+</button>
                                    </div>
                                </div>

                                <div class="support-note">
                                    <i class="fas fa-phone-alt"></i>
                                    <span>Need bulk order or prescription support? Contact us before checkout.</span>
                                </div>
                            </div>

                            <div class="action-buttons-row">
                                <button type="submit" class="btn-cart-primary" title="Add to Cart" {{ $stockQty < 1 ? 'disabled' : '' }}>
                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                </button>
                                <button type="submit" name="buy_now" value="1" class="btn-buy-modern" {{ $stockQty < 1 ? 'disabled' : '' }}>
                                    <i class="fas fa-bolt"></i> Buy Now
                                </button>
                            </div>
                        </form>

                        <div class="summary-assurance">
                            <div class="assurance-item">
                                <i class="fas fa-wallet"></i>
                                <span>Cash on delivery available</span>
                            </div>
                            <div class="assurance-item">
                                <i class="fas fa-undo"></i>
                                <span>Easy replacement on eligible issues</span>
                            </div>
                            <div class="assurance-item">
                                <i class="fas fa-lock"></i>
                                <span>Secure checkout experience</span>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </section>

        @if($relatedProducts->count() > 0)
            <section class="related-products-section">
                <div class="related-head">
                    <div>
                        <span class="section-tag">More options</span>
                        <h2>Related products</h2>
                    </div>
                    <a href="{{ route('ecommerce.products') }}" class="btn-view-all-arrow">
                        View all products <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="row g-4">
                    @foreach($relatedProducts as $relProduct)
                        @php
                            $relatedImage = $relProduct->image ?: 'assets/img/specialities/specialities-01.png';
                            $relatedPrice = $relProduct->sale_price ?: $relProduct->price;
                        @endphp
                        <div class="col-md-6 col-xl-3">
                            <div class="related-product-card">
                                <div class="related-card-badge {{ ($relProduct->stock ?? 0) > 0 ? 'in-stock' : 'out-stock' }}">
                                    {{ ($relProduct->stock ?? 0) > 0 ? 'In Stock' : 'Out of Stock' }}
                                </div>

                                <a href="{{ route('ecommerce.products.show', $relProduct->id) }}" class="related-image-wrap">
                                    <img src="{{ asset($relatedImage) }}"
                                        alt="{{ $relProduct->name }}"
                                        onerror="this.onerror=null;this.src='{{ $fallbackImage }}';">
                                </a>

                                <div class="related-body">
                                    <div class="related-meta">
                                        <span>{{ $relProduct->category->name ?? 'Healthcare' }}</span>
                                        <small>{{ ($relProduct->stock ?? 0) > 0 ? $relProduct->stock . ' in stock' : 'Unavailable' }}</small>
                                    </div>

                                    <h3>
                                        <a href="{{ route('ecommerce.products.show', $relProduct->id) }}">{{ $relProduct->name }}</a>
                                    </h3>

                                    <div class="related-price-row">
                                        <strong>৳{{ number_format($relatedPrice, 0) }}</strong>
                                        @if($relProduct->sale_price)
                                            <span>৳{{ number_format((float) $relProduct->price, 0) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .product-single-page {
        background: linear-gradient(180deg, #f7fbff 0%, #ffffff 42%, #f8fbff 100%);
        padding: 26px 0 70px;
    }

    .product-breadcrumb {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 18px;
        color: #64748b;
        font-size: 13px;
    }

    .product-breadcrumb a {
        color: #1d4ed8;
        text-decoration: none;
        font-weight: 500;
    }

    .product-hero-card {
        background: #fff;
        border-radius: 24px;
        padding: 20px;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.06);
    }

    .product-media-panel {
        background: linear-gradient(180deg, #f8fbff 0%, #f1f7ff 100%);
        border-radius: 22px;
        padding: 20px;
    }

    .media-topbar {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 18px;
    }

    .product-chip,
    .offer-chip,
    .section-tag {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
        padding: 7px 12px;
    }

    .product-chip,
    .section-tag {
        background: rgba(37, 99, 235, 0.1);
        color: #1d4ed8;
    }

    .offer-chip {
        background: rgba(249, 115, 22, 0.14);
        color: #ea580c;
    }

    .product-image-main {
        background: rgba(255, 255, 255, 0.82);
        border-radius: 20px;
        min-height: 430px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 24px;
    }

    .product-image-main img {
        width: 100%;
        max-height: 380px;
        object-fit: contain;
    }

    .product-thumb-strip {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 18px;
    }

    .product-thumb {
        width: 78px;
        height: 78px;
        border-radius: 18px;
        background: #fff;
        padding: 8px;
        transition: all .25s ease;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
    }

    .product-thumb img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .product-thumb:hover,
    .product-thumb.is-active {
        box-shadow: 0 12px 24px rgba(59, 130, 246, 0.18);
        transform: translateY(-2px);
    }

    .product-trust-row {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 18px;
    }

    .trust-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.9);
        color: #334155;
        font-size: 13px;
        font-weight: 600;
    }

    .trust-pill i,
    .summary-feature-list i,
    .benefit-item i,
    .service-row i,
    .assurance-item i {
        color: #2563eb;
    }

    .product-summary-card {
        position: sticky;
        top: 96px;
        background: #fff;
        border-radius: 24px;
        padding: 24px;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.06);
    }

    .summary-kicker {
        margin-bottom: 8px;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: .12em;
        font-size: 12px;
        font-weight: 700;
    }

    .summary-head h1 {
        margin-bottom: 12px;
        color: #0f172a;
        font-size: clamp(28px, 3vw, 40px);
        line-height: 1.15;
    }

    .summary-price-box {
        margin-top: 22px;
        padding: 20px;
        border-radius: 22px;
        background: linear-gradient(135deg, #eff6ff 0%, #f8fbff 100%);
    }

    .price-primary {
        color: #0f172a;
        font-size: 40px;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 10px;
    }

    .price-meta {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .price-old {
        color: #94a3b8;
        font-size: 16px;
        text-decoration: line-through;
    }

    .price-save {
        color: #ea580c;
        background: rgba(249, 115, 22, 0.12);
        padding: 5px 10px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 700;
    }

    .price-note {
        color: #334155;
        font-size: 14px;
        font-weight: 600;
    }

    .summary-status-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        margin-top: 18px;
    }

    .status-card {
        background: #f8fbff;
        border-radius: 18px;
        padding: 14px;
    }

    .status-label {
        display: block;
        color: #64748b;
        font-size: 12px;
        margin-bottom: 6px;
    }

    .status-card strong {
        color: #0f172a;
        font-size: 15px;
    }

    .summary-feature-list {
        list-style: none;
        padding: 0;
        margin: 20px 0 0;
        display: grid;
        gap: 10px;
    }

    .summary-feature-list li {
        display: flex;
        gap: 10px;
        align-items: flex-start;
        color: #334155;
        font-size: 14px;
    }

    .product-tag-list {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 16px;
    }

    .product-tag-list span {
        background: #eff6ff;
        color: #1d4ed8;
        border-radius: 999px;
        padding: 8px 12px;
        font-size: 13px;
        font-weight: 600;
    }

    .purchase-form {
        margin-top: 24px;
        padding-top: 22px;
        border-top: 0;
    }

    .purchase-controls {
        display: grid;
        gap: 14px;
    }

    .quantity-box label {
        display: block;
        margin-bottom: 10px;
        color: #0f172a;
        font-size: 13px;
        font-weight: 700;
    }

    .quantity-control {
        display: inline-flex;
        align-items: center;
        border-radius: 16px;
        overflow: hidden;
        background: #f8fbff;
    }

    .qty-btn {
        width: 48px;
        height: 48px;
        border: 0;
        background: #f8fbff;
        color: #0f172a;
        font-size: 22px;
    }

    .qty-input {
        width: 86px;
        border: 0;
        text-align: center;
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        box-shadow: none;
        appearance: textfield;
        -moz-appearance: textfield;
    }

    .qty-input::-webkit-outer-spin-button,
    .qty-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .support-note {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 12px 14px;
        border-radius: 16px;
        background: #f8fbff;
        color: #475569;
        font-size: 13px;
    }

    .action-buttons-row {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        margin-top: 16px;
    }

    .btn-cart-primary,
    .btn-buy-modern {
        min-height: 54px;
        border: 0;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        color: #fff;
        font-size: 15px;
        font-weight: 700;
        transition: all .25s ease;
    }

    .btn-cart-primary {
        background: linear-gradient(135deg, #1d4ed8 0%, #60a5fa 100%);
        box-shadow: 0 10px 24px rgba(29, 78, 216, 0.24);
    }

    .btn-buy-modern {
        background: linear-gradient(135deg, #f97316 0%, #fb923c 100%);
        box-shadow: 0 10px 24px rgba(249, 115, 22, 0.22);
    }

    .btn-cart-primary:hover,
    .btn-buy-modern:hover {
        color: #fff;
        transform: translateY(-2px);
    }

    .btn-cart-primary:disabled,
    .btn-buy-modern:disabled {
        background: #cbd5e1;
        box-shadow: none;
        cursor: not-allowed;
        transform: none;
    }

    .summary-assurance {
        display: grid;
        gap: 10px;
        margin-top: 20px;
        padding-top: 18px;
        border-top: 0;
    }

    .assurance-item {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #334155;
        font-size: 14px;
        font-weight: 500;
    }

    .related-products-section {
        margin-top: 28px;
    }

    .related-product-card {
        background: #fff;
        border-radius: 24px;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.05);
    }

    .related-head h2 {
        color: #0f172a;
        margin-bottom: 10px;
        font-size: 24px;
        font-weight: 700;
    }

    .related-head {
        display: flex;
        align-items: end;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 20px;
    }

    .btn-view-all-arrow {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 12px 22px;
        border-radius: 999px;
        background: linear-gradient(135deg, #1d4ed8 0%, #60a5fa 100%);
        color: #fff;
        text-decoration: none;
        font-weight: 700;
        box-shadow: 0 10px 24px rgba(29, 78, 216, 0.22);
    }

    .btn-view-all-arrow:hover {
        color: #fff;
        transform: translateY(-2px);
    }

    .related-product-card {
        position: relative;
        overflow: hidden;
        height: 100%;
    }

    .related-card-badge {
        position: absolute;
        top: 18px;
        left: 18px;
        z-index: 2;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    .related-card-badge.in-stock {
        background: #dcfce7;
        color: #15803d;
    }

    .related-card-badge.out-stock {
        background: #fee2e2;
        color: #dc2626;
    }

    .related-image-wrap {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 220px;
        padding: 26px 20px 10px;
        background: linear-gradient(180deg, #f8fbff 0%, #eef6ff 100%);
        text-decoration: none;
    }

    .related-image-wrap img {
        width: 100%;
        max-height: 180px;
        object-fit: contain;
    }

    .related-body {
        padding: 20px;
    }

    .related-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 12px;
        color: #64748b;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    .related-meta small {
        color: #64748b;
        font-size: 12px;
        font-weight: 600;
        text-transform: none;
        letter-spacing: 0;
    }

    .related-body h3 {
        margin-bottom: 12px;
        min-height: 48px;
    }

    .related-body h3 a {
        color: #0f172a;
        text-decoration: none;
        font-size: 18px;
        line-height: 1.35;
    }

    .related-price-row {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .related-price-row strong {
        color: #0f172a;
        font-size: 22px;
    }

    .related-price-row span {
        color: #94a3b8;
        text-decoration: line-through;
        font-size: 14px;
    }

    @media (max-width: 1199px) {
        .summary-head h1 {
            font-size: 32px;
        }
    }

    @media (max-width: 991px) {
        .product-single-page {
            padding-top: 18px;
        }

        .product-hero-card {
            border-radius: 22px;
            padding: 20px;
        }

        .product-image-main {
            min-height: 320px;
        }

        .product-summary-card {
            position: static;
        }

        .related-head {
            align-items: flex-start;
            flex-direction: column;
        }
    }

    @media (max-width: 575px) {
        .product-hero-card {
            padding: 16px;
        }

        .product-media-panel,
        .product-summary-card,
        .related-product-card {
            border-radius: 20px;
        }

        .summary-status-grid,
        .action-buttons-row {
            grid-template-columns: 1fr;
        }

        .related-meta {
            align-items: flex-start;
            flex-direction: column;
        }

        .product-thumb {
            width: 68px;
            height: 68px;
            border-radius: 16px;
        }

        .product-trust-row {
            flex-direction: column;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const activeImage = document.getElementById('activeProductImage');
        const thumbs = document.querySelectorAll('.product-thumb');
        const quantityInput = document.getElementById('productQuantity');
        const qtyButtons = document.querySelectorAll('.qty-btn');

        thumbs.forEach((thumb) => {
            thumb.addEventListener('click', function () {
                if (!activeImage || !this.dataset.image) {
                    return;
                }

                activeImage.src = this.dataset.image;
                thumbs.forEach((item) => item.classList.remove('is-active'));
                this.classList.add('is-active');
            });
        });

        qtyButtons.forEach((button) => {
            button.addEventListener('click', function () {
                if (!quantityInput) {
                    return;
                }

                const currentValue = Number(quantityInput.value || 1);
                const min = Number(quantityInput.min || 1);
                const max = Number(quantityInput.max || currentValue);
                const nextValue = this.dataset.action === 'increase'
                    ? Math.min(max, currentValue + 1)
                    : Math.max(min, currentValue - 1);

                quantityInput.value = nextValue;
            });
        });
    });
</script>
@endpush

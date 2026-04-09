@extends('layouts.app')

@section('title', ($product->meta_title ?: $product->name) . ' - abcsheba.com')

@section('content')
@php
    $fallbackImage = asset('assets/img/products/default-product.png');
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
    $reviewCount = (int) ($product->reviews_count ?? 0);
    $tagList = collect(preg_split('/\s*,\s*/', (string) $product->tags))
        ->map(fn ($tag) => trim($tag))
        ->filter()
        ->values();
    $summaryCopy = $product->meta_description
        ?: (\Illuminate\Support\Str::limit(strip_tags((string) $product->description), 140, '...') ?: 'Reliable healthcare product with clear pricing, stock status, and simple ordering support.');
    $detailDescription = trim((string) $product->description) !== ''
        ? nl2br(e($product->description))
        : 'This product page shows the full item overview, pricing, availability, and checkout actions in the same visual system as the product listing.';
    $summaryPoints = collect([
        $product->brand ? 'Brand: ' . $brandName : null,
        $product->category ? 'Category: ' . $product->category->name : null,
        $stockQty > 0 ? 'Available stock: ' . $stockQty . ' units' : 'This item is currently out of stock',
        $product->sale_price ? 'Current sale price is active' : 'Standard listed price',
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
                    <div class="product-gallery-shell product-card-modern">
                        <div class="stock-badge {{ $stockQty > 0 ? 'in-stock' : 'out-of-stock' }}">
                            {{ $stockQty > 0 ? 'IN STOCK' : 'OUT OF STOCK' }}
                        </div>

                        @if($product->sale_price)
                            <div class="detail-offer-badge">{{ $product->discount_percentage }}% OFF</div>
                        @endif

                        <div class="product-image-container product-image-main detail-main-image">
                            <img id="activeProductImage"
                                src="{{ $mainImage ? asset($mainImage) : $fallbackImage }}"
                                class="product-main-img"
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

                        <div class="detail-trust-grid">
                            <div class="detail-trust-card">
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Clear product details</strong>
                                    <span>Category, stock, and pricing are shown up front.</span>
                                </div>
                            </div>
                            <div class="detail-trust-card">
                                <i class="fas fa-truck"></i>
                                <div>
                                    <strong>{{ $stockQty > 0 ? 'Ready to order' : 'Currently unavailable' }}</strong>
                                    <span>{{ $stockQty > 0 ? 'Add to cart or buy now directly from this page.' : 'You can revisit later when stock is updated.' }}</span>
                                </div>
                            </div>
                            <div class="detail-trust-card">
                                <i class="fas fa-shield-alt"></i>
                                <div>
                                    <strong>Secure checkout</strong>
                                    <span>Simple checkout flow with order support when needed.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <aside class="product-summary-card">
                        <div class="product-rating product-rating-large">
                            <i class="fas fa-star"></i>
                            <span class="rating-value">{{ number_format((float) ($product->rating ?? 4.8), 1) }}</span>
                            <span class="review-count">({{ $reviewCount }} reviews)</span>
                        </div>

                        <div class="product-brand">{{ $brandName }}</div>

                        <div class="summary-head">
                            <h1>{{ $product->name }}</h1>
                            <p class="summary-copy">{{ $summaryCopy }}</p>
                        </div>

                        <div class="summary-price-box">
                            <div class="product-price-tag">
                                <span class="price-current">৳{{ number_format($displayPrice, 0) }}</span>
                                @if($product->sale_price)
                                    <span class="price-original">৳{{ number_format($regularPrice, 0) }}</span>
                                @endif
                            </div>

                            <div class="price-meta">
                                @if($product->sale_price)
                                    <span class="price-save">Save ৳{{ number_format($discountAmount, 0) }}</span>
                                @else
                                    <span class="price-note">Standard listed price</span>
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
                                    <span>Need help with prescription or bulk order quantity? Contact support before checkout.</span>
                                </div>
                            </div>

                            <div class="product-footer detail-product-footer">
                                <div class="btn-group-modern detail-btn-group">
                                    <button type="submit" class="btn-cart-modern detail-cart-btn" title="Add to Cart" {{ $stockQty < 1 ? 'disabled' : '' }}>
                                        <i class="fas fa-shopping-cart"></i>
                                        <span>Add to Cart</span>
                                    </button>
                                    <button type="submit" name="buy_now" value="1" class="btn-buy-modern detail-buy-btn" {{ $stockQty < 1 ? 'disabled' : '' }}>
                                        Buy Now
                                    </button>
                                </div>
                            </div>
                        </form>

                        <a href="{{ route('ecommerce.cart') }}" class="detail-view-cart">
                            <i class="fas fa-eye"></i>
                            <span>View Cart</span>
                        </a>

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

        <section class="product-detail-sections">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="info-card">
                        <div class="section-heading">
                            <span class="section-tag">Product Details</span>
                            <h2>About this product</h2>
                        </div>

                        <div class="info-copy">{!! $detailDescription !!}</div>

                        <div class="info-chip-list">
                            <span>{{ $brandName }}</span>
                            <span>{{ $product->category->name ?? 'General' }}</span>
                            <span>{{ $sku }}</span>
                            <span>{{ $stockQty > 0 ? $stockQty . ' units available' : 'Currently unavailable' }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="info-card info-side-card">
                        <div class="section-heading">
                            <span class="section-tag">Order Support</span>
                            <h2>Before you checkout</h2>
                        </div>

                        <ul class="summary-feature-list compact-feature-list">
                            <li><i class="fas fa-check"></i> Adjust quantity before adding the item to your cart.</li>
                            <li><i class="fas fa-check"></i> Use Buy Now for a direct checkout flow.</li>
                            <li><i class="fas fa-check"></i> Browse related products below for similar options.</li>
                        </ul>
                    </div>
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
                            $relatedImage = $relProduct->image ?: 'assets/img/products/default-product.png';
                            $relatedPrice = $relProduct->sale_price ?: $relProduct->price;
                            $relatedReviews = (int) ($relProduct->reviews_count ?? 24);
                        @endphp
                        <div class="col-md-6 col-xl-3">
                            <div class="product-card-modern">
                                <div class="stock-badge {{ ($relProduct->stock ?? 0) > 0 ? 'in-stock' : 'out-of-stock' }}">
                                    {{ ($relProduct->stock ?? 0) > 0 ? 'IN STOCK' : 'OUT OF STOCK' }}
                                </div>

                                <div class="product-image-container">
                                    <a href="{{ route('ecommerce.products.show', $relProduct->id) }}" class="product-image-link">
                                        <img src="{{ asset($relatedImage) }}"
                                            class="product-main-img"
                                            alt="{{ $relProduct->name }}"
                                            onerror="this.onerror=null;this.src='{{ $fallbackImage }}';">
                                    </a>
                                </div>

                                <div class="product-details">
                                    <div class="product-rating">
                                        <i class="fas fa-star"></i>
                                        <span class="rating-value">{{ number_format((float) ($relProduct->rating ?? 4.7), 1) }}</span>
                                        <span class="review-count">({{ $relatedReviews }})</span>
                                    </div>

                                    <div class="product-brand">{{ $relProduct->category->name ?? 'Healthcare' }}</div>

                                    <h3 class="product-name">
                                        <a href="{{ route('ecommerce.products.show', $relProduct->id) }}">{{ $relProduct->name }}</a>
                                    </h3>

                                    <div class="product-footer">
                                        <div class="product-price-tag">
                                            <span class="price-current">৳{{ number_format($relatedPrice, 0) }}</span>
                                            @if($relProduct->sale_price)
                                                <span class="price-original">৳{{ number_format((float) $relProduct->price, 0) }}</span>
                                            @endif
                                        </div>

                                        <form action="{{ route('ecommerce.cart.add') }}" method="POST" class="product-actions-form">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $relProduct->id }}">
                                            <input type="hidden" name="quantity" value="1">
                                            <div class="btn-group-modern">
                                                <button type="submit" class="btn-cart-modern" title="Add to Cart" {{ ($relProduct->stock ?? 0) < 1 ? 'disabled' : '' }}>
                                                    <i class="fas fa-shopping-cart"></i>
                                                </button>
                                                <button type="submit" name="buy_now" value="1" class="btn-buy-modern" {{ ($relProduct->stock ?? 0) < 1 ? 'disabled' : '' }}>
                                                    Buy
                                                </button>
                                            </div>
                                        </form>
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
        background: linear-gradient(180deg, #f6faff 0%, #ffffff 42%, #f8fbff 100%);
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
        margin-bottom: 28px;
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

    .product-gallery-shell {
        padding: 18px;
        gap: 18px;
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

    .detail-offer-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        z-index: 10;
        padding: 6px 12px;
        border-radius: 999px;
        background: linear-gradient(135deg, #f97316 0%, #fb923c 100%);
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.05em;
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

    .detail-main-image {
        height: 440px;
        border-radius: 18px;
        background: linear-gradient(180deg, #f8fbff 0%, #eef5ff 100%);
        padding: 20px;
    }

    .product-image-link {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
        text-decoration: none;
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

    .product-thumb-strip {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
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

    .detail-trust-grid {
        display: grid;
        gap: 12px;
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .detail-trust-card {
        background: #f8fbff;
        border-radius: 16px;
        padding: 14px;
        display: flex;
        gap: 12px;
        align-items: flex-start;
    }

    .detail-trust-card i,
    .summary-feature-list i,
    .assurance-item i {
        color: #2563eb;
        margin-top: 2px;
    }

    .detail-trust-card strong {
        display: block;
        color: #0f172a;
        font-size: 14px;
        margin-bottom: 4px;
    }

    .detail-trust-card span {
        color: #64748b;
        font-size: 12px;
        line-height: 1.5;
    }

    .product-summary-card {
        position: sticky;
        top: 96px;
        background: #fff;
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.06);
        border: 1px solid #eef2f7;
    }

    .product-rating {
        display: flex;
        align-items: center;
        gap: 4px;
        margin-bottom: 8px;
        font-size: 13px;
    }

    .product-rating-large {
        margin-bottom: 14px;
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

    .summary-head h1 {
        margin-bottom: 12px;
        color: #0f172a;
        font-size: clamp(28px, 3vw, 40px);
        line-height: 1.15;
    }

    .summary-copy {
        color: #64748b;
        font-size: 14px;
        line-height: 1.7;
        margin-bottom: 0;
    }

    .summary-price-box {
        margin-top: 22px;
        padding: 20px;
        border-radius: 18px;
        background: linear-gradient(135deg, #eff6ff 0%, #f8fbff 100%);
    }

    .product-price-tag {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .price-current {
        color: #0f172a;
        font-size: 36px;
        font-weight: 800;
        line-height: 1.05;
    }

    .price-original {
        color: #94a3b8;
        font-size: 16px;
        text-decoration: line-through;
    }

    .price-meta {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 12px;
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
        border: 1px solid #dbeafe;
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

    .product-footer {
        margin-top: 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .detail-product-footer {
        justify-content: flex-start;
    }

    .product-actions-form {
        display: flex;
    }

    .btn-group-modern {
        display: flex;
        gap: 6px;
    }

    .detail-btn-group {
        width: 100%;
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        gap: 10px;
    }

    .btn-cart-modern,
    .btn-buy-modern {
        border-radius: 8px;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-cart-modern {
        width: 38px;
        height: 38px;
        border: 2px solid #1D4ED8;
        background: transparent;
        color: #1D4ED8;
    }

    .btn-buy-modern {
        padding: 0 20px;
        height: 38px;
        background: linear-gradient(135deg, #1D4ED8 0%, #60A5FA 100%);
        color: #fff;
        font-weight: 600;
        font-size: 13px;
    }

    .btn-cart-modern:hover {
        background: #1D4ED8;
        color: #fff;
    }

    .btn-buy-modern:hover {
        background: linear-gradient(135deg, #1E40AF 0%, #3B82F6 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 102, 255, 0.3);
    }

    .detail-cart-btn,
    .detail-buy-btn {
        width: 100%;
        height: 52px;
        border-radius: 14px;
        font-size: 15px;
        font-weight: 700;
        gap: 8px;
    }

    .detail-cart-btn {
        background: #fff;
        border: 2px solid #1D4ED8;
        color: #1D4ED8;
    }

    .detail-buy-btn {
        flex: 1;
    }

    .detail-cart-btn:hover {
        background: #1D4ED8;
        color: #fff;
    }

    .detail-view-cart {
        margin-top: 12px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #334155;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
    }

    .detail-view-cart:hover {
        color: #1D4ED8;
    }

    .btn-cart-modern:disabled,
    .btn-buy-modern:disabled {
        background: #cbd5e1;
        color: #64748b;
        border-color: #cbd5e1;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .summary-assurance {
        display: grid;
        gap: 10px;
        margin-top: 20px;
        padding-top: 18px;
        border-top: 1px solid #edf2f7;
    }

    .assurance-item {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #334155;
        font-size: 14px;
        font-weight: 500;
    }

    .product-detail-sections {
        margin-bottom: 28px;
    }

    .info-card {
        background: #fff;
        border-radius: 20px;
        border: 1px solid #eef2f7;
        box-shadow: 0 12px 32px rgba(15, 23, 42, 0.05);
        padding: 24px;
        height: 100%;
    }

    .section-heading {
        margin-bottom: 16px;
    }

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
        background: rgba(37, 99, 235, 0.1);
        color: #1d4ed8;
        margin-bottom: 10px;
    }

    .section-heading h2 {
        color: #0f172a;
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 0;
    }

    .info-copy {
        color: #475569;
        line-height: 1.8;
        margin-bottom: 0;
    }

    .info-copy p:last-child {
        margin-bottom: 0;
    }

    .info-chip-list {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 18px;
    }

    .info-chip-list span {
        background: #f8fbff;
        border: 1px solid #dbeafe;
        color: #1e3a8a;
        border-radius: 999px;
        padding: 9px 14px;
        font-size: 13px;
        font-weight: 600;
    }

    .compact-feature-list {
        margin-top: 0;
    }

    .related-products-section {
        margin-top: 0;
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

    .product-details {
        padding: 16px;
        flex: 1;
        display: flex;
        flex-direction: column;
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

    @media (max-width: 1199px) {
        .summary-head h1 {
            font-size: 32px;
        }
    }

    @media (max-width: 991px) {
        .product-single-page {
            padding-top: 18px;
        }

        .detail-main-image {
            height: 340px;
        }

        .detail-trust-grid {
            grid-template-columns: 1fr;
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
        .product-summary-card,
        .product-gallery-shell,
        .info-card,
        .product-card-modern {
            border-radius: 20px;
        }

        .summary-status-grid,
        .detail-btn-group {
            grid-template-columns: 1fr;
        }

        .detail-btn-group {
            display: grid;
        }

        .product-thumb {
            width: 68px;
            height: 68px;
            border-radius: 16px;
        }

        .detail-main-image {
            height: 280px;
        }

        .detail-cart-btn {
            width: 100%;
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

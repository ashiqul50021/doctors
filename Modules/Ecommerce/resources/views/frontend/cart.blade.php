@extends('layouts.app')

@section('title', 'Shopping Cart - abcsheba.com')

@section('content')
<div class="content cart-page-modern">
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
        @endif

        @if(count($cart) > 0)
            <section class="cart-hero">
                <div class="cart-hero-copy">
                    <span class="cart-eyebrow">Checkout Ready</span>
                    <h1>Review your cart before checkout.</h1>
                    <p>Everything is organized for a faster purchase flow with free shipping and secure payment.</p>
                </div>
                <div class="cart-hero-stats">
                    <div class="hero-stat">
                        <span class="hero-stat-icon"><i class="fas fa-shopping-bag"></i></span>
                        <div class="hero-stat-copy">
                            <strong id="cart-item-stat">{{ count($cart) }} item{{ count($cart) > 1 ? 's' : '' }}</strong>
                            <span>ready in your cart</span>
                        </div>
                    </div>
                    <div class="hero-stat">
                        <span class="hero-stat-icon"><i class="fas fa-shield-alt"></i></span>
                        <div class="hero-stat-copy">
                            <strong>100% secure</strong>
                            <span>protected checkout</span>
                        </div>
                    </div>
                    <div class="hero-stat">
                        <span class="hero-stat-icon"><i class="fas fa-truck"></i></span>
                        <div class="hero-stat-copy">
                            <strong>Free shipping</strong>
                            <span>no extra charge today</span>
                        </div>
                    </div>
                </div>
            </section>

            <div class="row g-4 align-items-start">
                <div class="col-lg-8">
                    <div class="cart-card cart-items-card">
                        <div class="cart-card-header cart-card-header-lg">
                            <div>
                                <span class="cart-section-kicker">Selected Products</span>
                                <h4>Cart Items (<span id="cart-item-count">{{ count($cart) }}</span>)</h4>
                                <p>Adjust quantity, remove items, or continue shopping before placing your order.</p>
                            </div>
                            <a href="{{ route('ecommerce.products') }}" class="cart-inline-link">
                                <i class="fas fa-arrow-left"></i>
                                Continue Shopping
                            </a>
                        </div>

                        <div class="table-responsive">
                            <table class="table cart-table-modern align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Subtotal</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cart as $productId => $item)
                                        @php
                                            $imagePath = $item['image'] ?? null;
                                            $imageUrl = $imagePath
                                                ? (\Illuminate\Support\Str::startsWith($imagePath, ['http://', 'https://']) ? $imagePath : asset($imagePath))
                                                : asset('assets/img/products/product-1.jpg');
                                        @endphp
                                        <tr id="row-{{ $productId }}">
                                            <td data-label="Product">
                                                <div class="cart-product-cell">
                                                    <div class="cart-product-image-wrap">
                                                        <img src="{{ $imageUrl }}"
                                                            alt="{{ $item['name'] }}"
                                                            class="cart-product-image"
                                                            onerror="this.onerror=null;this.src='{{ asset('assets/img/products/product-1.jpg') }}';">
                                                    </div>
                                                    <div class="cart-product-content">
                                                        <span class="cart-product-chip">Ready to order</span>
                                                        <a href="{{ route('ecommerce.products.show', $productId) }}" class="cart-product-link">
                                                            <h6>{{ $item['name'] }}</h6>
                                                        </a>
                                                        <p>Premium healthcare essentials delivered with a smooth checkout experience.</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td data-label="Price">
                                                <div class="cart-price-stack">
                                                    <span class="cart-meta-label">Unit price</span>
                                                    <span class="cart-price">৳{{ number_format($item['price'], 2) }}</span>
                                                </div>
                                            </td>
                                            <td data-label="Quantity">
                                                <div class="cart-qty-wrap">
                                                    <div class="cart-qty-control">
                                                        <button type="button" class="qty-btn btn-minus" data-id="{{ $productId }}" aria-label="Decrease quantity">-</button>
                                                        <input type="number"
                                                            class="qty-input"
                                                            data-id="{{ $productId }}"
                                                            value="{{ $item['quantity'] }}"
                                                            min="1">
                                                        <button type="button" class="qty-btn btn-plus" data-id="{{ $productId }}" aria-label="Increase quantity">+</button>
                                                    </div>
                                                    <span class="cart-qty-note">Update instantly</span>
                                                </div>
                                            </td>
                                            <td data-label="Subtotal">
                                                <div class="cart-price-stack">
                                                    <span class="cart-meta-label">Line total</span>
                                                    <span class="cart-subtotal" id="subtotal-{{ $productId }}">৳{{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                                                </div>
                                            </td>
                                            <td data-label="Action" class="text-end">
                                                <button type="button" class="remove-cart-btn" onclick="removeFromCart('{{ $productId }}')" aria-label="Remove item">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="cart-card cart-summary-card">
                        <div class="summary-top">
                            <span class="summary-badge">Secure Checkout</span>
                            <h4>Order Summary</h4>
                            <p>Clear totals, free shipping, and one step away from finishing the purchase.</p>
                        </div>

                        <div class="summary-body">
                            <div class="summary-highlight">
                                <span>You are saving on delivery</span>
                                <strong>৳0.00</strong>
                            </div>

                            <div class="summary-line">
                                <span>Subtotal</span>
                                <strong id="cart-subtotal">৳{{ number_format($total, 2) }}</strong>
                            </div>
                            <div class="summary-line">
                                <span>Shipping</span>
                                <span class="summary-free">Free</span>
                            </div>
                            <div class="summary-divider"></div>
                            <div class="summary-line total-line">
                                <span>Total</span>
                                <strong id="cart-total">৳{{ number_format($total, 2) }}</strong>
                            </div>

                            <a href="{{ route('ecommerce.checkout') }}" class="cart-btn cart-btn-primary">
                                <i class="fas fa-credit-card"></i>
                                Proceed To Checkout
                            </a>
                            <a href="{{ route('ecommerce.products') }}" class="cart-btn cart-btn-secondary">
                                <i class="fas fa-arrow-left"></i>
                                Continue Shopping
                            </a>

                            <div class="summary-perks">
                                <div class="summary-perk">
                                    <i class="fas fa-lock"></i>
                                    <span>Encrypted payment flow</span>
                                </div>
                                <div class="summary-perk">
                                    <i class="fas fa-headset"></i>
                                    <span>Support available when needed</span>
                                </div>
                                <div class="summary-perk">
                                    <i class="fas fa-bolt"></i>
                                    <span>Fast quantity and total updates</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="cart-card empty-cart-state">
                <div class="empty-cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <span class="cart-eyebrow">Cart Empty</span>
                <h3>Your cart is waiting for products.</h3>
                <p>Browse the catalog and add healthcare products you need. Once added, they will appear here instantly.</p>
                <a href="{{ route('ecommerce.products') }}" class="cart-btn cart-btn-primary cart-btn-inline">
                    <i class="fas fa-shopping-bag"></i>
                    Browse Products
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .cart-page-modern {
        position: relative;
        overflow: hidden;
        background:
            radial-gradient(circle at top left, rgba(14, 165, 233, 0.16), transparent 34%),
            radial-gradient(circle at right 12%, rgba(37, 99, 235, 0.12), transparent 30%),
            linear-gradient(180deg, #f4f8ff 0%, #ffffff 44%, #f6fbff 100%);
        padding: 42px 0 84px;
    }

    .cart-hero {
        display: grid;
        grid-template-columns: minmax(0, 1.4fr) minmax(0, 1fr);
        gap: 28px;
        align-items: end;
        margin-bottom: 28px;
    }

    .cart-eyebrow,
    .cart-section-kicker,
    .summary-badge,
    .cart-product-chip {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: fit-content;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .cart-eyebrow,
    .cart-section-kicker {
        padding: 8px 12px;
        background: rgba(37, 99, 235, 0.08);
        color: #1d4ed8;
        margin-bottom: 14px;
    }

    .cart-hero-copy h1 {
        margin: 0 0 12px;
        font-size: clamp(32px, 5vw, 52px);
        line-height: 1.05;
        font-weight: 800;
        color: #0f172a;
        max-width: 10ch;
    }

    .cart-hero-copy p {
        margin: 0;
        max-width: 560px;
        font-size: 17px;
        line-height: 1.8;
        color: #475569;
    }

    .cart-hero-stats {
        display: grid;
        gap: 14px;
    }

    .hero-stat {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 18px 20px;
        background: rgba(255, 255, 255, 0.82);
        border: 1px solid rgba(191, 219, 254, 0.85);
        border-radius: 22px;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.06);
        backdrop-filter: blur(10px);
    }

    .hero-stat-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 46px;
        height: 46px;
        border-radius: 16px;
        background: linear-gradient(135deg, #2563eb, #0ea5e9);
        color: #fff;
        font-size: 18px;
        box-shadow: 0 14px 30px rgba(37, 99, 235, 0.22);
    }

    .hero-stat-copy strong,
    .hero-stat-copy span {
        display: block;
    }

    .hero-stat-copy strong {
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
    }

    .hero-stat-copy span {
        font-size: 14px;
        color: #64748b;
    }

    .cart-card {
        background: rgba(255, 255, 255, 0.92);
        border: 1px solid rgba(226, 232, 240, 0.95);
        border-radius: 28px;
        box-shadow: 0 22px 60px rgba(15, 23, 42, 0.08);
        overflow: hidden;
        backdrop-filter: blur(12px);
    }

    .cart-items-card {
        position: relative;
    }

    .cart-items-card::before {
        content: "";
        position: absolute;
        inset: 0 auto auto 0;
        width: 160px;
        height: 160px;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.14), transparent 70%);
        pointer-events: none;
    }

    .cart-card-header {
        padding: 28px 30px 24px;
        border-bottom: 1px solid #edf2f7;
    }

    .cart-card-header-lg {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 18px;
    }

    .cart-card-header h4,
    .summary-top h4 {
        margin: 0;
        font-size: 30px;
        line-height: 1.15;
        font-weight: 800;
        color: #0f172a;
    }

    .cart-card-header p,
    .summary-top p {
        margin: 10px 0 0;
        font-size: 15px;
        line-height: 1.75;
        color: #64748b;
    }

    .cart-inline-link {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        color: #1d4ed8;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
        white-space: nowrap;
    }

    .cart-inline-link:hover,
    .cart-product-link:hover {
        color: #0f172a;
    }

    .cart-table-modern {
        margin: 0;
        border-collapse: separate;
        border-spacing: 0 16px;
        min-width: 820px;
    }

    .cart-table-modern thead th {
        padding: 0 30px 2px;
        border: 0;
        color: #334155;
        font-size: 13px;
        font-weight: 800;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .cart-table-modern tbody td {
        padding: 24px 30px;
        border: 0;
        vertical-align: middle;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(248, 250, 252, 0.96));
        box-shadow: inset 0 0 0 1px rgba(226, 232, 240, 0.9);
    }

    .cart-table-modern tbody td:first-child {
        border-radius: 24px 0 0 24px;
    }

    .cart-table-modern tbody td:last-child {
        border-radius: 0 24px 24px 0;
    }

    .cart-product-cell {
        display: flex;
        align-items: center;
        gap: 18px;
        min-width: 280px;
    }

    .cart-product-image-wrap {
        position: relative;
        width: 92px;
        height: 92px;
        padding: 6px;
        border-radius: 24px;
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.12), rgba(14, 165, 233, 0.04));
        flex-shrink: 0;
    }

    .cart-product-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 18px;
        background: #fff;
        border: 1px solid rgba(226, 232, 240, 0.9);
    }

    .cart-product-content {
        min-width: 0;
    }

    .cart-product-chip {
        padding: 6px 10px;
        background: rgba(16, 185, 129, 0.12);
        color: #059669;
        margin-bottom: 10px;
    }

    .cart-product-link {
        color: inherit;
        text-decoration: none;
    }

    .cart-product-cell h6 {
        margin: 0 0 8px;
        font-size: 20px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.35;
    }

    .cart-product-content p {
        margin: 0;
        max-width: 320px;
        font-size: 14px;
        line-height: 1.7;
        color: #64748b;
    }

    .cart-price-stack {
        display: flex;
        flex-direction: column;
        gap: 6px;
        white-space: nowrap;
    }

    .cart-meta-label,
    .cart-qty-note {
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
    }

    .cart-price,
    .cart-subtotal {
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
        white-space: nowrap;
    }

    .cart-qty-wrap {
        display: inline-flex;
        flex-direction: column;
        gap: 10px;
    }

    .cart-qty-control {
        display: inline-flex;
        align-items: center;
        gap: 14px;
        min-width: 174px;
        padding: 10px 14px;
        border: 1px solid #dbe5f3;
        border-radius: 20px;
        background: linear-gradient(180deg, #f8fbff 0%, #eef6ff 100%);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.85);
    }

    .qty-btn {
        width: 38px;
        height: 38px;
        border: 0;
        border-radius: 12px;
        background: #ffffff;
        color: #1d4ed8;
        font-size: 24px;
        font-weight: 700;
        line-height: 1;
        box-shadow: 0 10px 24px rgba(37, 99, 235, 0.12);
        transition: 0.2s ease;
    }

    .qty-btn:hover {
        background: #1d4ed8;
        color: #fff;
        transform: translateY(-1px);
    }

    .qty-input {
        width: 50px;
        border: 0;
        background: transparent;
        text-align: center;
        font-size: 22px;
        font-weight: 800;
        color: #0f172a;
        padding: 0;
        box-shadow: none;
    }

    .qty-input:focus {
        outline: none;
    }

    .qty-input::-webkit-outer-spin-button,
    .qty-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .remove-cart-btn {
        width: 46px;
        height: 46px;
        border: 0;
        border-radius: 14px;
        background: linear-gradient(180deg, #fb7185 0%, #ef4444 100%);
        color: #fff;
        font-size: 16px;
        box-shadow: 0 16px 28px rgba(239, 68, 68, 0.22);
        transition: 0.2s ease;
    }

    .remove-cart-btn:hover {
        background: linear-gradient(180deg, #ef4444 0%, #dc2626 100%);
        transform: translateY(-1px);
    }

    .cart-summary-card {
        position: sticky;
        top: 104px;
    }

    .summary-top {
        padding: 28px 28px 24px;
        color: #fff;
        background: linear-gradient(160deg, #0f172a 0%, #1d4ed8 55%, #38bdf8 100%);
    }

    .summary-badge {
        padding: 7px 12px;
        background: rgba(255, 255, 255, 0.16);
        color: #fff;
        margin-bottom: 14px;
    }

    .summary-top h4,
    .summary-top p {
        color: #fff;
    }

    .summary-top p {
        opacity: 0.84;
    }

    .summary-body {
        padding: 24px 28px 28px;
    }

    .summary-highlight {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 18px 20px;
        margin-bottom: 22px;
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(52, 211, 153, 0.04));
        border: 1px solid rgba(16, 185, 129, 0.18);
        border-radius: 20px;
    }

    .summary-highlight span {
        font-size: 14px;
        font-weight: 700;
        color: #065f46;
    }

    .summary-highlight strong {
        font-size: 22px;
        font-weight: 800;
        color: #059669;
    }

    .summary-line {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
        color: #475569;
        font-size: 16px;
        font-weight: 600;
    }

    .summary-line strong {
        color: #0f172a;
        font-size: 24px;
        font-weight: 800;
    }

    .summary-free {
        font-size: 16px;
        font-weight: 800;
        color: #10b981;
    }

    .summary-divider {
        height: 1px;
        margin: 20px 0;
        background: linear-gradient(90deg, rgba(148, 163, 184, 0.15), rgba(148, 163, 184, 0.75), rgba(148, 163, 184, 0.15));
    }

    .total-line {
        margin-bottom: 24px;
        padding-top: 2px;
    }

    .total-line strong {
        color: #059669;
    }

    .cart-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        border-radius: 18px;
        padding: 16px 18px;
        text-decoration: none;
        font-size: 16px;
        font-weight: 800;
        transition: 0.2s ease;
    }

    .cart-btn-primary {
        color: #fff;
        background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 55%, #0ea5e9 100%);
        box-shadow: 0 18px 32px rgba(37, 99, 235, 0.22);
    }

    .cart-btn-primary:hover {
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 20px 36px rgba(37, 99, 235, 0.28);
    }

    .cart-btn-secondary {
        margin-top: 12px;
        color: #334155;
        background: #fff;
        border: 1px solid #dbe5f3;
    }

    .cart-btn-secondary:hover {
        color: #1d4ed8;
        border-color: rgba(37, 99, 235, 0.35);
        background: #f8fbff;
    }

    .summary-perks {
        display: grid;
        gap: 12px;
        margin-top: 22px;
    }

    .summary-perk {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 14px;
        font-weight: 600;
        color: #475569;
    }

    .summary-perk i {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: 12px;
        background: #eff6ff;
        color: #2563eb;
        flex-shrink: 0;
    }

    .empty-cart-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding: 54px 28px;
    }

    .empty-cart-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 96px;
        height: 96px;
        margin-bottom: 20px;
        border-radius: 28px;
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.16), rgba(14, 165, 233, 0.08));
        color: #1d4ed8;
        font-size: 34px;
    }

    .empty-cart-state h3 {
        margin: 0 0 12px;
        font-size: 34px;
        line-height: 1.15;
        font-weight: 800;
        color: #0f172a;
    }

    .empty-cart-state p {
        max-width: 560px;
        margin: 0 0 24px;
        font-size: 16px;
        line-height: 1.8;
        color: #64748b;
    }

    .cart-btn-inline {
        width: auto;
        min-width: 220px;
    }

    @media (max-width: 1199.98px) {
        .cart-hero {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 991.98px) {
        .cart-page-modern {
            padding: 28px 0 64px;
        }

        .cart-card-header-lg {
            flex-direction: column;
            align-items: flex-start;
        }

        .cart-card-header h4,
        .summary-top h4 {
            font-size: 26px;
        }

        .cart-summary-card {
            position: static;
        }

        .cart-table-modern thead th {
            padding-left: 20px;
            padding-right: 20px;
        }

        .cart-table-modern tbody td {
            padding: 20px;
        }
    }

    @media (max-width: 767.98px) {
        .cart-page-modern {
            padding: 22px 0 52px;
        }

        .cart-hero {
            gap: 20px;
            margin-bottom: 20px;
        }

        .cart-hero-copy h1 {
            font-size: 34px;
            max-width: none;
        }

        .hero-stat {
            padding: 16px 18px;
            border-radius: 18px;
        }

        .cart-card {
            border-radius: 24px;
        }

        .cart-card-header,
        .summary-top,
        .summary-body {
            padding-left: 20px;
            padding-right: 20px;
        }

        .cart-table-modern,
        .cart-table-modern tbody,
        .cart-table-modern tr,
        .cart-table-modern td {
            display: block;
            width: 100%;
        }

        .cart-table-modern {
            min-width: 0;
            border-spacing: 0;
        }

        .cart-table-modern thead {
            display: none;
        }

        .cart-table-modern tbody tr {
            margin: 0 16px 16px;
        }

        .cart-table-modern tbody td {
            padding: 0 18px 18px;
            background: #fff;
            box-shadow: inset 0 0 0 1px rgba(226, 232, 240, 0.9);
        }

        .cart-table-modern tbody td:first-child {
            padding-top: 18px;
            border-radius: 22px 22px 0 0;
        }

        .cart-table-modern tbody td:last-child {
            padding-bottom: 18px;
            border-radius: 0 0 22px 22px;
        }

        .cart-table-modern tbody td:not(:first-child) {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
        }

        .cart-table-modern tbody td:not(:first-child)::before {
            content: attr(data-label);
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #64748b;
        }

        .cart-product-cell {
            min-width: 0;
            align-items: flex-start;
        }

        .cart-product-image-wrap {
            width: 82px;
            height: 82px;
            border-radius: 22px;
        }

        .cart-product-cell h6 {
            font-size: 18px;
        }

        .cart-product-content p {
            font-size: 13px;
        }

        .cart-price,
        .cart-subtotal {
            font-size: 20px;
            text-align: right;
        }

        .cart-qty-wrap {
            align-items: flex-end;
        }

        .cart-qty-control {
            min-width: 154px;
            gap: 10px;
            padding: 8px 10px;
        }

        .qty-btn {
            width: 32px;
            height: 32px;
            font-size: 20px;
        }

        .qty-input {
            width: 40px;
            font-size: 19px;
        }

        .remove-cart-btn {
            width: 40px;
            height: 40px;
        }

        .empty-cart-state {
            padding: 42px 22px;
        }

        .empty-cart-state h3 {
            font-size: 28px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function formatCurrency(amount) {
        return '৳' + Number(amount).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function syncCartBadge(cartCount) {
        var badge = $('#cart-icon-btn .cart-badge');

        if (cartCount > 0) {
            if (badge.length) {
                badge.text(cartCount);
            } else {
                $('#cart-icon-btn').append('<span class="cart-badge">' + cartCount + '</span>');
            }
        } else {
            badge.remove();
        }
    }

    function syncCartItemCount() {
        var itemCount = $('.cart-table-modern tbody tr').length;
        $('#cart-item-count').text(itemCount);
        $('#cart-item-stat').text(itemCount + ' item' + (itemCount === 1 ? '' : 's'));
    }

    function updateCart(productId, quantity, $input) {
        var finalQuantity = Math.max(1, parseInt(quantity || 1, 10));
        $input.val(finalQuantity);

        $.ajax({
            url: '{{ route('ecommerce.cart.update') }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                product_id: productId,
                quantity: finalQuantity
            },
            success: function (response) {
                if (!response.success) {
                    toastr.error('Failed to update cart.');
                    return;
                }

                $('#subtotal-' + productId).text(formatCurrency(response.itemSubtotal));
                $('#cart-subtotal').text(formatCurrency(response.total));
                $('#cart-total').text(formatCurrency(response.total));
                syncCartBadge(response.cartCount);
            },
            error: function () {
                toastr.error('Failed to update cart.');
            }
        });
    }

    function removeFromCart(productId) {
        $.ajax({
            url: '{{ route('ecommerce.cart.remove') }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                product_id: productId
            },
            success: function (response) {
                if (!response.success) {
                    toastr.error('Failed to remove item.');
                    return;
                }

                $('#row-' + productId).fadeOut(200, function () {
                    $(this).remove();

                    if (!$('.cart-table-modern tbody tr').length) {
                        location.reload();
                        return;
                    }

                    $('#cart-subtotal').text(formatCurrency(response.total));
                    $('#cart-total').text(formatCurrency(response.total));
                    syncCartBadge(response.cartCount);
                    syncCartItemCount();
                });
            },
            error: function () {
                toastr.error('Failed to remove item.');
            }
        });
    }

    $(document).ready(function () {
        $('.btn-plus').on('click', function () {
            var productId = $(this).data('id');
            var $input = $('.qty-input[data-id="' + productId + '"]');
            updateCart(productId, parseInt($input.val() || 1, 10) + 1, $input);
        });

        $('.btn-minus').on('click', function () {
            var productId = $(this).data('id');
            var $input = $('.qty-input[data-id="' + productId + '"]');
            var currentValue = parseInt($input.val() || 1, 10);

            if (currentValue > 1) {
                updateCart(productId, currentValue - 1, $input);
            }
        });

        $('.qty-input').on('change', function () {
            var $input = $(this);
            updateCart($input.data('id'), $input.val(), $input);
        });
    });
</script>
@endpush

@extends('layouts.app')

@section('title', 'Shopping Cart - ' . ($siteSettings['site_name'] ?? 'abcsheba.com'))

@push('styles')
<style>
    .cart-page {
        background: #f8fafc;
        min-height: 80vh;
        padding: 104px 0 60px;
    }

    .cart-page-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 28px;
    }

    .cart-page-header h2 {
        margin: 0;
        font-size: 34px;
        font-weight: 700;
        color: #1f2937;
        line-height: 1.2;
    }

    .cart-page-header p {
        margin: 10px 0 0;
        color: #6b7280;
        font-size: 15px;
        line-height: 1.7;
        max-width: 680px;
    }

    .cart-page-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }

    .cart-meta-pill {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 10px 16px;
        border-radius: 999px;
        background: #fff;
        border: 1px solid #e5e7eb;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.05);
        color: #475569;
        font-size: 14px;
        font-weight: 600;
        white-space: nowrap;
    }

    .cart-meta-pill i {
        color: #1d4ed8;
    }

    .cart-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        box-shadow: 0 16px 40px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }

    .cart-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 22px 24px;
        border-bottom: 1px solid #eef2f7;
    }

    .cart-card-title {
        margin: 0;
        font-size: 24px;
        font-weight: 700;
        color: #111827;
        line-height: 1.2;
    }

    .cart-card-subtitle {
        margin: 8px 0 0;
        color: #6b7280;
        font-size: 15px;
        line-height: 1.7;
    }

    .cart-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #1d4ed8;
        font-weight: 600;
        text-decoration: none;
        white-space: nowrap;
    }

    .cart-link:hover {
        color: #1e40af;
        text-decoration: none;
    }

    .cart-table {
        margin-bottom: 0;
    }

    .cart-table thead th {
        padding: 14px 24px;
        background: #f8fafc;
        border-bottom: 1px solid #eef2f7;
        color: #64748b;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .cart-table tbody td {
        padding: 22px 24px;
        vertical-align: middle;
        border-color: #eef2f7;
    }

    .cart-product {
        display: flex;
        align-items: center;
        gap: 14px;
        min-width: 240px;
    }

    .cart-product-image {
        width: 84px;
        height: 84px;
        object-fit: cover;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        background: #f8fafc;
        flex-shrink: 0;
    }

    .cart-product-name {
        display: inline-block;
        margin-bottom: 6px;
        color: #111827;
        font-size: 18px;
        font-weight: 700;
        line-height: 1.4;
        text-decoration: none;
    }

    .cart-product-name:hover {
        color: #1d4ed8;
        text-decoration: none;
    }

    .cart-product-meta {
        margin: 0;
        color: #6b7280;
        font-size: 14px;
        line-height: 1.7;
    }

    .cart-price,
    .cart-subtotal {
        color: #111827;
        font-size: 18px;
        font-weight: 700;
        white-space: nowrap;
    }

    .qty-control {
        display: inline-flex;
        align-items: center;
        border: 1px solid #dbe5f3;
        border-radius: 10px;
        overflow: hidden;
        background: #fff;
    }

    .qty-btn {
        width: 38px;
        height: 38px;
        border: 0;
        background: #f8fafc;
        color: #1d4ed8;
        font-size: 18px;
        font-weight: 700;
        transition: all 0.2s ease;
    }

    .qty-btn:hover {
        background: #e8efff;
        color: #1e40af;
    }

    .qty-input {
        width: 54px;
        height: 38px;
        border: 0;
        text-align: center;
        color: #111827;
        font-weight: 700;
        background: #fff;
        -moz-appearance: textfield;
    }

    .qty-input::-webkit-inner-spin-button,
    .qty-input::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .remove-btn {
        width: 40px;
        height: 40px;
        border: 1px solid #fecaca;
        border-radius: 10px;
        background: #fff5f5;
        color: #dc2626;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .remove-btn:hover {
        background: #dc2626;
        border-color: #dc2626;
        color: #fff;
    }

    .cart-summary-card {
        padding: 24px;
        position: sticky;
        top: 118px;
    }

    .cart-summary-card h4 {
        margin: 0;
        color: #111827;
        font-size: 24px;
        font-weight: 700;
    }

    .cart-summary-card p {
        margin: 10px 0 0;
        color: #6b7280;
        font-size: 15px;
        line-height: 1.7;
    }

    .summary-list {
        margin-top: 22px;
        padding-top: 22px;
        border-top: 1px solid #eef2f7;
    }

    .summary-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 14px;
        color: #475569;
        font-size: 16px;
    }

    .summary-row strong {
        color: #111827;
        font-weight: 700;
    }

    .summary-row.total {
        margin-top: 18px;
        padding-top: 18px;
        border-top: 2px dashed #e5e7eb;
        font-size: 22px;
        font-weight: 700;
        color: #111827;
    }

    .summary-row.total strong {
        color: #1d4ed8;
        font-size: 24px;
    }

    .summary-free {
        color: #059669;
        font-weight: 700;
    }

    .summary-benefits {
        display: grid;
        gap: 10px;
        margin: 20px 0 24px;
    }

    .summary-benefit {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #6b7280;
        font-size: 14px;
    }

    .summary-benefit i {
        color: #1d4ed8;
    }

    .cart-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        padding: 13px 16px;
        border-radius: 10px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .cart-btn:hover {
        text-decoration: none;
    }

    .cart-btn-primary {
        background: #1d4ed8;
        border: 1px solid #1d4ed8;
        color: #fff;
    }

    .cart-btn-primary:hover {
        background: #1e40af;
        border-color: #1e40af;
        color: #fff;
    }

    .cart-btn-secondary {
        margin-top: 12px;
        background: #fff;
        border: 1px solid #cbd5e1;
        color: #1d4ed8;
    }

    .cart-btn-secondary:hover {
        background: #f8fafc;
        color: #1e40af;
    }

    .empty-cart-card {
        padding: 64px 24px;
        text-align: center;
    }

    .empty-cart-icon {
        width: 96px;
        height: 96px;
        margin: 0 auto 24px;
        border-radius: 50%;
        background: #eff6ff;
        color: #1d4ed8;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 34px;
    }

    .empty-cart-card h3 {
        margin-bottom: 12px;
        color: #111827;
        font-size: 30px;
        font-weight: 700;
    }

    .empty-cart-card p {
        max-width: 520px;
        margin: 0 auto 24px;
        color: #6b7280;
        font-size: 16px;
        line-height: 1.8;
    }

    @media (max-width: 991.98px) {
        .cart-page {
            padding-top: 92px;
        }

        .cart-page-header,
        .cart-card-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .cart-summary-card {
            position: static;
        }
    }

    @media (max-width: 575.98px) {
        .cart-page-header h2 {
            font-size: 28px;
        }

        .cart-card-title,
        .cart-summary-card h4 {
            font-size: 22px;
        }

        .cart-table thead th,
        .cart-table tbody td,
        .cart-card-header,
        .cart-summary-card {
            padding-left: 18px;
            padding-right: 18px;
        }

        .cart-product {
            min-width: 220px;
        }

        .cart-product-image {
            width: 72px;
            height: 72px;
        }

        .cart-product-name {
            font-size: 16px;
        }
    }
</style>
@endpush

@section('content')
@php
    $productImageFallback = asset('assets/img/img-01.jpg');
@endphp

<div class="cart-page">
    <div class="container">
        <div id="alert-container">
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>

        @if(count($cart) > 0)
            <div class="cart-page-header">
                <div>
                    <h2>Shopping Cart</h2>
                    <p>Review your selected products, update quantities, and continue to checkout using the same clean style as the rest of the site.</p>
                </div>
                <div class="cart-page-meta">
                    <div class="cart-meta-pill">
                        <i class="fas fa-shopping-bag"></i>
                        <span><span class="js-cart-count">{{ count($cart) }}</span> item{{ count($cart) > 1 ? 's' : '' }} in cart</span>
                    </div>
                    <div class="cart-meta-pill">
                        <i class="fas fa-truck"></i>
                        <span>Free shipping</span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="cart-card mb-4 mb-lg-0">
                        <div class="cart-card-header">
                            <div>
                                <h4 class="cart-card-title">Cart Items (<span class="js-cart-count">{{ count($cart) }}</span>)</h4>
                                <p class="cart-card-subtitle">Adjust quantity or remove products before proceeding to checkout.</p>
                            </div>
                            <a href="{{ route('ecommerce.products') }}" class="cart-link">
                                <i class="fas fa-arrow-left"></i>
                                Continue Shopping
                            </a>
                        </div>

                        <div class="table-responsive">
                            <table class="table cart-table align-middle">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Subtotal</th>
                                        <th class="text-end">Remove</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cart as $productId => $item)
                                        @php
                                            $imagePath = $item['image'] ?? null;
                                            $imageUrl = $imagePath
                                                ? (\Illuminate\Support\Str::startsWith($imagePath, ['http://', 'https://']) ? $imagePath : asset($imagePath))
                                                : $productImageFallback;
                                        @endphp
                                        <tr id="row-{{ $productId }}">
                                            <td>
                                                <div class="cart-product">
                                                    <img
                                                        src="{{ $imageUrl }}"
                                                        alt="{{ $item['name'] }}"
                                                        class="cart-product-image"
                                                        onerror="this.onerror=null;this.src='{{ $productImageFallback }}';">
                                                    <div>
                                                        <a href="{{ route('ecommerce.products.show', $productId) }}" class="cart-product-name">
                                                            {{ $item['name'] }}
                                                        </a>
                                                        <p class="cart-product-meta mb-0">
                                                            Product ID: #{{ $productId }}<br>
                                                            Ready for checkout
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="cart-price">৳{{ number_format($item['price'], 2) }}</span>
                                            </td>
                                            <td>
                                                <div class="qty-control">
                                                    <button type="button" class="qty-btn btn-minus" data-id="{{ $productId }}" aria-label="Decrease quantity">-</button>
                                                    <input
                                                        type="number"
                                                        class="qty-input"
                                                        data-id="{{ $productId }}"
                                                        data-last-valid="{{ $item['quantity'] }}"
                                                        value="{{ $item['quantity'] }}"
                                                        min="1"
                                                        aria-label="Quantity for {{ $item['name'] }}">
                                                    <button type="button" class="qty-btn btn-plus" data-id="{{ $productId }}" aria-label="Increase quantity">+</button>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="cart-subtotal" id="subtotal-{{ $productId }}">
                                                    ৳{{ number_format($item['price'] * $item['quantity'], 2) }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <button type="button" class="remove-btn" onclick="removeFromCart('{{ $productId }}')" aria-label="Remove {{ $item['name'] }}">
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
                        <h4>Order Summary</h4>
                        <p>Simple totals, no surprise shipping fees, and a direct path to checkout.</p>

                        <div class="summary-list">
                            <div class="summary-row">
                                <span>Subtotal</span>
                                <strong id="cart-subtotal">৳{{ number_format($total, 2) }}</strong>
                            </div>
                            <div class="summary-row">
                                <span>Shipping</span>
                                <span class="summary-free">Free</span>
                            </div>
                            <div class="summary-row total">
                                <span>Total</span>
                                <strong id="cart-total">৳{{ number_format($total, 2) }}</strong>
                            </div>
                        </div>

                        <div class="summary-benefits">
                            <div class="summary-benefit">
                                <i class="fas fa-lock"></i>
                                <span>Secure checkout flow</span>
                            </div>
                            <div class="summary-benefit">
                                <i class="fas fa-bolt"></i>
                                <span>Instant cart updates</span>
                            </div>
                            <div class="summary-benefit">
                                <i class="fas fa-headset"></i>
                                <span>Support available if needed</span>
                            </div>
                        </div>

                        <a href="{{ route('ecommerce.checkout') }}" class="cart-btn cart-btn-primary">
                            <i class="fas fa-credit-card"></i>
                            Proceed To Checkout
                        </a>
                        <a href="{{ route('ecommerce.products') }}" class="cart-btn cart-btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                            Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="cart-card empty-cart-card">
                <div class="empty-cart-icon">
                    <i class="fas fa-shopping-basket"></i>
                </div>
                <h3>Your cart is empty</h3>
                <p>Browse products and add the items you need. Once selected, they will appear here with quick quantity controls and a checkout summary.</p>
                <a href="{{ route('ecommerce.products') }}" class="cart-btn cart-btn-primary" style="max-width: 260px; margin: 0 auto;">
                    <i class="fas fa-shopping-bag"></i>
                    Browse Products
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function formatCurrency(amount) {
        return '৳' + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
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

    function syncCartCount(cartCount) {
        $('.js-cart-count').text(cartCount);
    }

    function extractErrorMessage(xhr, fallback) {
        if (xhr.responseJSON) {
            if (xhr.responseJSON.message) {
                return xhr.responseJSON.message;
            }

            if (xhr.responseJSON.errors) {
                var firstKey = Object.keys(xhr.responseJSON.errors)[0];

                if (firstKey && xhr.responseJSON.errors[firstKey] && xhr.responseJSON.errors[firstKey].length) {
                    return xhr.responseJSON.errors[firstKey][0];
                }
            }
        }

        return fallback;
    }

    function updateCart(productId, quantity, input) {
        if (quantity < 1 || Number.isNaN(quantity)) {
            return;
        }

        $.ajax({
            url: '{{ route('ecommerce.cart.update') }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                product_id: productId,
                quantity: quantity
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
                syncCartCount(response.cartCount);

                if (input) {
                    $(input).data('last-valid', response.quantity);
                    $(input).val(response.quantity);
                }
            },
            error: function (xhr) {
                if (input) {
                    $(input).val($(input).data('last-valid') || 1);
                }

                toastr.error(extractErrorMessage(xhr, 'Failed to update cart.'));
            }
        });
    }

    function removeFromCart(productId) {
        Swal.fire({
            title: 'Remove item?',
            text: 'Do you want to remove this product from your cart?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#2563eb',
            confirmButtonText: 'Yes, remove it'
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }

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

                    $('#row-' + productId).fadeOut(250, function () {
                        $(this).remove();

                        if (!$('.cart-table tbody tr').length) {
                            window.location.reload();
                        }
                    });

                    $('#cart-subtotal').text(formatCurrency(response.total));
                    $('#cart-total').text(formatCurrency(response.total));
                    syncCartBadge(response.cartCount);
                    syncCartCount(response.cartCount);
                    toastr.success('Item removed from cart.');
                },
                error: function () {
                    toastr.error('Failed to remove item.');
                }
            });
        });
    }

    $(document).ready(function () {
        $('.btn-plus').on('click', function () {
            var input = $('.qty-input[data-id="' + $(this).data('id') + '"]');
            var newValue = parseInt(input.val(), 10) + 1;
            input.val(newValue);
            updateCart($(this).data('id'), newValue, input);
        });

        $('.btn-minus').on('click', function () {
            var input = $('.qty-input[data-id="' + $(this).data('id') + '"]');
            var currentValue = parseInt(input.val(), 10);

            if (currentValue <= 1) {
                return;
            }

            var newValue = currentValue - 1;
            input.val(newValue);
            updateCart($(this).data('id'), newValue, input);
        });

        $('.qty-input').on('change', function () {
            var quantity = parseInt($(this).val(), 10);

            if (!quantity || quantity < 1) {
                quantity = 1;
                $(this).val(quantity);
            }

            updateCart($(this).data('id'), quantity, this);
        });
    });
</script>
@endpush

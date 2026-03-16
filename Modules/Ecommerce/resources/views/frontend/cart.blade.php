@extends('layouts.app')

@section('title', 'Shopping Cart - abcsheba.com')

@section('content')
<div class="content cart-page-modern">
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
        @endif

        @if(count($cart) > 0)
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="cart-card">
                        <div class="cart-card-header">
                            <h4>Cart Items ({{ count($cart) }})</h4>
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
                                            <td>
                                                <div class="cart-product-cell">
                                                    <img src="{{ $imageUrl }}"
                                                        alt="{{ $item['name'] }}"
                                                        class="cart-product-image"
                                                        onerror="this.onerror=null;this.src='{{ asset('assets/img/products/product-1.jpg') }}';">
                                                    <div>
                                                        <h6>{{ $item['name'] }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="cart-price">৳{{ number_format($item['price'], 2) }}</td>
                                            <td>
                                                <div class="cart-qty-control">
                                                    <button type="button" class="qty-btn btn-minus" data-id="{{ $productId }}" aria-label="Decrease quantity">-</button>
                                                    <input type="number"
                                                        class="qty-input"
                                                        data-id="{{ $productId }}"
                                                        value="{{ $item['quantity'] }}"
                                                        min="1">
                                                    <button type="button" class="qty-btn btn-plus" data-id="{{ $productId }}" aria-label="Increase quantity">+</button>
                                                </div>
                                            </td>
                                            <td class="cart-subtotal" id="subtotal-{{ $productId }}">৳{{ number_format($item['price'] * $item['quantity'], 2) }}</td>
                                            <td class="text-end">
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
                        <div class="cart-card-header">
                            <h4>Order Summary</h4>
                        </div>

                        <div class="summary-line">
                            <span>Subtotal</span>
                            <strong id="cart-subtotal">৳{{ number_format($total, 2) }}</strong>
                        </div>
                        <div class="summary-line">
                            <span>Shipping</span>
                            <span class="text-success">Free</span>
                        </div>

                        <hr>

                        <div class="summary-line total-line">
                            <span>Total</span>
                            <strong id="cart-total">৳{{ number_format($total, 2) }}</strong>
                        </div>

                        <a href="{{ route('ecommerce.checkout') }}" class="btn btn-primary w-100 btn-lg rounded-pill mt-4">
                            <i class="fas fa-credit-card me-2"></i>Proceed To Checkout
                        </a>
                        <a href="{{ route('ecommerce.products') }}" class="btn btn-outline-secondary w-100 mt-3 rounded-pill">
                            <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="cart-card empty-cart-state text-center py-5">
                <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
                <h3>Your cart is empty</h3>
                <p class="text-muted mb-4">Looks like you haven't added any products to your cart yet.</p>
                <a href="{{ route('ecommerce.products') }}" class="btn btn-primary btn-lg rounded-pill px-5">
                    <i class="fas fa-shopping-bag me-2"></i>Browse Products
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .cart-page-modern {
        background: linear-gradient(180deg, #f7fbff 0%, #ffffff 45%, #f8fbff 100%);
        padding: 28px 0 70px;
    }

    .cart-card {
        background: #fff;
        border: 1px solid #e8eef8;
        border-radius: 24px;
        box-shadow: 0 20px 45px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }

    .cart-card-header {
        padding: 22px 24px;
        border-bottom: 1px solid #edf2f7;
    }

    .cart-card-header h4 {
        margin: 0;
        font-size: 28px;
        font-weight: 700;
        color: #0f172a;
    }

    .cart-table-modern th,
    .cart-table-modern td {
        padding: 22px 24px;
        vertical-align: middle;
        border-color: #edf2f7;
    }

    .cart-table-modern thead th {
        color: #334155;
        font-size: 15px;
        font-weight: 700;
        white-space: nowrap;
    }

    .cart-product-cell {
        display: flex;
        align-items: center;
        gap: 16px;
        min-width: 220px;
    }

    .cart-product-image {
        width: 74px;
        height: 74px;
        object-fit: cover;
        border-radius: 18px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        flex-shrink: 0;
    }

    .cart-product-cell h6 {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.45;
    }

    .cart-price,
    .cart-subtotal {
        font-size: 20px;
        font-weight: 700;
        color: #0f172a;
        white-space: nowrap;
    }

    .cart-qty-control {
        display: inline-flex;
        align-items: center;
        gap: 14px;
        min-width: 164px;
        padding: 10px 14px;
        border: 1px solid #dbe5f3;
        border-radius: 18px;
        background: #f8fbff;
    }

    .qty-btn {
        width: 34px;
        height: 34px;
        border: 0;
        border-radius: 10px;
        background: #ffffff;
        color: #1d4ed8;
        font-size: 22px;
        font-weight: 700;
        line-height: 1;
        box-shadow: 0 8px 20px rgba(37, 99, 235, 0.10);
    }

    .qty-btn:hover {
        background: #1d4ed8;
        color: #fff;
    }

    .qty-input {
        width: 46px;
        border: 0;
        background: transparent;
        text-align: center;
        font-size: 20px;
        font-weight: 700;
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
        width: 42px;
        height: 42px;
        border: 0;
        border-radius: 12px;
        background: #ef4444;
        color: #fff;
        font-size: 16px;
    }

    .remove-cart-btn:hover {
        background: #dc2626;
    }

    .cart-summary-card {
        padding: 0 24px 24px;
    }

    .cart-summary-card .cart-card-header {
        margin: 0 -24px 24px;
    }

    .summary-line {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
        color: #475569;
        font-size: 17px;
    }

    .summary-line strong {
        color: #0f172a;
        font-size: 22px;
        font-weight: 800;
    }

    .total-line {
        margin-bottom: 0;
    }

    .total-line strong {
        color: #10b981;
    }

    .empty-cart-state {
        padding-left: 24px;
        padding-right: 24px;
    }

    @media (max-width: 991.98px) {
        .cart-card-header h4 {
            font-size: 24px;
        }

        .cart-table-modern th,
        .cart-table-modern td {
            padding: 18px 16px;
        }
    }

    @media (max-width: 767.98px) {
        .cart-page-modern {
            padding: 18px 0 50px;
        }

        .cart-product-cell {
            min-width: 180px;
        }

        .cart-qty-control {
            min-width: 148px;
            gap: 10px;
            padding: 8px 10px;
        }

        .qty-btn {
            width: 30px;
            height: 30px;
        }

        .qty-input {
            width: 38px;
            font-size: 18px;
        }

        .cart-price,
        .cart-subtotal {
            font-size: 18px;
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

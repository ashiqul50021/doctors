@extends('layouts.app')

@section('title', 'Shopping Cart - abcsheba.com')

@push('styles')
    <style>
        .cart-container {
            padding: 100px 0 60px;
            background-color: #f9fafb;
            min-height: 80vh;
        }

        .cart-card {
            border: none;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border-radius: 12px;
            background: #fff;
            margin-bottom: 24px;
            overflow: hidden;
        }

        .cart-header {
            background: #fff;
            padding: 20px 24px;
            border-bottom: 1px solid #e5e7eb;
        }

        .cart-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #111827;
            margin: 0;
        }

        .cart-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .cart-table th {
            background: #f3f4f6;
            padding: 12px 24px;
            text-align: left;
            font-size: 0.875rem;
            font-weight: 600;
            color: #4b5563;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .cart-table td {
            padding: 20px 24px;
            vertical-align: middle;
            border-bottom: 1px solid #f3f4f6;
        }

        .cart-table tr:last-child td {
            border-bottom: none;
        }

        .cart-product-img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .cart-product-name {
            font-weight: 600;
            color: #111827;
            font-size: 1rem;
            text-decoration: none;
            transition: color 0.2s;
        }

        .cart-product-name:hover {
            color: #1D4ED8;
        }

        /* Quantity Control */
        .qty-container {
            display: flex;
            align-items: center;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
            width: 120px;
        }

        .qty-btn {
            width: 36px;
            height: 36px;
            background: #f9fafb;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #4b5563;
            transition: all 0.2s;
        }

        .qty-btn:hover {
            background: #e5e7eb;
            color: #111827;
        }

        .qty-input {
            width: 48px;
            height: 36px;
            border: none;
            text-align: center;
            font-weight: 600;
            font-size: 0.95rem;
            color: #111827;
            -moz-appearance: textfield;
        }

        .qty-input::-webkit-outer-spin-button,
        .qty-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Summary Card */
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            color: #4b5563;
            font-size: 0.95rem;
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            margin-top: 16px;
            padding-top: 16px;
            border-top: 2px dashed #e5e7eb;
            font-size: 1.25rem;
            font-weight: 700;
            color: #111827;
        }

        .btn-checkout {
            background: #1D4ED8;
            color: white;
            padding: 14px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.2s;
            border: none;
            display: block;
            width: 100%;
            text-align: center;
        }

        .btn-checkout:hover {
            background: #1E40AF;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(29, 78, 216, 0.25);
            color: white;
        }

        .remove-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #fee2e2;
            color: #ef4444;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .remove-btn:hover {
            background: #ef4444;
            color: white;
            transform: rotate(90deg);
        }
    </style>
@endpush

@section('content')
    <div class="cart-container">
        <div class="container">

            <div id="alert-container">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            </div>

            @if(count($cart) > 0)
                <div class="row">
                    <!-- Cart Items -->
                    <div class="col-lg-8">
                        <div class="cart-card">
                            <div class="cart-header">
                                <h4 class="cart-title">Shopping Cart ({{ count($cart) }} Items)</h4>
                            </div>
                            <div class="table-responsive">
                                <table class="cart-table">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th>Subtotal</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($cart as $productId => $item)
                                            <tr id="row-{{ $productId }}">
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ $item['image'] ? asset($item['image']) : asset('assets/img/products/default-product.png') }}"
                                                            alt="{{ $item['name'] }}" class="cart-product-img me-3">
                                                        <div>
                                                            <a href="{{ route('ecommerce.products.show', $productId) }}"
                                                                class="cart-product-name">{{ $item['name'] }}</a>
                                                            <div class="text-muted small mt-1">ID: #{{ $productId }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="fw-bold text-dark">৳{{ number_format($item['price'], 2) }}</td>
                                                <td>
                                                    <div class="qty-container">
                                                        <button class="qty-btn btn-minus" data-id="{{ $productId }}"><i
                                                                class="fas fa-minus"></i></button>
                                                        <input type="number" class="qty-input" value="{{ $item['quantity'] }}"
                                                            min="1" readonly>
                                                        <button class="qty-btn btn-plus" data-id="{{ $productId }}"><i
                                                                class="fas fa-plus"></i></button>
                                                    </div>
                                                </td>
                                                <td class="fw-bold text-dark" id="subtotal-{{ $productId }}">
                                                    ৳{{ number_format($item['price'] * $item['quantity'], 2) }}
                                                </td>
                                                <td>
                                                    <button class="remove-btn" onclick="removeFromCart({{ $productId }})">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Summary -->
                    <div class="col-lg-4">
                        <div class="cart-card">
                            <div class="cart-header">
                                <h4 class="cart-title">Order Summary</h4>
                            </div>
                            <div class="card-body p-4">
                                <div class="summary-item">
                                    <span>Subtotal</span>
                                    <span class="fw-bold" id="cart-subtotal">৳{{ number_format($total, 2) }}</span>
                                </div>

                                <div class="summary-total">
                                    <span>Total</span>
                                    <span class="text-primary" id="cart-total">৳{{ number_format($total, 2) }}</span>
                                </div>

                                <a href="{{ route('product.checkout') }}" class="btn-checkout mt-4 text-decoration-none">
                                    Proceed to Checkout <i class="fas fa-arrow-right ms-2"></i>
                                </a>

                                <a href="{{ route('ecommerce.products') }}"
                                    class="d-block text-center mt-3 text-muted text-decoration-none small">
                                    <i class="fas fa-long-arrow-alt-left me-1"></i> Continue Shopping
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="cart-card text-center p-5">
                    <div class="mb-4">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center"
                            style="width: 100px; height: 100px;">
                            <i class="fas fa-shopping-basket fa-3x text-muted"></i>
                        </div>
                    </div>
                    <h3>Your cart is empty</h3>
                    <p class="text-muted mb-4">Start shopping to add items to your cart.</p>
                    <a href="{{ route('ecommerce.products') }}" class="btn btn-primary btn-lg px-5 rounded-pill">
                        Browse Products
                    </a>
                </div>
            @endif

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Format Currency
        function formatCurrency(amount) {
            return '৳' + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }

        // Update Cart AJAX
        function updateCart(productId, quantity) {
            if (quantity < 1) return;

            $.ajax({
                url: '{{ route("cart.update") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    product_id: productId,
                    quantity: quantity
                },
                success: function (response) {
                    if (response.success) {
                        // Update Item Subtotal
                        $('#subtotal-' + productId).text(formatCurrency(response.itemSubtotal));
                        // Update Cart Total
                        $('#cart-subtotal').text(formatCurrency(response.total));
                        $('#cart-total').text(formatCurrency(response.total));
                        // Update Badge in Header
                        var badge = $('#cart-icon-btn .badge');
                        if (badge.length) {
                            badge.text(response.cartCount);
                        }
                        toastr.success('Cart updated');
                    }
                }
            });
        }

        // Remove from Cart AJAX
        function removeFromCart(productId) {
            Swal.fire({
                title: 'Remove Item?',
                text: "Do you want to remove this item from your cart?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, remove it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("cart.remove") }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            product_id: productId
                        },
                        success: function (response) {
                            if (response.success) {
                                $('#row-' + productId).fadeOut(300, function () {
                                    $(this).remove();
                                    if ($('.cart-table tbody tr').length === 0) {
                                        location.reload(); // Reload to show empty state
                                    }
                                });
                                $('#cart-subtotal').text(formatCurrency(response.total));
                                $('#cart-total').text(formatCurrency(response.total));

                                var badge = $('#cart-icon-btn .badge');
                                if (badge.length) {
                                    badge.text(response.cartCount);
                                }
                                toastr.success('Item removed');
                            }
                        }
                    });
                }
            });
        }

        $(document).ready(function () {
            // Increment
            $('.btn-plus').click(function () {
                var input = $(this).siblings('.qty-input');
                var val = parseInt(input.val());
                var newVal = val + 1;
                input.val(newVal);
                updateCart($(this).data('id'), newVal);
            });

            // Decrement
            $('.btn-minus').click(function () {
                var input = $(this).siblings('.qty-input');
                var val = parseInt(input.val());
                if (val > 1) {
                    var newVal = val - 1;
                    input.val(newVal);
                    updateCart($(this).data('id'), newVal);
                }
            });
        });
    </script>
@endpush

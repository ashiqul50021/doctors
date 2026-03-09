@extends('layouts.app')

@section('title', 'Checkout - abcsheba.com')

@section('content')
    @push('styles')
        <style>
            .checkout-container {
                padding: 100px 0 60px;
                background-color: #f8f9fa;
                min-height: 100vh;
            }

            .form-floating>label {
                padding-left: 20px;
            }

            /* Quantity Control Stats */
            .qty-container {
                display: flex;
                align-items: center;
                border: 1px solid #e5e7eb;
                border-radius: 6px;
                overflow: hidden;
                width: 90px;
                height: 30px;
            }

            .qty-btn {
                width: 25px;
                height: 100%;
                background: #f9fafb;
                border: none;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                color: #4b5563;
                font-size: 0.7rem;
                transition: all 0.2s;
            }

            .qty-btn:hover {
                background: #e5e7eb;
                color: #111827;
            }

            .qty-input {
                width: 40px;
                height: 100%;
                border: none;
                text-align: center;
                font-weight: 600;
                font-size: 0.85rem;
                color: #111827;
                -moz-appearance: textfield;
                padding: 0;
            }

            .qty-input:focus {
                outline: none;
            }

            .qty-input::-webkit-outer-spin-button,
            .qty-input::-webkit-inner-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }


            .form-floating>.form-control:focus,
            .form-floating>.form-control:not(:placeholder-shown) {
                padding-top: 1.625rem;
                padding-bottom: 0.625rem;
            }

            .form-floating>.form-control {
                height: calc(3.5rem + 2px);
                line-height: 1.25;
            }

            .shipping-option {
                cursor: pointer;
                transition: all 0.2s;
            }

            .shipping-option:hover {
                background-color: #f8f9fa;
                border-color: #0d6efd !important;
            }

            .shipping-option.selected {
                background-color: #e7f1ff;
                border-color: #0d6efd !important;
            }

            .card {
                margin-bottom: 20px;
            }
        </style>
    @endpush

    <!-- Page Content -->
    <div class="checkout-container">
        <div class="container">
            <!-- ... form content ... -->


            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('ecommerce.order.place') }}" method="POST">
                @csrf
                <div class="row">
                    <!-- Billing Details -->
                    <div class="col-lg-8">
                        <div class="card shadow-sm border-0 rounded-3">
                            <div class="card-header bg-white py-3">
                                <h5 class="card-title mb-0"><i class="fas fa-shipping-fast me-2 text-primary"></i>Shipping
                                    Information</h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <div class="form-floating mb-2">
                                            <input type="text" name="name" class="form-control" id="name"
                                                placeholder="John Doe" value="{{ old('name', Auth::user()->name ?? '') }}"
                                                required>
                                            <label for="name">Full Name <span class="text-danger">*</span></label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating mb-2">
                                            <input type="email" name="email" class="form-control" id="email"
                                                placeholder="name@example.com"
                                                value="{{ old('email', Auth::user()->email ?? '') }}" required>
                                            <label for="email">Email Address <span class="text-danger">*</span></label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating mb-2">
                                            <input type="text" name="phone" class="form-control" id="phone"
                                                placeholder="+8801..."
                                                value="{{ old('phone', Auth::user()->patient->phone ?? '') }}" required>
                                            <label for="phone">Phone Number <span class="text-danger">*</span></label>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-floating mb-2">
                                            <textarea name="address" class="form-control" id="address" placeholder="Address"
                                                style="height: 100px"
                                                required>{{ old('address', Auth::user()->patient->address ?? '') }}</textarea>
                                            <label for="address">Shipping Address <span class="text-danger">*</span></label>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-floating">
                                            <textarea name="notes" class="form-control" id="notes" placeholder="Notes"
                                                style="height: 80px">{{ old('notes') }}</textarea>
                                            <label for="notes">Order Notes (Optional)</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-4 shadow-sm border-0 rounded-3">
                            <div class="card-header bg-white py-3">
                                <h5 class="card-title mb-0"><i class="fas fa-truck me-2 text-primary"></i>Delivery Method
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="form-check p-3 border rounded mb-3 shipping-option selected"
                                    onclick="selectShipping('inside')">
                                    <input class="form-check-input" type="radio" name="shipping_method" id="inside_dhaka"
                                        value="inside" checked onchange="updateTotal()">
                                    <label class="form-check-label d-flex justify-content-between w-100" for="inside_dhaka">
                                        <div>
                                            <strong>Inside Dhaka</strong>
                                            <p class="text-muted mb-0 small">Delivery within 2-3 days</p>
                                        </div>
                                        <span
                                            class="fw-bold text-dark">৳{{ $ecommerceSettings['shipping_inside_dhaka'] ?? 60 }}</span>
                                    </label>
                                </div>

                                <div class="form-check p-3 border rounded shipping-option"
                                    onclick="selectShipping('outside')">
                                    <input class="form-check-input" type="radio" name="shipping_method" id="outside_dhaka"
                                        value="outside" onchange="updateTotal()">
                                    <label class="form-check-label d-flex justify-content-between w-100"
                                        for="outside_dhaka">
                                        <div>
                                            <strong>Outside Dhaka</strong>
                                            <p class="text-muted mb-0 small">Delivery within 3-5 days</p>
                                        </div>
                                        <span
                                            class="fw-bold text-dark">৳{{ $ecommerceSettings['shipping_outside_dhaka'] ?? 120 }}</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-4 shadow-sm border-0 rounded-3">
                            <div class="card-header bg-white py-3">
                                <h5 class="card-title mb-0"><i class="fas fa-credit-card me-2 text-primary"></i>Payment
                                    Method</h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="form-check p-3 border rounded bg-light">
                                    <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod"
                                        checked>
                                    <label class="form-check-label" for="cod">
                                        <strong>Cash on Delivery</strong>
                                        <p class="text-muted mb-0 small">Pay with cash upon delivery.</p>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Billing Details -->

                    <!-- Order Summary -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title mb-0">Order Summary</h4>
                            </div>
                            <div class="card-body">
                                @foreach($cart as $productId => $item)
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $item['image'] ? asset($item['image']) : asset('assets/img/products/default-product.png') }}"
                                                alt="{{ $item['name'] }}" class="me-2"
                                                style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                            <div>
                                                <small class="fw-bold">{{ Str::limit($item['name'], 20) }}</small>
                                                <div class="qty-container mt-1">
                                                    <button type="button" class="qty-btn btn-minus"
                                                        data-id="{{ $productId }}"><i class="fas fa-minus"></i></button>
                                                    <input type="number" class="qty-input" value="{{ $item['quantity'] }}"
                                                        min="1" readonly>
                                                    <button type="button" class="qty-btn btn-plus" data-id="{{ $productId }}"><i
                                                            class="fas fa-plus"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span>৳{{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                                            <button type="button" class="btn btn-sm text-danger ms-2"
                                                onclick="removeFromCart({{ $productId }})" title="Remove">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach

                                <hr>

                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal</span>
                                    <span>৳{{ number_format($total, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Shipping</span>
                                    <span class="text-success fw-bold" id="shipping_display">Free</span>
                                </div>

                                <div class="d-flex justify-content-between mb-2" id="discount_wrapper"
                                    style="display: none;">
                                    <span>Discount <small class="text-muted" id="coupon_info"></small></span>
                                    <span class="text-danger">-৳<span id="discount_display">0.00</span></span>
                                </div>

                                <div class="coupon-box mt-3 mb-3 p-3 bg-white rounded border"
                                    style="border-style: dashed !important; border-color: #dee2e6 !important;">
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent border-end-0 ps-3"><i
                                                class="fas fa-tag text-muted"></i></span>
                                        <input type="text" class="form-control border-start-0 ps-0 shadow-none"
                                            id="coupon_code" placeholder="Enter Coupon Code"
                                            style="background: transparent;">
                                        <button class="btn btn-dark px-4" type="button"
                                            onclick="applyCoupon()">Apply</button>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mb-4 border-top pt-3">
                                    <span class="h5 mb-0">Total</span>
                                    <span class="h4 mb-0 text-primary fw-bold"
                                        id="grand_total">৳{{ number_format($total, 2) }}</span>
                                </div>

                                <input type="hidden" name="shipping_method" id="shipping_method_input" value="inside">
                                <input type="hidden" name="shipping_cost" id="shipping_cost_input" value="0">
                                <input type="hidden" name="coupon_code" id="coupon_code_input">
                                <input type="hidden" name="total_amount" id="total_amount_input" value="{{ $total }}">

                                <button type="submit" class="btn btn-primary w-100 btn-sm py-2">
                                    <i class="fas fa-check-circle me-2"></i>Place Order
                                </button>
                                <a href="{{ route('ecommerce.cart') }}" class="btn btn-outline-secondary w-100 mt-3">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Cart
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- /Order Summary -->
                </div>
            </form>

        </div>
    </div>
    <!-- /Page Content -->
@endsection

@push('scripts')
    <script>
        const subtotal = {{ $total }};
        const shippingInside = {{ $ecommerceSettings['shipping_inside_dhaka'] ?? 60 }};
        const shippingOutside = {{ $ecommerceSettings['shipping_outside_dhaka'] ?? 120 }};
        let currentDiscount = 0;

        function formatCurrency(amount) {
            return parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }

        function selectShipping(type) {
            // Update radio button
            document.getElementById(type + '_dhaka').checked = true;

            // Update visual selection
            document.querySelectorAll('.shipping-option').forEach(el => el.classList.remove('selected'));
            document.getElementById(type + '_dhaka').closest('.shipping-option').classList.add('selected');

            updateTotal();
        }

        function updateTotal() {
            let shippingCost = 0;
            const method = document.querySelector('input[name="shipping_method"]:checked').value;

            if (method === 'inside') {
                shippingCost = shippingInside;
            } else {
                shippingCost = shippingOutside;
            }

            const total = Math.max(0, (subtotal - currentDiscount) + shippingCost);

            // Update UI
            document.getElementById('shipping_display').innerText = '৳' + formatCurrency(shippingCost);
            document.getElementById('shipping_display').classList.remove('text-success', 'text-dark');
            document.getElementById('shipping_display').classList.add('text-dark');

            document.getElementById('grand_total').innerText = '৳' + formatCurrency(total);

            // Update Hidden Inputs
            document.getElementById('shipping_method_input').value = method;
            document.getElementById('shipping_cost_input').value = shippingCost;
            document.getElementById('total_amount_input').value = total;
        }

        function applyCoupon() {
            const code = document.getElementById('coupon_code').value;
            if (!code) {
                Swal.fire('Error', 'Please enter a coupon code', 'error');
                return;
            }

            $.ajax({
                url: '{{ route("cart.coupon") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    coupon_code: code
                },
                success: function (response) {
                    if (response.success) {
                        currentDiscount = parseFloat(response.discount);
                        document.getElementById('discount_wrapper').style.display = 'flex';
                        document.getElementById('discount_display').innerText = formatCurrency(currentDiscount);
                        document.getElementById('coupon_info').innerText = '(' + response.code + ')';
                        document.getElementById('coupon_code_input').value = response.code;

                        updateTotal();
                        Swal.fire('Success', response.message, 'success');
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function (xhr) {
                    let msg = 'Failed to apply coupon';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    Swal.fire('Error', msg, 'error');
                }
            });

        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function () {
            updateTotal();

            // Quantity Increment
            document.querySelectorAll('.btn-plus').forEach(button => {
                button.addEventListener('click', function () {
                    let input = this.parentElement.querySelector('.qty-input');
                    let newVal = parseInt(input.value) + 1;
                    updateCart(this.dataset.id, newVal);
                });
            });

            // Quantity Decrement
            document.querySelectorAll('.btn-minus').forEach(button => {
                button.addEventListener('click', function () {
                    let input = this.parentElement.querySelector('.qty-input');
                    let val = parseInt(input.value);
                    if (val > 1) {
                        let newVal = val - 1;
                        updateCart(this.dataset.id, newVal);
                    }
                });
            });
        });

        // Update Cart Quantity
        function updateCart(productId, quantity) {
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
                        location.reload();
                    }
                }
            });
        }


        // Remove from Cart Logic (for checkout page)
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
                                Swal.fire(
                                    'Removed!',
                                    'Item has been removed.',
                                    'success'
                                ).then(() => {
                                    // Reload page to recalculate everything (easiest for checkout flow)
                                    location.reload();
                                });
                            }
                        },
                        error: function () {
                            Swal.fire('Error', 'Failed to remove item', 'error');
                        }
                    });
                }
            });
        }
    </script>
@endpush

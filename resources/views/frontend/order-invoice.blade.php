@extends('layouts.app')

@section('title', 'Invoice - Order #' . $order->id)

@section('content')

    <!-- Page Content -->
    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="invoice-content">
                        <div class="invoice-item">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="invoice-logo">
                                        <img src="{{ !empty($siteSettings['logo']) ? asset($siteSettings['logo']) : asset('assets/img/logo.png') }}" alt="logo">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <p class="invoice-details">
                                        <strong>Order:</strong> #{{ $order->id }} <br>
                                        <strong>Issued:</strong> {{ $order->created_at->format('d/m/Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Invoice Item -->
                        <div class="invoice-item">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="invoice-info">
                                        <strong class="customer-text">Invoice From</strong>
                                        <p class="invoice-details invoice-details-two">
                                            {{ $siteSettings['site_name'] ?? 'Doccure' }} <br>
                                            {{ $siteSettings['address'] ?? 'Dhaka, Bangladesh' }} <br>
                                            {{ $siteSettings['email'] ?? 'info@doccure.com' }} <br>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="invoice-info invoice-info2">
                                        <strong class="customer-text">Invoice To</strong>
                                        <p class="invoice-details">
                                            {{ $order->customer_name }} <br>
                                            {{ $order->shipping_address }} <br>
                                            {{ $order->customer_phone }} <br>
                                            {{ $order->customer_email }} <br>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /Invoice Item -->

                        <!-- Invoice Item -->
                        <div class="invoice-item">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="invoice-info">
                                        <strong class="customer-text">Payment Method</strong>
                                        <p class="invoice-details invoice-details-two">
                                            Cash on Delivery <br>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /Invoice Item -->

                        <!-- Invoice Item -->
                        <div class="invoice-item invoice-table-wrap">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="invoice-table table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Description</th>
                                                    <th class="text-center">Price</th>
                                                    <th class="text-center">Quantity</th>
                                                    <th class="text-end">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($order->items as $item)
                                                    <tr>
                                                        <td>{{ $item->product->name ?? 'Product' }}</td>
                                                        <td class="text-center">৳{{ number_format($item->price, 2) }}</td>
                                                        <td class="text-center">{{ $item->quantity }}</td>
                                                        <td class="text-end">
                                                            ৳{{ number_format($item->price * $item->quantity, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xl-4 ms-auto">
                                    <div class="table-responsive">
                                        <table class="invoice-table-two table">
                                            <tbody>
                                                <tr>
                                                    <th>Subtotal:</th>
                                                    <td><span>৳{{ number_format($order->subtotal, 2) }}</span></td>
                                                </tr>
                                                <tr>
                                                    <th>Shipping:</th>
                                                    <td><span>৳{{ number_format($order->shipping, 2) }}</span></td>
                                                </tr>
                                                @if($order->discount > 0)
                                                    <tr>
                                                        <th>Discount:</th>
                                                        <td><span
                                                                class="text-danger">-৳{{ number_format($order->discount, 2) }}</span>
                                                        </td>
                                                    </tr>
                                                @endif
                                                <tr>
                                                    <th>Total Amount:</th>
                                                    <td><span>৳{{ number_format($order->total, 2) }}</span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /Invoice Item -->

                        <!-- Invoice Information -->
                        <div class="other-info">
                            <h4>Notes</h4>
                            <p class="text-muted mb-0">{{ $order->notes ?? 'No additional notes.' }}</p>
                        </div>
                        <!-- /Invoice Information -->

                        <div class="text-center mt-4 print-btn-div">
                            <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print me-2"></i> Print
                                Invoice</button>
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </div>
    <!-- /Page Content -->

    @push('styles')
        <style>
            @media print {

                .print-btn-div,
                header,
                footer,
                .breadcrumb-bar,
                .bottom-nav-container {
                    display: none !important;
                }

                .content {
                    padding: 0 !important;
                }

                .invoice-content {
                    border: 0 !important;
                    box-shadow: none !important;
                }
            }
        </style>
    @endpush

@endsection

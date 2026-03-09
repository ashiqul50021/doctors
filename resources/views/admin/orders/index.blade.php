@extends('layouts.admin')

@section('title', 'Orders - Doccure Admin')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Order Management</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Orders</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-center mb-0">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td>{{ $order->order_number }}</td>
                                        <td>
                                            <h2 class="table-avatar">
                                                <a href="#" class="avatar avatar-sm me-2">
                                                    <img class="avatar-img rounded-circle"
                                                        src="{{ $order->patient && $order->patient->user->profile_image ? asset($order->patient->user->profile_image) : asset('assets/img/patients/patient.jpg') }}"
                                                        alt="User Image">
                                                </a>
                                                <a href="#">{{ $order->customer_name }}</a>
                                            </h2>
                                        </td>
                                        <td>{{ $order->created_at->format('d M Y') }} <span
                                                class="text-primary d-block">{{ $order->created_at->format('h:i A') }}</span>
                                        </td>
                                        <td>{{ $order->items_count ?? $order->items()->count() }} Items</td>
                                        <td>৳{{ number_format($order->total, 2) }}</td>
                                        <td>
                                            @if($order->status == 'pending')
                                                <span class="badge bg-warning-light">Pending</span>
                                            @elseif($order->status == 'processing')
                                                <span class="badge bg-info-light">Processing</span>
                                            @elseif($order->status == 'completed')
                                                <span class="badge bg-success-light">Completed</span>
                                            @elseif($order->status == 'cancelled')
                                                <span class="badge bg-danger-light">Cancelled</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="actions">
                                                <a class="btn btn-sm bg-success-light"
                                                    href="{{ route('admin.orders.show', $order->id) }}">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $orders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
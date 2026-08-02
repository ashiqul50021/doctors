@extends('layouts.admin')

@section('title', 'My Orders')

@section('content')
<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title">My Orders</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('ecommerce.seller.dashboard') }}">Dashboard</a></li>
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
                                <th>My Items</th>
                                <th>Status</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
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
                                <td>
                                    {{ $order->created_at->format('d M Y') }}
                                    <span class="text-primary d-block">{{ $order->created_at->format('h:i A') }}</span>
                                </td>
                                <td>{{ $order->items_count }} Item(s)</td>
                                <td>
                                    @if($order->status == 'pending')
                                        <span class="badge bg-warning-light">Pending</span>
                                    @elseif($order->status == 'processing')
                                        <span class="badge bg-info-light">Processing</span>
                                    @elseif($order->status == 'shipped')
                                        <span class="badge bg-primary-light">Shipped</span>
                                    @elseif($order->status == 'delivered')
                                        <span class="badge bg-success-light">Delivered</span>
                                    @elseif($order->status == 'cancelled')
                                        <span class="badge bg-danger-light">Cancelled</span>
                                    @else
                                        <span class="badge bg-secondary-light">{{ ucfirst($order->status) }}</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <div class="actions">
                                        <a class="btn btn-sm bg-success-light" href="{{ route('ecommerce.seller.orders.show', $order->id) }}">
                                            <i class="fas fa-eye"></i> View Order
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No orders found containing your products.</td>
                            </tr>
                            @endforelse
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

@extends('layouts.admin')

@section('title', 'Coupons - Doccure Admin')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-7 col-auto">
                <h3 class="page-title">Coupons</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Coupons</li>
                </ul>
            </div>
            <div class="col-sm-5 col">
                <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary float-end mt-2">Add Coupon</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-center mb-0">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Expiry Date</th>
                                    <th>Usage</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($coupons as $coupon)
                                    <tr>
                                        <td><strong>{{ $coupon->code }}</strong></td>
                                        <td>{{ ucfirst($coupon->type) }}</td>
                                        <td>{{ $coupon->type == 'fixed' ? '৳' . number_format($coupon->amount, 2) : $coupon->amount . '%' }}
                                        </td>
                                        <td>
                                            @if($coupon->expiry_date)
                                                {{ $coupon->expiry_date->format('d M Y') }}
                                                @if($coupon->expiry_date->isPast())
                                                    <span class="badge bg-danger-light">Expired</span>
                                                @endif
                                            @else
                                                <span class="badge bg-info-light">No Expiry</span>
                                            @endif
                                        </td>
                                        <td>{{ $coupon->used_count }} / {{ $coupon->usage_limit ?? '∞' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $coupon->status ? 'success' : 'danger' }}-light">
                                                {{ $coupon->status ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="actions">
                                                <a class="btn btn-sm bg-success-light"
                                                    href="{{ route('admin.coupons.edit', $coupon->id) }}">
                                                    <i class="fe fe-pencil"></i> Edit
                                                </a>
                                                <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST"
                                                    style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm bg-danger-light"
                                                        onclick="return confirm('Are you sure?')">
                                                        <i class="fe fe-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $coupons->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
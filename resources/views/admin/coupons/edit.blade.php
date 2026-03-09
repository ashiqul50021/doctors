@extends('layouts.admin')

@section('title', 'Edit Coupon')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Edit Coupon</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.coupons.index') }}">Coupons</a></li>
                    <li class="breadcrumb-item active">Edit Coupon</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.coupons.update', $coupon->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row form-row">
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label>Coupon Code <span class="text-danger">*</span></label>
                                    <input type="text" name="code" class="form-control" value="{{ $coupon->code }}"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label>Type <span class="text-danger">*</span></label>
                                    <select name="type" class="form-select">
                                        <option value="fixed" {{ $coupon->type == 'fixed' ? 'selected' : '' }}>Fixed Amount
                                            (৳)</option>
                                        <option value="percent" {{ $coupon->type == 'percent' ? 'selected' : '' }}>Percentage
                                            (%)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label>Amount <span class="text-danger">*</span></label>
                                    <input type="number" name="amount" class="form-control" step="0.01"
                                        value="{{ $coupon->amount }}" required>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label>Expiry Date</label>
                                    <input type="date" name="expiry_date" class="form-control"
                                        value="{{ $coupon->expiry_date ? $coupon->expiry_date->format('Y-m-d') : '' }}">
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label>Usage Limit (Optional)</label>
                                    <input type="number" name="usage_limit" class="form-control"
                                        value="{{ $coupon->usage_limit }}" placeholder="Leave empty for unlimited">
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-select">
                                        <option value="1" {{ $coupon->status ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ !$coupon->status ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Update Coupon</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
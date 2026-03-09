@extends('layouts.admin')

@section('title', 'Add Coupon')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Add New Coupon</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.coupons.index') }}">Coupons</a></li>
                    <li class="breadcrumb-item active">Add Coupon</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.coupons.store') }}" method="POST">
                        @csrf
                        <div class="row form-row">
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label>Coupon Code <span class="text-danger">*</span></label>
                                    <input type="text" name="code" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label>Type <span class="text-danger">*</span></label>
                                    <select name="type" class="form-select">
                                        <option value="fixed">Fixed Amount (৳)</option>
                                        <option value="percent">Percentage (%)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label>Amount <span class="text-danger">*</span></label>
                                    <input type="number" name="amount" class="form-control" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label>Expiry Date</label>
                                    <input type="date" name="expiry_date" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label>Usage Limit (Optional)</label>
                                    <input type="number" name="usage_limit" class="form-control"
                                        placeholder="Leave empty for unlimited">
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-select">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Create Coupon</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
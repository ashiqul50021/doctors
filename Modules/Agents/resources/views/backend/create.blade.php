@extends('layouts.admin')

@section('title', 'Add Agent - ' . ($siteSettings['site_name'] ?? 'Doccure Admin'))

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Add New Agent</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.agents.index') }}">Agents</a></li>
                    <li class="breadcrumb-item active">Add Agent</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.agents.store') }}" method="POST">
                        @csrf
                        <div class="row form-row">
                            <h4 class="card-title col-12 mb-4">Account Information</h4>
                            
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group mb-3">
                                    <label>Agent Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="e.g. Ashiqul Islam">
                                    @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group mb-3">
                                    <label>Email Address <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="e.g. agent@example.com">
                                    @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-md-6 col-sm-12">
                                <div class="form-group mb-3">
                                    <label>Phone Number <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required placeholder="e.g. 01712345678">
                                    @error('phone') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-md-6 col-sm-12">
                                <div class="form-group mb-3">
                                    <label>Password <span class="text-danger">*</span></label>
                                    <input type="password" name="password" class="form-control" required placeholder="Min 8 characters">
                                    @error('password') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <hr class="my-4">

                            <h4 class="card-title col-12 mb-4">Permissions & Operations</h4>
                            
                            <div class="col-md-12 mb-4">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="can_book_appointments" id="can_book_appointments" value="1" checked>
                                    <label class="form-check-label font-weight-bold" for="can_book_appointments">
                                        Can Book Appointments
                                    </label>
                                </div>
                                <div class="form-check form-check-inline mx-4">
                                    <input class="form-check-input" type="checkbox" name="can_sell_products" id="can_sell_products" value="1" checked>
                                    <label class="form-check-label font-weight-bold" for="can_sell_products">
                                        Can Sell Products
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="can_sell_courses" id="can_sell_courses" value="1" checked>
                                    <label class="form-check-label font-weight-bold" for="can_sell_courses">
                                        Can Sell Courses
                                    </label>
                                </div>
                            </div>

                            <hr class="my-2">

                            <h4 class="card-title col-12 mb-4">Commission Configurations</h4>

                            <div class="col-md-4 col-sm-12">
                                <div class="form-group mb-3">
                                    <label>Booking Commission (৳ Flat Fee per Booking)</label>
                                    <input type="number" name="booking_commission_rate" class="form-control" step="0.01" value="{{ old('booking_commission_rate', '50.00') }}" required>
                                    @error('booking_commission_rate') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-12">
                                <div class="form-group mb-3">
                                    <label>Product Commission (% of Sale Total)</label>
                                    <input type="number" name="product_commission_rate" class="form-control" step="0.01" value="{{ old('product_commission_rate', '5.00') }}" required>
                                    @error('product_commission_rate') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-12">
                                <div class="form-group mb-3">
                                    <label>Course Commission (% of Course Price)</label>
                                    <input type="number" name="course_commission_rate" class="form-control" step="0.01" value="{{ old('course_commission_rate', '10.00') }}" required>
                                    @error('course_commission_rate') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-md-6 col-sm-12 mt-3">
                                <div class="form-group mb-3">
                                    <label>Account Status</label>
                                    <select name="status" class="form-select">
                                        <option value="active">Active</option>
                                        <option value="pending">Pending Admin Approval</option>
                                        <option value="suspended">Suspended</option>
                                    </select>
                                    @error('status') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Create Agent Account</button>
                            <a href="{{ route('admin.agents.index') }}" class="btn btn-secondary mx-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

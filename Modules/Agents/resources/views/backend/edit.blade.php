@extends('layouts.admin')

@section('title', 'Edit Agent - ' . ($siteSettings['site_name'] ?? 'abcsheba Admin'))

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Edit Agent: {{ $agent->user->name }}</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.agents.index') }}">Agents</a></li>
                    <li class="breadcrumb-item active">Edit Agent</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.agents.update', $agent->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row form-row">
                            <h4 class="card-title col-12 mb-4">Account Information</h4>
                            
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group mb-3">
                                    <label>Agent Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', $agent->user->name) }}" required placeholder="e.g. Ashiqul Islam">
                                    @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group mb-3">
                                    <label>Email Address <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email', $agent->user->email) }}" required placeholder="e.g. agent@example.com">
                                    @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-md-6 col-sm-12">
                                <div class="form-group mb-3">
                                    <label>Phone Number <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $agent->phone) }}" required placeholder="e.g. 01712345678">
                                    @error('phone') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-md-6 col-sm-12">
                                <div class="form-group mb-3">
                                    <label>Password <small class="text-muted">(Leave blank to keep current password)</small></label>
                                    <input type="password" name="password" class="form-control" placeholder="Min 8 characters">
                                    @error('password') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <hr class="my-4">

                            <h4 class="card-title col-12 mb-4">Permissions & Operations</h4>
                            
                            <div class="col-md-12 mb-4">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="can_book_appointments" id="can_book_appointments" value="1" {{ $agent->can_book_appointments ? 'checked' : '' }}>
                                    <label class="form-check-label font-weight-bold" for="can_book_appointments">
                                        Can Book Appointments
                                    </label>
                                </div>
                                <div class="form-check form-check-inline mx-4">
                                    <input class="form-check-input" type="checkbox" name="can_sell_products" id="can_sell_products" value="1" {{ $agent->can_sell_products ? 'checked' : '' }}>
                                    <label class="form-check-label font-weight-bold" for="can_sell_products">
                                        Can Sell Products
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="can_sell_courses" id="can_sell_courses" value="1" {{ $agent->can_sell_courses ? 'checked' : '' }}>
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
                                    <input type="number" name="booking_commission_rate" class="form-control" step="0.01" value="{{ old('booking_commission_rate', $agent->booking_commission_rate) }}" required>
                                    @error('booking_commission_rate') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-12">
                                <div class="form-group mb-3">
                                    <label>Product Commission (% of Sale Total)</label>
                                    <input type="number" name="product_commission_rate" class="form-control" step="0.01" value="{{ old('product_commission_rate', $agent->product_commission_rate) }}" required>
                                    @error('product_commission_rate') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-12">
                                <div class="form-group mb-3">
                                    <label>Course Commission (% of Course Price)</label>
                                    <input type="number" name="course_commission_rate" class="form-control" step="0.01" value="{{ old('course_commission_rate', $agent->course_commission_rate) }}" required>
                                    @error('course_commission_rate') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-md-6 col-sm-12 mt-3">
                                <div class="form-group mb-3">
                                    <label>Account Status</label>
                                    <select name="status" class="form-select">
                                        <option value="active" {{ $agent->status === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="pending" {{ $agent->status === 'pending' ? 'selected' : '' }}>Pending Admin Approval</option>
                                        <option value="suspended" {{ $agent->status === 'suspended' ? 'selected' : '' }}>Suspended</option>
                                    </select>
                                    @error('status') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Update Agent Account</button>
                            <a href="{{ route('admin.agents.index') }}" class="btn btn-secondary mx-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

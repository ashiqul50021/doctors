@extends('layouts.admin')

@section('title', 'Shop Settings')

@section('content')
<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title">Shop Settings</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('ecommerce.seller.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Shop Settings</li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('ecommerce.seller.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title text-primary">Store Branding & Info</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="store_name">Store Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="store_name" name="store_name" value="{{ old('store_name', $seller->store_name) }}" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="phone">Store Phone <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $seller->phone) }}" required>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label" for="address">Store Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3">{{ old('address', $seller->address) }}</textarea>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="store_logo">Store Logo</label>
                            <input type="file" class="form-control" id="store_logo" name="store_logo" accept="image/*">
                            @if($seller->store_logo)
                                <div class="mt-2">
                                    <img src="{{ asset($seller->store_logo) }}" alt="Logo" class="img-thumbnail" style="max-height: 80px;">
                                </div>
                            @endif
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="store_banner">Store Banner</label>
                            <input type="file" class="form-control" id="store_banner" name="store_banner" accept="image/*">
                            @if($seller->store_banner)
                                <div class="mt-2">
                                    <img src="{{ asset($seller->store_banner) }}" alt="Banner" class="img-thumbnail" style="max-height: 80px;">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title text-primary">Bank Account Information</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="bank_name">Bank Name</label>
                            <input type="text" class="form-control" id="bank_name" name="bank_name" value="{{ old('bank_name', $seller->bank_name) }}">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="bank_account_name">Account Holder Name</label>
                            <input type="text" class="form-control" id="bank_account_name" name="bank_account_name" value="{{ old('bank_account_name', $seller->bank_account_name) }}">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="bank_account_number">Account Number</label>
                            <input type="text" class="form-control" id="bank_account_number" name="bank_account_number" value="{{ old('bank_account_number', $seller->bank_account_number) }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection

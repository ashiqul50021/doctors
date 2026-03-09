@extends('layouts.admin')

@section('title', 'Edit Category - Doccure Admin')

@section('content')
<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title">Edit Product Category</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.product-categories.index') }}">Categories</a></li>
                <li class="breadcrumb-item active">Edit Category</li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.product-categories.update', $productCategory->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row form-row">
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>Category Name</label>
                                <input type="text" name="name" class="form-control" value="{{ $productCategory->name }}" required>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>Image</label>
                                <input type="file" name="image" class="form-control">
                                @if($productCategory->image)
                                    <img src="{{ asset('storage/'.$productCategory->image) }}" alt="" width="50" class="mt-2">
                                @endif
                            </div>
                        </div>
                         <div class="col-12">
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" class="form-control" rows="4">{{ $productCategory->description }}</textarea>
                            </div>
                        </div>
                        <div class="col-12">
                             <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" {{ $productCategory->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Is Active
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

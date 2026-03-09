@extends('layouts.admin')

@section('title', 'Products - Doccure Admin')

@section('content')
<div class="page-header">
    <div class="row">
        <div class="col-sm-7 col-auto">
            <h3 class="page-title">Products</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Products</li>
            </ul>
        </div>
        <div class="col-sm-5 col">
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary float-right mt-2">Add Product</a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="table-responsive">
                    <table class="datatable table table-hover table-center mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                            <tr>
                                <td>#PRO{{ $product->id }}</td>
                                <td>
                                    <h2 class="table-avatar">
                                        <a href="#" class="avatar avatar-sm mr-2">
                                            <img class="avatar-img" src="{{ $product->image ? asset('storage/'.$product->image) : asset('assets/img/specialities/specialities-01.png') }}" alt="Product">
                                        </a>
                                        <a href="#">{{ $product->name }}</a>
                                    </h2>
                                </td>
                                <td>{{ $product->category->name ?? 'N/A' }}</td>
                                <td>${{ number_format($product->price, 2) }}</td>
                                <td>{{ $product->stock }}</td>
                                <td>
                                    <div class="status-toggle">
                                        <input type="checkbox" id="status_{{ $product->id }}" class="check" {{ $product->is_active ? 'checked' : '' }} disabled>
                                        <label for="status_{{ $product->id }}" class="checktoggle">checkbox</label>
                                    </div>
                                </td>
                                <td class="text-right">
                                    <div class="actions">
                                        <a class="btn btn-sm bg-success-light" href="{{ route('admin.products.edit', $product->id) }}">
                                            <i class="fe fe-pencil"></i> Edit
                                        </a>
                                        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm bg-danger-light">
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
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('title', 'Product Categories - Doccure Admin')

@section('content')
<div class="page-header">
    <div class="row">
        <div class="col-sm-7 col-auto">
            <h3 class="page-title">Product Categories</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Product Categories</li>
            </ul>
        </div>
        <div class="col-sm-5 col">
            <a href="{{ route('admin.product-categories.create') }}" class="btn btn-primary float-right mt-2">Add Category</a>
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
                                <th>Category Name</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $category)
                            <tr>
                                <td>#CAT{{ $category->id }}</td>
                                <td>
                                    <h2 class="table-avatar">
                                        <a href="#" class="avatar avatar-sm mr-2">
                                            <img class="avatar-img" src="{{ $category->image ? asset('storage/'.$category->image) : asset('assets/img/specialities/specialities-01.png') }}" alt="Category">
                                        </a>
                                        <a href="#">{{ $category->name }}</a>
                                    </h2>
                                </td>
                                <td class="text-right">
                                    <div class="actions">
                                        <a class="btn btn-sm bg-success-light" href="{{ route('admin.product-categories.edit', $category->id) }}">
                                            <i class="fe fe-pencil"></i> Edit
                                        </a>
                                        <form action="{{ route('admin.product-categories.destroy', $category->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure?');">
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

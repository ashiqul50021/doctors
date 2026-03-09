@extends('layouts.admin')

@section('content')
<div class="page-header">
    <div class="row">
        <div class="col-sm-7 col-auto">
            <h3 class="page-title">Banners</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Banners</li>
            </ul>
        </div>
        <div class="col-sm-5 col">
            <a href="{{ route('admin.banners.create') }}" class="btn btn-primary float-end mt-2">Add Banner</a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="datatable table table-hover table-center mb-0">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Image</th>
                                <th>Type</th>
                                <th>Title</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($banners as $banner)
                            <tr>
                                <td>{{ $banner->order }}</td>
                                <td>
                                    <h2 class="table-avatar">
                                        <a href="{{ asset($banner->image) }}" class="avatar avatar-sm me-2" target="_blank">
                                            <img class="avatar-img rounded-circle" src="{{ asset($banner->image) }}" alt="Banner Image">
                                        </a>
                                    </h2>
                                </td>
                                <td>
                                    @if($banner->type == 'content_image')
                                        <span class="badge badge-info">Content + Image</span>
                                    @else
                                        <span class="badge badge-warning">Image Only</span>
                                    @endif
                                </td>
                                <td>{{ $banner->title ?? '-' }}</td>
                                <td>
                                    @if($banner->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="actions">
                                        <a class="btn btn-sm bg-success-light" href="{{ route('admin.banners.edit', $banner->id) }}">
                                            <i class="fe fe-pencil"></i> Edit
                                        </a>
                                        <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?')">
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

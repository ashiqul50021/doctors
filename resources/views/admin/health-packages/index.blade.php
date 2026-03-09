@extends('layouts.admin')

@section('title', 'Health Packages - ' . ($siteSettings['site_name'] ?? 'Admin'))

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-7 col-auto">
                <h3 class="page-title">Health Packages</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Health Packages</li>
                </ul>
            </div>
            <div class="col-sm-5 col">
                <a href="{{ route('admin.health-packages.create') }}" class="btn btn-primary float-end mt-2">Add Package</a>
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
                                    <th>Icon</th>
                                    <th>Title</th>
                                    <th>Badge</th>
                                    <th>Tests</th>
                                    <th>Price</th>
                                    <th>Featured</th>
                                    <th>Status</th>
                                    <th>Order</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($packages as $package)
                                    <tr>
                                        <td>{{ $package->id }}</td>
                                        <td><i class="{{ $package->icon }}" style="font-size: 20px; color: #1D4ED8;"></i></td>
                                        <td>{{ $package->title }}</td>
                                        <td><span class="badge bg-info">{{ $package->badge_label }}</span></td>
                                        <td>{{ $package->test_count }}+</td>
                                        <td>৳{{ number_format($package->price, 0) }}</td>
                                        <td>
                                            @if($package->is_featured)
                                                <span class="badge bg-success">Yes</span>
                                            @else
                                                <span class="badge bg-secondary">No</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($package->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>{{ $package->sort_order }}</td>
                                        <td class="text-end">
                                            <div class="actions">
                                                <a class="btn btn-sm bg-success-light"
                                                    href="{{ route('admin.health-packages.edit', $package->id) }}">
                                                    <i class="fe fe-pencil"></i> Edit
                                                </a>
                                                <form action="{{ route('admin.health-packages.destroy', $package->id) }}"
                                                    method="POST" style="display:inline-block;"
                                                    onsubmit="return confirm('Are you sure?');">
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

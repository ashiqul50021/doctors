@extends('layouts.admin')

@section('title', 'Menu Manager - ' . ($siteSettings['site_name'] ?? 'Doccure Admin'))

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-7 col-auto">
                <h3 class="page-title">Menu Manager</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Menus</li>
                </ul>
            </div>
            <div class="col-sm-5 col">
                <a href="{{ route('admin.menus.create') }}" class="btn btn-primary float-end mt-2">Add Menu</a>
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
                                    <th>Order</th>
                                    <th>Title</th>
                                    <th>URL/Route</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($menus as $menu)
                                    <tr>
                                        <td>{{ $menu->order }}</td>
                                        <td>
                                            <strong>{{ $menu->title }}</strong>
                                            @if($menu->children->count() > 0)
                                                <br><small class="text-muted">{{ $menu->children->count() }} submenu items</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($menu->route_name)
                                                <span class="badge badge-info">{{ $menu->route_name }}</span>
                                            @elseif($menu->url)
                                                <span class="text-muted">{{ Str::limit($menu->url, 30) }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $menu->location == 'main' ? 'primary' : 'secondary' }}">
                                                {{ ucfirst($menu->location) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($menu->is_active)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="actions">
                                                <a class="btn btn-sm bg-success-light"
                                                    href="{{ route('admin.menus.edit', $menu->id) }}">
                                                    <i class="fe fe-pencil"></i> Edit
                                                </a>
                                                <a class="btn btn-sm bg-danger-light"
                                                    href="{{ route('admin.menus.delete', $menu->id) }}"
                                                    onclick="return confirm('Are you sure you want to delete this menu?');">
                                                    <i class="fe fe-trash"></i> Delete
                                                </a>
                                            </div>
                                        </td>
                                    </tr>

                                    {{-- Show children/submenu items --}}
                                    @foreach($menu->children as $child)
                                        <tr style="background-color: #f9f9f9;">
                                            <td class="ps-4">↳ {{ $child->order }}</td>
                                            <td class="ps-4">{{ $child->title }}</td>
                                            <td>
                                                @if($child->route_name)
                                                    <span class="badge badge-info">{{ $child->route_name }}</span>
                                                @elseif($child->url)
                                                    <span class="text-muted">{{ Str::limit($child->url, 30) }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>-</td>
                                            <td>
                                                @if($child->is_active)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <div class="actions">
                                                    <a class="btn btn-sm bg-success-light"
                                                        href="{{ route('admin.menus.edit', $child->id) }}">
                                                        <i class="fe fe-pencil"></i> Edit
                                                    </a>
                                                    <a class="btn btn-sm bg-danger-light"
                                                        href="{{ route('admin.menus.delete', $child->id) }}"
                                                        onclick="return confirm('Are you sure you want to delete this menu?');">
                                                        <i class="fe fe-trash"></i> Delete
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

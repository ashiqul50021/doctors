@extends('layouts.admin')

@section('title', 'Add Menu - Doccure Admin')

@section('content')
<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title">Add Menu</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.menus.index') }}">Menus</a></li>
                <li class="breadcrumb-item active">Add</li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.menus.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label>Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title" value="{{ old('title') }}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>URL</label>
                                <input type="text" class="form-control" name="url" value="{{ old('url') }}" placeholder="https://example.com">
                                <small class="text-muted">Use this for external links</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Route Name</label>
                                <input type="text" class="form-control" name="route_name" value="{{ old('route_name') }}" placeholder="home">
                                <small class="text-muted">Use this for internal pages (e.g., home, search)</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Parent Menu</label>
                                <select class="form-control" name="parent_id">
                                    <option value="">None (Top Level)</option>
                                    @foreach($parentMenus as $parent)
                                        <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                            {{ $parent->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Order</label>
                                <input type="number" class="form-control" name="order" value="{{ old('order', 0) }}" min="0">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Icon (FontAwesome class)</label>
                                <input type="text" class="form-control" name="icon" value="{{ old('icon') }}" placeholder="fas fa-home">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Location <span class="text-danger">*</span></label>
                                <select class="form-control" name="location" required>
                                    <option value="main" {{ old('location') == 'main' ? 'selected' : '' }}>Main Navigation</option>
                                    <option value="footer" {{ old('location') == 'footer' ? 'selected' : '' }}>Footer</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check custom-checkbox">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" checked>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check custom-checkbox">
                            <input type="checkbox" class="form-check-input" id="open_in_new_tab" name="open_in_new_tab">
                            <label class="form-check-label" for="open_in_new_tab">Open in New Tab</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Create Menu</button>
                    <a href="{{ route('admin.menus.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

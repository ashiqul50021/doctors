@extends('layouts.admin')

@section('title', 'General Settings - ' . ($siteSettings['site_name'] ?? 'Doccure Admin'))

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">General Settings</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:(0);">Settings</a></li>
                    <li class="breadcrumb-item active">General Settings</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <!-- General -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">General</h4>
                </div>
                <div class="card-body">
                    <form action="#">

                        <div class="mb-3">
                            <label>Website Name</label>
                            <input type="text" class="form-control" value="Doccure">
                        </div>
                        <div class="mb-3">
                            <label>Website Logo</label>
                            <input type="file" class="form-control">
                            <small class="text-secondary">Recommended image size is 150px x 150px</small>
                        </div>
                        <div class="mb-3">
                            <label>Favicon</label>
                            <input type="file" class="form-control">
                            <small class="text-secondary">Recommended image size is 16px x 16px or 32px x 32px</small>
                            <small class="text-secondary">Mime types: Png, Ico</small>
                        </div>

                    </form>
                </div>
            </div>
            <!-- /General -->
        </div>
    </div>
@endsection
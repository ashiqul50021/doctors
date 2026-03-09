@extends('layouts.admin')

@section('title', 'Banner Settings - Admin')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Banner Settings</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Banner Settings</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Homepage Banner Configuration</h4>
                    <p class="text-muted mb-0">Manage the banner section displayed on the homepage</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.banner-settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            <!-- Text Settings -->
                            <div class="col-md-8">
                                <h5 class="mb-3">Banner Text</h5>

                                <div class="mb-3 mb-3">
                                    <label for="banner_title">Banner Title</label>
                                    <input type="text" class="form-control" id="banner_title" name="banner_title"
                                        value="{{ $bannerSettings['banner_title'] ?? '' }}"
                                        placeholder="e.g., Get best quality health care services">
                                    <small class="text-muted">Main heading displayed on the banner</small>
                                </div>

                                <div class="mb-3 mb-3">
                                    <label for="banner_subtitle">Banner Subtitle</label>
                                    <textarea class="form-control" id="banner_subtitle" name="banner_subtitle" rows="3"
                                        placeholder="Description text below the title">{{ $bannerSettings['banner_subtitle'] ?? '' }}</textarea>
                                    <small class="text-muted">Short description paragraph</small>
                                </div>

                                <h5 class="mb-3 mt-4">Feature Highlights</h5>
                                <p class="text-muted mb-3">Three checkmark features shown below the subtitle</p>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3 mb-3">
                                            <label for="banner_feature_1">Feature 1</label>
                                            <input type="text" class="form-control" id="banner_feature_1"
                                                name="banner_feature_1"
                                                value="{{ $bannerSettings['banner_feature_1'] ?? 'Reasonable cost' }}"
                                                placeholder="e.g., Reasonable cost">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3 mb-3">
                                            <label for="banner_feature_2">Feature 2</label>
                                            <input type="text" class="form-control" id="banner_feature_2"
                                                name="banner_feature_2"
                                                value="{{ $bannerSettings['banner_feature_2'] ?? 'Qualified doctor' }}"
                                                placeholder="e.g., Qualified doctor">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3 mb-3">
                                            <label for="banner_feature_3">Feature 3</label>
                                            <input type="text" class="form-control" id="banner_feature_3"
                                                name="banner_feature_3"
                                                value="{{ $bannerSettings['banner_feature_3'] ?? 'Hi-tech machine' }}"
                                                placeholder="e.g., Hi-tech machine">
                                        </div>
                                    </div>
                                </div>

                                <h5 class="mb-3 mt-4">Stats Badge</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3 mb-3">
                                            <label for="banner_stats_text">Stats Text</label>
                                            <input type="text" class="form-control" id="banner_stats_text"
                                                name="banner_stats_text"
                                                value="{{ $bannerSettings['banner_stats_text'] ?? '10K+ Happy Patients' }}"
                                                placeholder="e.g., 10K+ Happy Patients">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3 mb-3">
                                            <label for="banner_rating">Rating</label>
                                            <input type="text" class="form-control" id="banner_rating" name="banner_rating"
                                                value="{{ $bannerSettings['banner_rating'] ?? '4.9' }}"
                                                placeholder="e.g., 4.9">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Image Settings -->
                            <div class="col-md-4">
                                <h5 class="mb-3">Banner Image</h5>

                                <div class="mb-3 mb-3">
                                    <label for="banner_image">Hero Image</label>
                                    <input type="file" class="form-control @error('banner_image') is-invalid @enderror"
                                        id="banner_image" name="banner_image" accept="image/*">
                                    @error('banner_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Recommended: 600x600px or larger</small>
                                </div>

                                @if(!empty($bannerSettings['banner_image']))
                                    <div class="mt-3">
                                        <label>Current Image:</label>
                                        <div class="border rounded p-2">
                                            <img src="{{ asset($bannerSettings['banner_image']) }}" alt="Banner Image"
                                                class="img-fluid rounded" style="max-height: 300px;">
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Save Banner Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Preview</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        To see your changes, save the settings and visit the <a href="{{ route('home') }}"
                            target="_blank">homepage</a>.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
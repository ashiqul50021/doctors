@extends('layouts.app')

@section('title', 'Social Media - ' . ($siteSettings['site_name'] ?? 'Doccure'))

@section('content')
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-md-5 col-lg-4 col-xl-3 theiaStickySidebar">
                @include('frontend.includes.doctor-sidebar')
            </div>
            <div class="col-md-7 col-lg-8 col-xl-9">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Social Media Links</h4>

                        @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <form action="{{ route('social.media.update') }}" method="POST">
                            @csrf
                            <div class="form-group mb-3">
                                <label>Website URL</label>
                                <input type="url" class="form-control" name="website"
                                    value="{{ old('website', $doctor->website) }}" placeholder="https://www.example.com">
                            </div>
                            <div class="form-group mb-3">
                                <label>Facebook URL</label>
                                <input type="url" class="form-control" name="facebook"
                                    value="{{ old('facebook', $doctor->facebook) }}" placeholder="https://www.facebook.com/username">
                            </div>
                            <div class="form-group mb-3">
                                <label>LinkedIn URL</label>
                                <input type="url" class="form-control" name="linkedin"
                                    value="{{ old('linkedin', $doctor->linkedin) }}" placeholder="https://www.linkedin.com/in/username">
                            </div>
                            <div class="submit-section">
                                <button type="submit" class="btn btn-primary submit-btn">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

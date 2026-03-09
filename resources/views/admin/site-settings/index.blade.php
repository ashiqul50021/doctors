@extends('layouts.admin')

@section('title', 'Site Settings - ' . ($siteSettings['site_name'] ?? 'Doccure Admin'))

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Site Settings</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Site Settings</li>
                </ul>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        <!-- General Settings -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"><i class="fas fa-cog me-2"></i>General Settings</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.site-settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label>Website Name</label>
                            <input type="text" class="form-control" name="site_name"
                                value="{{ $generalSettings['site_name'] ?? 'Doccure' }}">
                        </div>

                        <div class="mb-3">
                            <label>Tagline</label>
                            <input type="text" class="form-control" name="site_tagline"
                                value="{{ $generalSettings['site_tagline'] ?? '' }}">
                        </div>

                        <div class="mb-3">
                            <label>Website Logo</label>
                            <div class="mb-2">
                                <img id="logoPreview"
                                    src="{{ !empty($generalSettings['logo']) ? asset($generalSettings['logo']) : '' }}"
                                    alt="Logo Preview"
                                    style="max-height: 50px; {{ empty($generalSettings['logo']) ? 'display:none;' : '' }}">
                            </div>
                            <input type="file" class="form-control" name="logo" id="logoInput" accept="image/*">
                            <small class="text-muted">Recommended size: 200px x 50px</small>
                        </div>

                        <div class="mb-3">
                            <label>Favicon</label>
                            <div class="mb-2">
                                <img id="faviconPreview"
                                    src="{{ !empty($generalSettings['favicon']) ? asset($generalSettings['favicon']) : '' }}"
                                    alt="Favicon Preview"
                                    style="max-height: 32px; {{ empty($generalSettings['favicon']) ? 'display:none;' : '' }}">
                            </div>
                            <input type="file" class="form-control" name="favicon" id="faviconInput" accept="image/*">
                            <small class="text-muted">Recommended size: 32px x 32px (PNG or ICO)</small>
                        </div>

                        <button type="submit" class="btn btn-primary">Save General Settings</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Contact Settings -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"><i class="fas fa-phone me-2"></i>Contact Settings</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.site-settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label>Contact Email</label>
                            <input type="email" class="form-control" name="contact_email"
                                value="{{ $contactSettings['contact_email'] ?? '' }}">
                        </div>

                        <div class="mb-3">
                            <label>Contact Phone</label>
                            <input type="text" class="form-control" name="contact_phone"
                                value="{{ $contactSettings['contact_phone'] ?? '' }}">
                        </div>

                        <div class="mb-3">
                            <label>Address</label>
                            <textarea class="form-control" name="contact_address"
                                rows="3">{{ $contactSettings['contact_address'] ?? '' }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Save Contact Settings</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Ecommerce Settings -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"><i class="fas fa-shopping-cart me-2"></i>Ecommerce Settings</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.site-settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>Shipping Charge (Inside Dhaka)</label>
                                    <input type="number" class="form-control" name="shipping_inside_dhaka"
                                        value="{{ $ecommerceSettings['shipping_inside_dhaka'] ?? '60' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>Shipping Charge (Outside Dhaka)</label>
                                    <input type="number" class="form-control" name="shipping_outside_dhaka"
                                        value="{{ $ecommerceSettings['shipping_outside_dhaka'] ?? '120' }}">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Save Ecommerce Settings</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Social Links -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"><i class="fas fa-share-alt me-2"></i>Social Links</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.site-settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label><i class="fab fa-facebook me-1"></i>Facebook URL</label>
                            <input type="url" class="form-control" name="facebook_url"
                                value="{{ $socialSettings['facebook_url'] ?? '' }}">
                        </div>

                        <div class="mb-3">
                            <label><i class="fab fa-twitter me-1"></i>Twitter URL</label>
                            <input type="url" class="form-control" name="twitter_url"
                                value="{{ $socialSettings['twitter_url'] ?? '' }}">
                        </div>

                        <div class="mb-3">
                            <label><i class="fab fa-instagram me-1"></i>Instagram URL</label>
                            <input type="url" class="form-control" name="instagram_url"
                                value="{{ $socialSettings['instagram_url'] ?? '' }}">
                        </div>

                        <div class="mb-3">
                            <label><i class="fab fa-linkedin me-1"></i>LinkedIn URL</label>
                            <input type="url" class="form-control" name="linkedin_url"
                                value="{{ $socialSettings['linkedin_url'] ?? '' }}">
                        </div>

                        <button type="submit" class="btn btn-primary">Save Social Links</button>
                    </form>
                </div>
            </div>
        </div>


    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            function bindPreview(inputId, previewId) {
                const input = document.getElementById(inputId);
                const preview = document.getElementById(previewId);
                if (!input || !preview) return;

                input.addEventListener('change', function (event) {
                    const file = event.target.files && event.target.files[0];
                    if (!file) return;
                    if (!file.type.startsWith('image/')) return;

                    const reader = new FileReader();
                    reader.onload = function (e) {
                        preview.src = e.target.result;
                        preview.style.display = 'inline-block';
                    };
                    reader.readAsDataURL(file);
                });
            }

            bindPreview('logoInput', 'logoPreview');
            bindPreview('faviconInput', 'faviconPreview');
        })();
    </script>
@endpush

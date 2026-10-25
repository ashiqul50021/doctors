@extends('layouts.admin')

@section('title', 'Site Settings - ' . ($siteSettings['site_name'] ?? 'abcsheba Admin'))

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
                                value="{{ $generalSettings['site_name'] ?? 'abcsheba' }}">
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
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label>Shipping Charge (Inside Dhaka)</label>
                                    <input type="number" class="form-control" name="shipping_inside_dhaka"
                                        value="{{ $ecommerceSettings['shipping_inside_dhaka'] ?? '60' }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label>Shipping Charge (Outside Dhaka)</label>
                                    <input type="number" class="form-control" name="shipping_outside_dhaka"
                                        value="{{ $ecommerceSettings['shipping_outside_dhaka'] ?? '120' }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label>Default Agent Coupon Discount (%)</label>
                                    <input type="number" step="0.01" min="0" max="100" class="form-control" name="agent_default_coupon_discount"
                                        value="{{ $ecommerceSettings['agent_default_coupon_discount'] ?? '5' }}">
                                    <small class="text-muted">Default discount (%) given when agents create coupons.</small>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Save Ecommerce Settings</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- Payment Gateway Settings -->
        <div class="col-lg-12 mt-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"><i class="fas fa-credit-card me-2"></i>Payment Gateway (SSLCommerz)</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.site-settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>Store ID</label>
                                    <input type="text" class="form-control" name="sslcz_store_id"
                                        value="{{ $ecommerceSettings['sslcz_store_id'] ?? 'testbox' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>Store Password</label>
                                    <input type="text" class="form-control" name="sslcz_store_password"
                                        value="{{ $ecommerceSettings['sslcz_store_password'] ?? 'qwerty' }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="sslcz_testmode" name="sslcz_testmode" value="1" {{ isset($ecommerceSettings['sslcz_testmode']) && $ecommerceSettings['sslcz_testmode'] == '1' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="sslcz_testmode">Enable Test Mode (Sandbox)</label>
                                    </div>
                                    <small class="text-muted">Uncheck this when making your site live.</small>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Save SSLCommerz Settings</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Manual Mobile Banking (bKash / Nagad / Rocket) Settings -->
        <div class="col-lg-12 mt-4">
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-mobile-alt me-2 text-primary"></i>Manual Payment Settings (bKash, Nagad, Rocket)
                    </h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.site-settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="manual_payment_enabled" name="manual_payment_enabled" value="1" {{ (!isset($ecommerceSettings['manual_payment_enabled']) || $ecommerceSettings['manual_payment_enabled'] == '1') ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="manual_payment_enabled">Enable Manual Payment for Online Consultation</label>
                                </div>
                                <small class="text-muted">অনলাইন কনসালটেশনে রোগীরা সরাসরি বিকাশ/নগদ/রকেটে টাকা পাঠিয়ে TrxID দিয়ে বুকিং কনফার্ম করতে পারবে।</small>
                            </div>

                            <!-- bKash -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="fw-semibold text-danger"><i class="fas fa-wallet me-1"></i> bKash Number</label>
                                    <input type="text" class="form-control" name="manual_bkash_number"
                                        placeholder="e.g. 017XXXXXXXX"
                                        value="{{ $ecommerceSettings['manual_bkash_number'] ?? '01711111111' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="fw-semibold">bKash Account Type</label>
                                    <select class="form-select" name="manual_bkash_type">
                                        <option value="Personal (Send Money)" {{ ($ecommerceSettings['manual_bkash_type'] ?? '') == 'Personal (Send Money)' ? 'selected' : '' }}>Personal (Send Money)</option>
                                        <option value="Merchant (Payment)" {{ ($ecommerceSettings['manual_bkash_type'] ?? '') == 'Merchant (Payment)' ? 'selected' : '' }}>Merchant (Payment)</option>
                                        <option value="Agent (Cash In)" {{ ($ecommerceSettings['manual_bkash_type'] ?? '') == 'Agent (Cash In)' ? 'selected' : '' }}>Agent (Cash In)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Nagad -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="fw-semibold text-warning"><i class="fas fa-wallet me-1"></i> Nagad Number</label>
                                    <input type="text" class="form-control" name="manual_nagad_number"
                                        placeholder="e.g. 018XXXXXXXX"
                                        value="{{ $ecommerceSettings['manual_nagad_number'] ?? '01811111111' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="fw-semibold">Nagad Account Type</label>
                                    <select class="form-select" name="manual_nagad_type">
                                        <option value="Personal (Send Money)" {{ ($ecommerceSettings['manual_nagad_type'] ?? '') == 'Personal (Send Money)' ? 'selected' : '' }}>Personal (Send Money)</option>
                                        <option value="Merchant (Payment)" {{ ($ecommerceSettings['manual_nagad_type'] ?? '') == 'Merchant (Payment)' ? 'selected' : '' }}>Merchant (Payment)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Rocket -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="fw-semibold text-primary"><i class="fas fa-wallet me-1"></i> Rocket Number</label>
                                    <input type="text" class="form-control" name="manual_rocket_number"
                                        placeholder="e.g. 019XXXXXXXX"
                                        value="{{ $ecommerceSettings['manual_rocket_number'] ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="fw-semibold">Rocket Account Type</label>
                                    <select class="form-select" name="manual_rocket_type">
                                        <option value="Personal (Send Money)" {{ ($ecommerceSettings['manual_rocket_type'] ?? '') == 'Personal (Send Money)' ? 'selected' : '' }}>Personal (Send Money)</option>
                                        <option value="Merchant (Payment)" {{ ($ecommerceSettings['manual_rocket_type'] ?? '') == 'Merchant (Payment)' ? 'selected' : '' }}>Merchant (Payment)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Instructions -->
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="fw-semibold">Special Instructions for Patient</label>
                                    <textarea class="form-control" name="manual_payment_instruction" rows="2"
                                        placeholder="টাকা পাঠিয়ে নিচে আপনার সেন্ডার নম্বর ও TrxID ইনপুট দিন।">{{ $ecommerceSettings['manual_payment_instruction'] ?? 'আমাদের বিকাশ বা নগদ নম্বরে Send Money করে নিচের বক্সে আপনার নম্বর ও Transaction ID (TrxID) প্রদান করুন।' }}</textarea>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Save Manual Payment Settings</button>
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

        <!-- Legal & Policy Pages -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"><i class="fas fa-file-contract me-2"></i>Legal & Policy Pages (Terms & Privacy)</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.site-settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-bold">Terms & Conditions Content (HTML / Text)</label>
                            <textarea class="form-control" name="terms_conditions" rows="8" placeholder="Enter terms and conditions content...">{{ $generalSettings['terms_conditions'] ?? ($settings['terms_conditions'] ?? '') }}</textarea>
                            <small class="text-muted">You can write plain text or HTML. If left empty, default medical terms will be displayed.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Privacy Policy Content (HTML / Text)</label>
                            <textarea class="form-control" name="privacy_policy" rows="8" placeholder="Enter privacy policy content...">{{ $generalSettings['privacy_policy'] ?? ($settings['privacy_policy'] ?? '') }}</textarea>
                            <small class="text-muted">You can write plain text or HTML. If left empty, default privacy policy will be displayed.</small>
                        </div>

                        <button type="submit" class="btn btn-primary">Save Legal Pages</button>
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

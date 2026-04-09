<!-- Footer -->
<footer class="footer">
    @php
        $siteName = trim((string) ($siteSettings['site_name'] ?? 'ABCSHEBA'));
        $footerDescription = 'Book appointments with the best doctors and specialists nearest to you.';
        $contactAddress = trim((string) ($siteSettings['contact_address'] ?? 'House 12, Road 5, Dhanmondi, Dhaka, Bangladesh'));
        $contactPhone = trim((string) ($siteSettings['contact_phone'] ?? '+880 1712 345 678'));
        $contactEmail = trim((string) ($siteSettings['contact_email'] ?? 'info@abcsheba.com'));
        $phoneHref = preg_replace('/[^0-9+]/', '', $contactPhone);

        $footerLogoCandidates = array_filter([
            $siteSettings['footer_logo'] ?? null,
            $siteSettings['logo'] ?? null,
            'assets/img/footer-logo.png',
            'assets/img/logo.png',
        ], fn ($path) => filled($path));

        $footerLogo = collect($footerLogoCandidates)->first(function ($path) {
            $normalizedPath = ltrim((string) $path, '/');

            return file_exists(base_path($normalizedPath)) || file_exists(public_path($normalizedPath));
        }) ?? 'assets/img/footer-logo.png';

        $socialLinks = [
            ['url' => $siteSettings['facebook_url'] ?? null, 'icon' => 'fab fa-facebook-f', 'label' => 'Facebook'],
            ['url' => $siteSettings['twitter_url'] ?? null, 'icon' => 'fab fa-twitter', 'label' => 'Twitter'],
            ['url' => $siteSettings['linkedin_url'] ?? null, 'icon' => 'fab fa-linkedin-in', 'label' => 'LinkedIn'],
            ['url' => $siteSettings['instagram_url'] ?? null, 'icon' => 'fab fa-instagram', 'label' => 'Instagram'],
        ];
        $hasSocialLinks = collect($socialLinks)->contains(fn ($social) => filled($social['url']));
    @endphp

    <!-- Footer Top -->
    <div class="footer-top">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6">

                    <!-- Footer Widget -->
                    <div class="footer-widget footer-about">
                        <div class="footer-logo">
                            <a href="{{ route('home') }}">
                                <img
                                    src="{{ asset($footerLogo) }}"
                                    alt="{{ $siteName }} logo"
                                    class="img-fluid"
                                    style="max-height: 52px; width: auto;"
                                    onerror="this.onerror=null;this.src='{{ asset('assets/img/footer-logo.png') }}';">
                            </a>
                        </div>
                        <div class="footer-about-content">
                            <p>{{ $footerDescription }}</p>
                            @if($hasSocialLinks)
                                <div class="social-icon">
                                    <ul>
                                        @foreach($socialLinks as $social)
                                            @continue(blank($social['url']))
                                            <li>
                                                <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer"
                                                    aria-label="{{ $social['label'] }}">
                                                    <i class="{{ $social['icon'] }}"></i>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                    <!-- /Footer Widget -->

                </div>

                <div class="col-lg-3 col-md-6">

                    <!-- Footer Widget -->
                    <div class="footer-widget footer-menu">
                        <h2 class="footer-title">For Patients</h2>
                        <ul>
                            <li><a href="{{ route('doctors.search') }}"><i class="fas fa-angle-double-right"></i> Search for
                                    Doctors</a></li>
                            <li><a href="{{ route('login') }}"><i class="fas fa-angle-double-right"></i> Login</a></li>
                            <li><a href="{{ route('register') }}"><i class="fas fa-angle-double-right"></i> Register</a>
                            </li>
                            <li><a href="{{ route('doctors.search') }}"><i class="fas fa-angle-double-right"></i> Booking</a>
                            </li>
                            <li><a href="{{ route('patient.dashboard') }}"><i class="fas fa-angle-double-right"></i>
                                    Patient Dashboard</a></li>
                        </ul>
                    </div>
                    <!-- /Footer Widget -->

                </div>

                <div class="col-lg-3 col-md-6">

                    <!-- Footer Widget -->
                    <div class="footer-widget footer-menu">
                        <h2 class="footer-title">For Doctors</h2>
                        <ul>
                            <li><a href="{{ route('doctors.appointments') }}"><i class="fas fa-angle-double-right"></i>
                                    Appointments</a></li>
                            <li><a href="{{ route('chat.doctor') }}"><i class="fas fa-angle-double-right"></i> Chat</a></li>
                            <li><a href="{{ route('doctor.login') }}"><i class="fas fa-angle-double-right"></i> Login</a></li>
                            <li><a href="{{ route('doctor.register') }}"><i class="fas fa-angle-double-right"></i>
                                    Register</a></li>
                            <li><a href="{{ route('doctors.dashboard') }}"><i class="fas fa-angle-double-right"></i>
                                    Doctor Dashboard</a></li>
                        </ul>
                    </div>
                    <!-- /Footer Widget -->

                </div>

                <div class="col-lg-3 col-md-6">

                    <!-- Footer Widget -->
                    <div class="footer-widget footer-contact">
                        <h2 class="footer-title">Contact Us</h2>
                        <div class="footer-contact-info">
                            <div class="footer-address">
                                <span><i class="fas fa-map-marker-alt"></i></span>
                                <p>{!! nl2br(e($contactAddress)) !!}</p>
                            </div>
                            <p>
                                <i class="fas fa-phone-alt"></i>
                                <a href="tel:{{ $phoneHref ?: $contactPhone }}">{{ $contactPhone }}</a>
                            </p>
                            <p class="mb-0">
                                <i class="fas fa-envelope"></i>
                                <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>
                            </p>
                        </div>
                    </div>
                    <!-- /Footer Widget -->

                </div>

            </div>
        </div>
    </div>
    <!-- /Footer Top -->

    <!-- Footer Bottom -->
    <div class="footer-bottom">
        <div class="container">
            <!-- Copyright -->
            <div class="copyright">
                <div class="row">
                    <div class="col-md-6 col-lg-6">
                        <div class="copyright-text">
                            <p class="mb-0">&copy; {{ date('Y') }} {{ \Illuminate\Support\Str::upper($siteName) }}. All rights reserved.</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6">
                        <!-- Copyright Menu -->
                        <div class="copyright-menu">
                            <ul class="policy-menu">
                                <li><a href="{{ route('terms') }}">Terms and Conditions</a></li>
                                <li><a href="{{ route('privacy') }}">Privacy Policy</a></li>
                            </ul>
                        </div>
                        <!-- /Copyright Menu -->
                    </div>
                </div>
            </div>
            <!-- /Copyright -->
        </div>
    </div>
    <!-- /Footer Bottom -->

</footer>
<!-- /Footer -->

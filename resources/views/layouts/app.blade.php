<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $siteSettings['site_name'] ?? 'abcsheba.com')</title>

    <!-- Favicons -->
    <link type="image/x-icon" href="{{ asset('assets/img/favicon.png') }}" rel="icon">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/all.min.css') }}">

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Select2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">

    @stack('styles')

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    <!-- Custom Header & Banner CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/header-banner-custom.css') }}">

    <!-- Mobile Bottom Nav CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/mobile-nav.css') }}">
</head>

<body class="@yield('body_class')">
    <!-- Main Wrapper -->
    <div class="main-wrapper">

        @include('layouts.partials.header')

        @yield('content')

        @include('layouts.partials.footer')

        @include('layouts.partials.bottom-nav')

    </div>
    <!-- /Main Wrapper -->

    <!-- jQuery -->
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>

    <!-- Popper JS (required for Bootstrap 5 dropdowns) -->
    <script src="{{ asset('assets/js/popper.min.js') }}"></script>

    <!-- Bootstrap Core JS -->
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>

    <!-- Slick JS -->
    <script src="{{ asset('assets/js/slick.js') }}"></script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000"
        };
        @if(session('success'))
            toastr.success("{{ session('success') }}");
        @endif
        @if(session('error'))
            toastr.error("{{ session('error') }}");
        @endif
    </script>

    <script>
        $(document).ready(function () {
            // Intercept only Add to Cart forms so cart update/remove screens can use their own handlers.
            $('form[action*="cart/add"]').on('submit', function (e) {
                // Check if it's a Buy Now submit
                if ($(document.activeElement).hasClass('btn-buy-modern') || $(document.activeElement).val() == '1') {
                    return; // Allow default submission
                }

                e.preventDefault();
                var $form = $(this);
                var $btn = $form.find('button[title="Add to Cart"], button[type="submit"]:not(.btn-buy-modern)');

                // Animation Logic
                var cartIcon = $('#cart-icon-btn');
                var imgToClone = $form.closest('.product-card-modern').find('img').eq(0);

                // Fallback for details page
                if (imgToClone.length === 0) {
                    imgToClone = $('.product-image-main img').eq(0);
                }

                if (imgToClone.length && cartIcon.length) {
                    var imgClone = imgToClone.clone()
                        .offset({
                            top: imgToClone.offset().top,
                            left: imgToClone.offset().left
                        })
                        .css({
                            'opacity': '0.8',
                            'position': 'absolute',
                            'height': '150px',
                            'width': '150px',
                            'z-index': '9999',
                            'border-radius': '50%'
                        })
                        .appendTo($('body'))
                        .animate({
                            'top': cartIcon.offset().top + 10,
                            'left': cartIcon.offset().left + 10,
                            'width': '20px',
                            'height': '20px'
                        }, 800, 'swing'); // Reduced speed for better visibility

                    imgClone.animate({
                        'width': 0,
                        'height': 0
                    }, function () {
                        $(this).detach();
                    });
                }

                $.ajax({
                    url: $form.attr('action'),
                    method: 'POST',
                    data: $form.serialize(),
                    success: function (response) {
                        if (response.success) {
                            toastr.success(response.message);
                            // Update cart count
                            var badge = $('#cart-icon-btn .cart-badge');
                            if (badge.length) {
                                badge.text(response.cartCount);
                            } else {
                                $('#cart-icon-btn').append('<span class="cart-badge">' + response.cartCount + '</span>');
                            }
                            // Trigger wiggle animation
                            var $cartWrapper = $('#cart-icon-btn');
                            $cartWrapper.addClass('wiggle');
                            setTimeout(function() { $cartWrapper.removeClass('wiggle'); }, 600);
                        } else {
                            toastr.error('Something went wrong!'); // Fallback
                        }
                    },
                    error: function (xhr) {
                        toastr.error('Failed to add to cart.');
                    }
                });
            });
        });
    </script>

    @auth
        <script>
            (function () {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (!token) return;

                const pingHeartbeat = function () {
                    fetch('{{ route('heartbeat') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: '{}',
                        keepalive: true
                    }).catch(function () {});
                };

                pingHeartbeat();
                setInterval(pingHeartbeat, 60000);
            })();
        </script>
    @endauth

    @stack('scripts')

    <!-- Custom JS -->
    <script src="{{ asset('assets/js/script.js') }}"></script>
</body>

</html>

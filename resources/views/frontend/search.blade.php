@extends('layouts.app')

@section('title', 'Search Doctors - Doccure')

@push('styles')
<!-- Datetimepicker CSS -->
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap-datetimepicker.min.css') }}">

<!-- Fancybox CSS -->
<link rel="stylesheet" href="{{ asset('assets/plugins/fancybox/jquery.fancybox.min.css') }}">

<link rel="stylesheet" href="{{ asset('assets/css/premium-search.css') }}">
@endpush

@section('content')

<!-- Page Content -->
<div class="content" style="padding-top: 100px; background: #f8fafc; min-height: 100vh;">
    <div class="container">

        <div class="row">
            <div class="col-md-12 col-lg-4 col-xl-3 theiaStickySidebar">

                <!-- Premium Search Filter -->
                <form id="searchFilterForm" method="GET" action="{{ route('doctors.search') }}">
                <div class="search-filter-premium">
                    <!-- Header -->
                    <div class="filter-header">
                        <h4>FILTERS</h4>
                        <button type="button" class="reset-btn" onclick="resetFilters()">
                            Clear All
                        </button>
                    </div>

                    <!-- Filter Body -->
                    <div class="filter-body">

                        <!-- Location Section -->
                        <div class="filter-section">
                            <div class="filter-section-header" onclick="toggleSection(this)">
                                <h5>LOCATION</h5>
                                <i class="fas fa-chevron-down toggle-icon"></i>
                            </div>
                            <div class="filter-section-content">
                                <select name="district_id" class="custom-filter-select" id="filterDistrict">
                                    <option value="">Select District</option>
                                    @foreach($districts as $district)
                                        <option value="{{ $district->id }}" {{ request('district_id') == $district->id ? 'selected' : '' }}>
                                            {{ $district->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div style="margin-top: 12px;">
                                    <select name="area_id" class="custom-filter-select" id="filterArea" {{ !request('district_id') ? 'disabled' : '' }}>
                                        <option value="">Select Area</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Speciality Section -->
                        <div class="filter-section">
                            <div class="filter-section-header" onclick="toggleSection(this)">
                                <h5>SPECIALITY</h5>
                                <i class="fas fa-chevron-down toggle-icon"></i>
                            </div>
                            <div class="filter-section-content" style="max-height: 240px; overflow-y: auto; padding-right: 4px;">
                                @foreach($specialities as $speciality)
                                <label class="custom-filter-check">
                                    <input type="checkbox" name="select_specialist[]" value="{{ $speciality->id }}"
                                        {{ in_array($speciality->id, (array)request('select_specialist')) ? 'checked' : '' }}>
                                    <span class="check-box"></span>
                                    <span class="check-label">{{ $speciality->name }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Gender Section -->
                        <div class="filter-section">
                            <div class="filter-section-header" onclick="toggleSection(this)">
                                <h5>GENDER</h5>
                                <i class="fas fa-chevron-down toggle-icon"></i>
                            </div>
                            <div class="filter-section-content">
                                <div class="d-flex align-items-center" style="gap: 12px; background: #f3f4f6; padding: 4px; border-radius: 14px;">
                                    <label class="gender-pill {{ in_array('male', (array)request('gender')) ? 'active' : '' }}" style="margin: 0;">
                                        <input type="checkbox" name="gender[]" value="male"
                                            {{ in_array('male', (array)request('gender')) ? 'checked' : '' }}>
                                        Male
                                    </label>
                                    <label class="gender-pill {{ in_array('female', (array)request('gender')) ? 'active' : '' }}" style="margin: 0;">
                                        <input type="checkbox" name="gender[]" value="female"
                                            {{ in_array('female', (array)request('gender')) ? 'checked' : '' }}>
                                        Female
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Fee Range Section -->
                        <div class="filter-section">
                            <div class="filter-section-header" onclick="toggleSection(this)">
                                <h5>CONSULTATION FEE</h5>
                                <i class="fas fa-chevron-down toggle-icon"></i>
                            </div>
                            <div class="filter-section-content">
                                <div class="fee-range-container">
                                    <div class="fee-range-inputs">
                                        <div class="input-group">
                                            <span class="currency-symbol">৳</span>
                                            <input type="number" name="fee_min" placeholder="Min" value="{{ request('fee_min') }}">
                                        </div>
                                        <span class="separator"></span>
                                        <div class="input-group">
                                            <span class="currency-symbol">৳</span>
                                            <input type="number" name="fee_max" placeholder="Max" value="{{ request('fee_max') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Experience Section -->
                        <div class="filter-section">
                            <div class="filter-section-header" onclick="toggleSection(this)">
                                <h5>EXPERIENCE</h5>
                                <i class="fas fa-chevron-down toggle-icon"></i>
                            </div>
                            <div class="filter-section-content">
                                <select name="experience" class="custom-filter-select">
                                    <option value="">Any Experience</option>
                                    <option value="1" {{ request('experience') == '1' ? 'selected' : '' }}>1+ Years</option>
                                    <option value="5" {{ request('experience') == '5' ? 'selected' : '' }}>5+ Years</option>
                                    <option value="10" {{ request('experience') == '10' ? 'selected' : '' }}>10+ Years</option>
                                    <option value="15" {{ request('experience') == '15' ? 'selected' : '' }}>15+ Years</option>
                                    <option value="20" {{ request('experience') == '20' ? 'selected' : '' }}>20+ Years</option>
                                </select>
                            </div>
                        </div>

                        <!-- Rating Section -->
                        <div class="filter-section">
                            <div class="filter-section-header" onclick="toggleSection(this)">
                                <h5>RATING</h5>
                                <i class="fas fa-chevron-down toggle-icon"></i>
                            </div>
                            <div class="filter-section-content">
                                <div class="rating-filter">
                                    <label class="rating-option {{ request('rating') == '4' ? 'selected' : '' }}">
                                        <input type="radio" name="rating" value="4" {{ request('rating') == '4' ? 'checked' : '' }}>
                                        <div class="stars">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star empty"></i>
                                        </div>
                                        <span>4+ Stars</span>
                                    </label>
                                    <label class="rating-option {{ request('rating') == '3' ? 'selected' : '' }}">
                                        <input type="radio" name="rating" value="3" {{ request('rating') == '3' ? 'checked' : '' }}>
                                        <div class="stars">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star empty"></i>
                                            <i class="fas fa-star empty"></i>
                                        </div>
                                        <span>3+ Stars</span>
                                    </label>
                                    <label class="rating-option {{ request('rating') == '' ? 'selected' : '' }}">
                                        <input type="radio" name="rating" value="" {{ !request('rating') ? 'checked' : '' }}>
                                        <div class="stars">
                                            <i class="fas fa-star empty"></i>
                                            <i class="fas fa-star empty"></i>
                                            <i class="fas fa-star empty"></i>
                                            <i class="fas fa-star empty"></i>
                                            <i class="fas fa-star empty"></i>
                                        </div>
                                        <span>All Ratings</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Services Section -->
                        <div class="filter-section">
                            <div class="filter-section-header" onclick="toggleSection(this)">
                                <h5>SERVICES</h5>
                                <i class="fas fa-chevron-down toggle-icon"></i>
                            </div>
                            <div class="filter-section-content">
                                <div class="toggle-filter">
                                    <span class="toggle-label">
                                        Online Consultation
                                    </span>
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="online_consultation" value="1"
                                            {{ request('online_consultation') == '1' ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                                <div class="toggle-filter">
                                    <span class="toggle-label">
                                        Home Visit
                                    </span>
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="home_visit" value="1"
                                            {{ request('home_visit') == '1' ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                                <div class="toggle-filter">
                                    <span class="toggle-label">
                                        Verified Only
                                    </span>
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="verified_only" value="1"
                                            {{ request('verified_only') == '1' ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Search Button -->
                    <div class="filter-search-btn">
                        <button type="submit">
                            <i class="fas fa-search"></i> Apply Filters
                        </button>
                    </div>
                </div>
                </form>
                <!-- /Premium Search Filter -->

            </div>

            <div class="col-md-12 col-lg-8 col-xl-9">

                <div id="doctor-list-container">
                    @include('frontend.super-doctor-list')
                </div>

            </div>
        </div>

    </div>

</div>
<!-- /Page Content -->
@endsection

@push('scripts')
<!-- Sticky Sidebar JS -->
<script src="{{ asset('assets/plugins/theia-sticky-sidebar/ResizeSensor.js') }}"></script>
<script src="{{ asset('assets/plugins/theia-sticky-sidebar/theia-sticky-sidebar.js') }}"></script>

<!-- Datetimepicker JS -->
<script src="{{ asset('assets/js/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap-datetimepicker.min.js') }}"></script>

<!-- Fancybox JS -->
<script src="{{ asset('assets/plugins/fancybox/jquery.fancybox.min.js') }}"></script>

<script>
    // Toggle filter section
    function toggleSection(header) {
        var section = header.closest('.filter-section');
        section.classList.toggle('collapsed');
    }

    // Reset all filters
    function resetFilters() {
        window.location.href = '{{ route("search") }}';
    }

    // Change sort (AJAX)
    function changeSortBy(value) {
        // Update URL param without reload
        var url = new URL(window.location.href);
        url.searchParams.set('sort_by', value);
        window.history.pushState({}, '', url);

        submitFilterForm();
    }

    // Auto-submit form function (AJAX)
    function submitFilterForm() {
        var $form = $('#searchFilterForm');
        var formData = $form.serialize();
        var sortBy = new URL(window.location.href).searchParams.get('sort_by') || 'relevance';

        // Add sort_by to formData
        if (formData) {
            formData += '&sort_by=' + sortBy;
        } else {
            formData = 'sort_by=' + sortBy;
        }

        // Show loading state (optional: add overlay or spinner)
        $('#doctor-list-container').css('opacity', '0.5');

        $.ajax({
            url: '{{ route("search") }}',
            type: 'GET',
            data: formData,
            success: function(response) {
                $('#doctor-list-container').html(response).css('opacity', '1');

                // Update URL without reload
                var newUrl = new URL(window.location.href);
                // We should update all params from form but for now just pushState is enough visually
                // A better way is construct URL from formData but let's keep it simple
            },
            error: function() {
                alert('Something went wrong. Please try again.');
                $('#doctor-list-container').css('opacity', '1');
            }
        });
    }

    // Debounce function for inputs
    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    // District change - load areas AND submit
    $('#filterDistrict').on('change', function() {
        var districtId = $(this).val();
        var $areaSelect = $('#filterArea');

        // Allow interacting with form without reload
        submitFilterForm();

        if (districtId) {
            $areaSelect.prop('disabled', false);
            // Don't clear content immediately, looks bad. Just load.

            $.ajax({
                url: '/api/areas/' + districtId,
                type: 'GET',
                dataType: 'json',
                success: function(areas) {
                    var html = '<option value="">All Areas</option>';
                    $.each(areas, function(key, area) {
                        html += '<option value="' + area.id + '">' + area.name + '</option>';
                    });
                    $areaSelect.html(html);
                }
            });
        } else {
            $areaSelect.prop('disabled', true);
            $areaSelect.html('<option value="">Select District First</option>');
        }
    });

    // Other inputs change - Auto Submit
    $('#filterArea, input[name="select_specialist[]"], input[name="gender[]"], input[name="experience"], input[name="rating"], input[name="online_consultation"], input[name="home_visit"], input[name="verified_only"]').on('change', function() {
        submitFilterForm();
    });

    // Select dropdowns specifically (if not covered above)
    $('select[name="experience"]').on('change', function() {
        submitFilterForm();
    });

    // Prevent form actual submit
    $('#searchFilterForm').on('submit', function(e) {
        e.preventDefault();
        submitFilterForm();
    });

    // Fee inputs - Debounce submit (wait 1 sec after typing stops)
    const debouncedSubmit = debounce(() => submitFilterForm(), 1000);
    $('input[name="fee_min"], input[name="fee_max"]').on('input', debouncedSubmit);

    // Rating option click
    $('.rating-option').on('click', function() {
        $('.rating-option').removeClass('selected');
        $(this).addClass('selected');
        // Specific handling for radio button trigger
        var radio = $(this).find('input[type="radio"]');
        radio.prop('checked', true).trigger('change');
    });

    // AJAX Pagination
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');

        $('#doctor-list-container').css('opacity', '0.5');
        $('html, body').animate({
            scrollTop: $(".sort-bar").offset().top - 100
        }, 500);

        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                $('#doctor-list-container').html(response).css('opacity', '1');
                window.history.pushState({}, '', url);
            },
            error: function() {
                alert('Something went wrong. Please try again.');
                $('#doctor-list-container').css('opacity', '1');
            }
        });
    });

    // Initialize sticky sidebar
    $(document).ready(function() {
        if ($(window).width() > 991) {
            $('.theiaStickySidebar').theiaStickySidebar({
                additionalMarginTop: 100
            });
        }

        // Hide submit button since we have auto-submit and AJAX
        $('.filter-search-btn').hide();

        // Load areas if district is selected (for initial page load)
        var selectedDistrict = '{{ request("district_id") }}';
        var selectedArea = '{{ request("area_id") }}';
        if (selectedDistrict) {
            $.ajax({
                url: '/api/areas/' + selectedDistrict,
                type: 'GET',
                dataType: 'json',
                success: function(areas) {
                    var html = '<option value="">All Areas</option>';
                    $.each(areas, function(key, area) {
                        var isSelected = area.id == selectedArea ? 'selected' : '';
                        html += '<option value="' + area.id + '" ' + isSelected + '>' + area.name + '</option>';
                    });
                    $('#filterArea').html(html).prop('disabled', false);

                    // Re-attach event listener for new elements
                    $('#filterArea').off('change').on('change', function() {
                        submitFilterForm();
                    });
                }
            });
        }
    });
</script>
@endpush

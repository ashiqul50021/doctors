@extends('layouts.app')

@section('title', 'Search Doctors - Doccure')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap-datetimepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/fancybox/jquery.fancybox.min.css') }}">

<style>
    .doctor-search-layout {
        padding-top: 8px;
        --filter-primary: #0f766e;
        --filter-primary-dark: #115e59;
        --filter-primary-soft: #ccfbf1;
        --filter-primary-soft-border: #99f6e4;
        --filter-focus: rgba(15, 118, 110, 0.18);
    }

    .filter-panel {
        border: 0;
        border-radius: 16px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
        overflow: hidden;
        position: sticky;
        top: 92px;
        background: #ffffff;
    }

    .filter-panel-header {
        background: #ffffff;
        color: #0f172a;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #e2e8f0;
    }

    .filter-panel-header h4 {
        font-size: 20px;
        font-weight: 700;
        margin: 0;
    }

    .filter-panel-header p {
        font-size: 13px;
        margin: 0;
        opacity: 1;
        color: #334155;
    }

    .filter-panel-body {
        background: #ffffff;
        padding: 18px;
    }

    .filter-block {
        margin-bottom: 18px;
        padding-bottom: 18px;
        border-bottom: 1px solid #edf2f7;
    }

    .filter-block:last-of-type {
        border-bottom: 0;
        margin-bottom: 14px;
        padding-bottom: 0;
    }

    .filter-title {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 10px;
        letter-spacing: 0.2px;
    }

    .filter-input,
    .filter-select {
        border: 1px solid #dbe3ef;
        border-radius: 10px;
        min-height: 44px;
        padding: 10px 12px;
        font-size: 14px;
        transition: all 0.2s ease;
        background-color: #ffffff;
    }

    .filter-input:focus,
    .filter-select:focus {
        border-color: var(--filter-primary);
        box-shadow: 0 0 0 0.2rem var(--filter-focus);
    }

    .filter-check {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        color: #334155;
        margin-bottom: 8px;
        cursor: pointer;
    }

    .filter-check input {
        width: 16px;
        height: 16px;
        accent-color: var(--filter-primary);
    }

    .speciality-search-box {
        margin-bottom: 12px;
    }

    .speciality-list {
        max-height: 220px;
        overflow-y: auto;
        padding-right: 4px;
        background: #ffffff;
    }

    .speciality-list::-webkit-scrollbar {
        width: 6px;
    }

    .speciality-list::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    .toggle-speciality-btn {
        border: 0;
        background: transparent;
        color: var(--filter-primary);
        font-size: 13px;
        font-weight: 600;
        padding: 0;
        margin-top: 8px;
    }

    .result-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }

    .result-count h5 {
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }

    .result-count p {
        margin: 0;
        font-size: 13px;
        color: #64748b;
    }

    .active-filters {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }

    .filter-chip {
        background: var(--filter-primary-soft);
        color: var(--filter-primary-dark);
        border: 1px solid var(--filter-primary-soft-border);
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        padding: 6px 10px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .filter-chip-clear {
        color: var(--filter-primary-dark);
        text-decoration: none;
        font-size: 12px;
        font-weight: 700;
    }

    .sponsored-ad-card {
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        overflow: hidden;
        margin-bottom: 24px;
        background: #fff;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
    }

    .empty-state {
        background: #fff;
        border-radius: 16px;
        border: 1px dashed #cbd5e1;
        padding: 48px 20px;
        text-align: center;
    }

    @media (max-width: 991.98px) {
        .filter-panel {
            position: static;
            margin-bottom: 16px;
        }

        .result-toolbar {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@endpush

@section('content')
@php
    $selectedSpecialities = collect(request()->input('select_specialist', []))->map(fn ($id) => (int) $id)->all();
    $activeFilters = [];

    if (request()->filled('keywords')) {
        $activeFilters[] = ['label' => 'Keyword', 'value' => request('keywords')];
    }

    if (request()->filled('location')) {
        $activeFilters[] = ['label' => 'Location', 'value' => request('location')];
    }

    if (request()->filled('district_id')) {
        $district = $districts->firstWhere('id', (int) request('district_id'));
        if ($district) {
            $activeFilters[] = ['label' => 'District', 'value' => $district->name];
        }
    }

    if (request()->filled('area_id')) {
        $area = $areas->firstWhere('id', (int) request('area_id'));
        if ($area) {
            $activeFilters[] = ['label' => 'Area', 'value' => $area->name];
        }
    }

    foreach ($specialities as $speciality) {
        if (in_array((int) $speciality->id, $selectedSpecialities, true)) {
            $activeFilters[] = ['label' => 'Speciality', 'value' => $speciality->name];
        }
    }
@endphp

<div class="content doctor-search-layout">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-lg-4 col-xl-3 theiaStickySidebar">
                <div class="card filter-panel">
                    <div class="filter-panel-header">
                        <div>
                            <h4>Filters</h4>
                            <p>Refine doctor search instantly</p>
                        </div>
                        <span class="badge bg-light text-primary">{{ $doctors->total() }} Results</span>
                    </div>

                    <div class="filter-panel-body">
                        <form method="GET" action="{{ route('doctors.search') }}" id="doctorFilterForm">
                            <div class="filter-block">
                                <div class="filter-title">Search</div>
                                <input type="text" name="keywords" value="{{ request('keywords') }}" class="form-control filter-input" placeholder="Doctor name / speciality">
                            </div>

                            <div class="filter-block">
                                <div class="filter-title">Location</div>
                                <input type="text" name="location" value="{{ request('location') }}" class="form-control filter-input mb-2" placeholder="Clinic city or address">
                                <select name="district_id" id="districtSelect" class="form-select filter-select mb-2">
                                    <option value="">All Districts</option>
                                    @foreach($districts as $district)
                                        <option value="{{ $district->id }}" {{ (string) request('district_id') === (string) $district->id ? 'selected' : '' }}>
                                            {{ $district->name }}
                                        </option>
                                    @endforeach
                                </select>

                                <select name="area_id" id="areaSelect" class="form-select filter-select" {{ request('district_id') ? '' : 'disabled' }}>
                                    <option value="">All Areas</option>
                                    @foreach($areas as $area)
                                        <option value="{{ $area->id }}" {{ (string) request('area_id') === (string) $area->id ? 'selected' : '' }}>
                                            {{ $area->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="filter-block">
                                <div class="filter-title">Speciality</div>
                                <div class="speciality-search-box">
                                    <input type="text" id="specialitySearchInput" class="form-control filter-input" placeholder="Search speciality">
                                </div>

                                <div class="speciality-list" id="specialityList">
                                    @foreach($specialities as $speciality)
                                        <label class="filter-check speciality-item {{ $loop->index >= 8 ? 'extra-speciality d-none' : '' }}" data-speciality-name="{{ strtolower($speciality->name) }}">
                                            <input
                                                type="checkbox"
                                                name="select_specialist[]"
                                                value="{{ $speciality->id }}"
                                                {{ in_array((int) $speciality->id, $selectedSpecialities, true) ? 'checked' : '' }}
                                            >
                                            <span>{{ $speciality->name }}</span>
                                        </label>
                                    @endforeach
                                </div>

                                @if($specialities->count() > 8)
                                    <button type="button" id="toggleSpecialityBtn" class="toggle-speciality-btn">Show more</button>
                                @endif
                            </div>

                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-12 col-lg-8 col-xl-9">
                <div class="result-toolbar">
                    <div class="result-count">
                        <h5>{{ $doctors->total() }} Doctors Found</h5>
                        <p>Showing page {{ $doctors->currentPage() }} of {{ $doctors->lastPage() }}</p>
                    </div>
                </div>

                @if(!empty($activeFilters))
                    <div class="active-filters">
                        @foreach($activeFilters as $filter)
                            <span class="filter-chip">{{ $filter['label'] }}: {{ $filter['value'] }}</span>
                        @endforeach
                        <a href="{{ route('doctors.search') }}" class="filter-chip-clear">Clear all</a>
                    </div>
                @endif

                @forelse($doctors as $doctor)
                    @include('components.search-doctor-card', [
                        'image' => $doctor->profile_image ? asset('storage/'.$doctor->profile_image) : asset('assets/img/doctors/doctor-thumb-01.jpg'),
                        'name' => 'Dr. ' . $doctor->user->name,
                        'speciality' => $doctor->speciality->name ?? 'General',
                        'department' => $doctor->speciality->name ?? 'General',
                        'departmentIcon' => ($doctor->speciality && $doctor->speciality->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($doctor->speciality->image)) ? asset('storage/'.$doctor->speciality->image) : asset('assets/img/specialities/specialities-05.png'),
                        'rating' => (int) round($doctor->average_rating),
                        'reviews' => $doctor->review_count,
                        'location' => $doctor->clinic_name ?? ($doctor->clinic_city ?? 'Location'),
                        'qualification' => $doctor->qualification ?? '',
                        'price' => ((float) ($doctor->consultation_fee ?? 0)) > 0 ? (float) $doctor->consultation_fee : 'Free',
                        'feeLabel' => 'Consultation Fee',
                        'thumbsUp' => $doctor->average_rating > 0 ? round(($doctor->average_rating / 5) * 100) . '%' : '0%',
                        'profileLink' => route('doctors.profile', $doctor->id),
                        'bookingLink' => route('booking', $doctor->id)
                    ])

                    @if(($loop->index + 1) % 3 == 0 && $advertisements->isNotEmpty())
                        @php $ad = $advertisements->random(); @endphp
                        <div class="sponsored-ad-card">
                            <div class="card-body p-0 position-relative">
                                <a href="{{ $ad->link ?? '#' }}" target="_blank">
                                    <img src="{{ asset('storage/'.$ad->image) }}" class="img-fluid" alt="{{ $ad->title }}" style="width: 100%; max-height: 220px; object-fit: cover;">
                                </a>
                                <span class="badge bg-light text-muted position-absolute" style="top: 12px; right: 12px;">Sponsored</span>
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="empty-state">
                        <img src="{{ asset('assets/img/no-results.svg') }}" alt="No Results" style="max-width: 200px; opacity: 0.6;" onerror="this.style.display='none'">
                        <h4 class="mt-4" style="color: #334155;">No doctors found</h4>
                        <p style="color: #64748b;">Try adjusting your filters to find the right doctor.</p>
                        <a href="{{ route('doctors.search') }}" class="btn btn-primary mt-2">Reset Filters</a>
                    </div>
                @endforelse

                @if($doctors->count() > 0)
                    <div class="load-more text-center mt-4">
                        {{ $doctors->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/plugins/theia-sticky-sidebar/ResizeSensor.js') }}"></script>
<script src="{{ asset('assets/plugins/theia-sticky-sidebar/theia-sticky-sidebar.js') }}"></script>
<script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap-datetimepicker.min.js') }}"></script>
<script src="{{ asset('assets/plugins/fancybox/jquery.fancybox.min.js') }}"></script>

<script>
    (function () {
        var filterForm = document.getElementById('doctorFilterForm');
        var specialityInput = document.getElementById('specialitySearchInput');
        var specialityItems = document.querySelectorAll('.speciality-item');
        var toggleBtn = document.getElementById('toggleSpecialityBtn');
        var expanded = false;
        var autoSubmitTimer = null;

        function submitFiltersWithDelay(delay) {
            if (!filterForm) {
                return;
            }
            clearTimeout(autoSubmitTimer);
            autoSubmitTimer = setTimeout(function () {
                filterForm.submit();
            }, delay || 0);
        }

        if (specialityInput) {
            specialityInput.addEventListener('input', function () {
                var keyword = this.value.trim().toLowerCase();
                specialityItems.forEach(function (item) {
                    var name = item.getAttribute('data-speciality-name') || '';
                    item.style.display = name.indexOf(keyword) !== -1 ? 'flex' : 'none';
                });
            });
        }

        if (toggleBtn) {
            toggleBtn.addEventListener('click', function () {
                expanded = !expanded;
                document.querySelectorAll('.extra-speciality').forEach(function (item) {
                    item.classList.toggle('d-none', !expanded);
                });
                toggleBtn.textContent = expanded ? 'Show less' : 'Show more';
            });
        }

        var districtSelect = document.getElementById('districtSelect');
        var areaSelect = document.getElementById('areaSelect');
        var areaApiTemplate = @json(route('api.areas', ['district' => '__DISTRICT__']));

        if (districtSelect && areaSelect) {
            districtSelect.addEventListener('change', function () {
                var districtId = this.value;

                areaSelect.innerHTML = '<option value="">All Areas</option>';
                areaSelect.disabled = !districtId;

                if (!districtId) {
                    submitFiltersWithDelay(0);
                    return;
                }

                fetch(areaApiTemplate.replace('__DISTRICT__', districtId))
                    .then(function (response) {
                        return response.json();
                    })
                    .then(function (areas) {
                        areas.forEach(function (area) {
                            var option = document.createElement('option');
                            option.value = area.id;
                            option.textContent = area.name;
                            areaSelect.appendChild(option);
                        });
                        submitFiltersWithDelay(0);
                    })
                    .catch(function () {
                        areaSelect.innerHTML = '<option value="">All Areas</option>';
                        submitFiltersWithDelay(0);
                    });
            });
        }

        if (areaSelect) {
            areaSelect.addEventListener('change', function () {
                submitFiltersWithDelay(0);
            });
        }

        if (filterForm) {
            filterForm.querySelectorAll('input[name="select_specialist[]"]').forEach(function (checkbox) {
                checkbox.addEventListener('change', function () {
                    submitFiltersWithDelay(0);
                });
            });

            ['keywords', 'location'].forEach(function (fieldName) {
                var field = filterForm.querySelector('[name="' + fieldName + '"]');
                if (!field) {
                    return;
                }
                field.addEventListener('input', function () {
                    submitFiltersWithDelay(600);
                });
            });
        }
    })();
</script>
@endpush

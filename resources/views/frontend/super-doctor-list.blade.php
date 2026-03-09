<div class="sort-bar">
    <div class="results-count">
        <strong>{{ $doctors->total() }}</strong> doctors found
    </div>
    <div class="sort-options">
        <label>Sort by:</label>
        <select class="sort-select" onchange="changeSortBy(this.value)">
            <option value="relevance" {{ request('sort_by') == 'relevance' ? 'selected' : '' }}>Relevance</option>
            <option value="fee_low" {{ request('sort_by') == 'fee_low' ? 'selected' : '' }}>Fee: Low to High</option>
            <option value="fee_high" {{ request('sort_by') == 'fee_high' ? 'selected' : '' }}>Fee: High to Low</option>
            <option value="experience" {{ request('sort_by') == 'experience' ? 'selected' : '' }}>Experience</option>
            <option value="rating" {{ request('sort_by') == 'rating' ? 'selected' : '' }}>Rating</option>
            <option value="newest" {{ request('sort_by') == 'newest' ? 'selected' : '' }}>Newest</option>
        </select>
    </div>
</div>

@forelse($doctors as $doctor)
    @php
        $consultationFee = (float) ($doctor->consultation_fee ?? 0);
        $price = $consultationFee > 0 ? $consultationFee : 'Free';
    @endphp

    @include('components.search-doctor-card', [
        'image' => $doctor->profile_image ? (filter_var($doctor->profile_image, FILTER_VALIDATE_URL) ? $doctor->profile_image : asset($doctor->profile_image)) : asset('assets/img/doctors/doctor-thumb-01.jpg'),
        'name' => $doctor->user->name,
        'speciality' => $doctor->speciality->name ?? 'General',
        'department' => $doctor->speciality->name ?? 'General',
        'departmentIcon' => ($doctor->speciality && $doctor->speciality->image && file_exists(public_path($doctor->speciality->image))) ? asset($doctor->speciality->image) : asset('assets/img/specialities/specialities-05.png'),
        'rating' => $doctor->average_rating,
        'reviews' => $doctor->review_count,
        'location' => ($doctor->area ? $doctor->area->name . ', ' : '') . ($doctor->district ? $doctor->district->name : 'Location'),
        'price' => $price,
        'feeLabel' => 'Consultation Fee',
        'thumbsUp' => $doctor->average_rating > 0 ? round(($doctor->average_rating / 5) * 100) . '%' : '0%',
        'experience' => $doctor->experience_years ?? 0,
        'qualification' => $doctor->qualification ?? '',
        'profileLink' => route('doctors.profile', $doctor->id),
        'bookingLink' => route('booking', $doctor->id),
        'isOnline' => optional($doctor->user)->isOnline(),
        'lastSeenText' => optional($doctor->user?->last_seen_at)->diffForHumans()
    ])


               @if(($loop->index + 1) % 3 == 0 && isset($advertisements) && $advertisements->count() > 0)
                @php
                    // Calculate sequential index: (3rd item -> 0, 6th item -> 1, 9th item -> 2)
                    $adIndex = (int) (($loop->iteration / 3) - 1);
                    // Get ad using modulo to rotate if we have fewer ads than slots
                    $ad = $advertisements->values()->get($adIndex % $advertisements->count());
                @endphp
                        @if($ad)
                            <div class="card mb-3 shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid #e4e4e4;">
                                <div class="card-body p-0 position-relative">
                                    <a href="{{ $ad->link ?? '#' }}" target="_blank" class="d-block text-center" style="background: #f8f9fa;">
                                        <img src="{{ asset($ad->image) }}" class="img-fluid" alt="{{ $ad->title }}" style="width: 100%; max-height: 250px; object-fit: cover;">
                                        </a>
                                        <span class="badge badge-light text-muted position-absolute" style="top: 10px; right: 10px; background: rgba(255,255,255,0.8);">Sponsored</span>
                                    </div>
                            </div>
                        @endif
            @endif
@empty    <div class="text-center py-5">
        <img src="{{ asset('assets/img/no-results.svg') }}" alt="No Results" style="max-width: 200px; opacity: 0.5;" onerror="this.style.display='none'">
        <h4 class="mt-4" style="color: #6b7280;">No doctors found</h4>
        <p style="color: #9ca3af;">Try adjusting your search filters</p>
        <button onclick="resetFilters()" class="btn btn-primary mt-3">
            <i class="fas fa-redo"></i> Reset Filters
        </button>
    </div>
@endforelse

@if($doctors->count() > 0)
    <div class="load-more text-center mt-4">
        {{ $doctors->withQueryString()->links() }}
    </div>
@endif

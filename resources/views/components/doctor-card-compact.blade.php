{{-- Compact doctor card (same design as the home page), used for the mobile 2-column grid. --}}
<div class="doctor-card-new">
    <div class="doctor-img-wrapper">
        <a href="{{ $profileLink }}">
            <img src="{{ $image }}" class="doctor-img" alt="{{ $name }}">
        </a>
        <div class="doctor-fee-badge">
            <span>{{ is_numeric($price) ? '৳ ' . number_format($price, 0) : 'Free' }}</span>
        </div>
        @if(isset($id))
            <a href="javascript:void(0)" class="fav-btn {{ !empty($isFavourite) ? 'active' : '' }}" data-id="{{ $id }}">
                <i class="{{ !empty($isFavourite) ? 'fas' : 'far' }} fa-bookmark"></i>
            </a>
        @endif
    </div>
    <div class="doctor-info">
        <span class="doctor-speciality">{{ $speciality }}</span>
        <h4 class="doctor-name">
            <a href="{{ $profileLink }}">{{ $name }}</a>
            <i class="fas fa-check-circle verified-badge" title="Verified"></i>
        </h4>
        @if(!empty($designation))
            <div class="doctor-desig">{{ $designation }}</div>
        @endif
        @if(!empty($chamber))
            <div class="doctor-place"><i class="fas fa-clinic-medical"></i><span>{{ $chamber }}</span></div>
        @endif
        <div class="doctor-place"><i class="fas fa-map-marker-alt"></i><span>{{ $location }}</span></div>
        <div class="doctor-rating doctor-card-rating">
            @if(($reviews ?? 0) > 0)
                <i class="fas fa-star"></i>
                <span>{{ number_format($rawRating ?? 0, 1) }}</span>
                <span class="rating-count">({{ $reviews }} reviews)</span>
            @else
                <i class="fas fa-star empty"></i>
                <span class="rating-count">(0 reviews)</span>
            @endif
        </div>
        <div class="doctor-buttons">
            <a href="{{ $profileLink }}" class="btn-view-details"><i class="fas fa-user"></i> Details</a>
            <a href="{{ $bookingLink }}" class="btn-book-appointment"><i class="fas fa-calendar-check"></i> Appointment</a>
        </div>
    </div>
</div>

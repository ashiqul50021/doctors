<div class="card doctor-search-card"
    style="border: none; border-radius: 16px; box-shadow: 0 8px 24px rgba(149, 157, 165, 0.1); margin-bottom: 24px; transition: all 0.3s ease; overflow: hidden; background: #fff;">
    <div class="card-body p-4">
        <div class="row align-items-center">
            <!-- Left: Doctor Image -->
            <div class="col-md-auto text-center mb-3 mb-md-0 position-relative">
                <a href="{{ $profileLink }}" class="d-inline-block position-relative">
                    <img src="{{ $image }}" class="img-fluid rounded-circle" alt="{{ $name }}"
                        style="width: 110px; height: 110px; object-fit: cover; border: 4px solid #f8f9fa; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
                    <div class="verified-badge position-absolute"
                        style="bottom: 5px; right: 5px; background: #fff; border-radius: 50%; padding: 2px;">
                        <i class="fas fa-check-circle text-primary"
                            style="font-size: 22px; background: #fff; border-radius: 50%;"></i>
                    </div>
                </a>
            </div>

            <!-- Middle: Info & Stats -->
            <div class="col-md pl-md-4">
                <h4 class="doc-name mb-1">
                    <a href="{{ $profileLink }}"
                        style="color: #272b41; font-weight: 700; font-size: 1.25rem; text-decoration: none;">{{ $name }}</a>
                </h4>
                <p class="doc-speciality mb-3" style="font-size: 0.9rem; color: #757575;">{{ $qualification }}</p>

                <div class="d-flex flex-wrap gap-2 mb-3" style="gap: 10px;">
                    <span class="badge badge-pill"
                        style="background: {{ !empty($isOnline) ? '#dcfce7' : '#f3f4f6' }}; color: {{ !empty($isOnline) ? '#166534' : '#4b5563' }}; padding: 8px 12px; font-weight: 600;">
                        <i class="fas fa-circle"
                            style="font-size: 9px; margin-right: 6px; color: {{ !empty($isOnline) ? '#22c55e' : '#9ca3af' }};"></i>
                        {{ !empty($isOnline) ? 'Online' : 'Offline' }}
                        @if(empty($isOnline) && !empty($lastSeenText))
                            <small style="margin-left: 6px;">({{ $lastSeenText }})</small>
                        @endif
                    </span>
                    <span class="badge badge-pill"
                        style="background: #e2f6ff; color: #0de0fe; padding: 8px 12px; font-weight: 500;">
                        <img src="{{ $departmentIcon }}" alt=""
                            style="width: 16px; height: 16px; margin-right: 4px; vertical-align: text-bottom;">
                        {{ $department }}
                    </span>
                    <span class="badge badge-pill"
                        style="background: #f0f2f5; color: #6c757d; padding: 8px 12px; font-weight: 500;">
                        <i class="fas fa-briefcase" style="margin-right: 4px;"></i> {{ $experience ?? '5+' }} Years Exp
                    </span>
                    <span class="badge badge-pill"
                        style="background: #f0f2f5; color: #6c757d; padding: 8px 12px; font-weight: 500;">
                        <i class="fas fa-map-marker-alt" style="margin-right: 4px;"></i> {{ $location }}
                    </span>
                </div>
            </div>

            <!-- Right: Price & Actions -->
            <div class="col-md-auto text-center text-md-right mt-3 mt-md-0 border-left-md pl-md-4"
                style="border-left: 1px solid #f0f0f0;">
                <div class="price-box mb-2">
                    <div style="font-size: 0.8rem; color: #6b7280; font-weight: 600; margin-bottom: 2px;">
                        {{ $feeLabel ?? 'Consultation Fee' }}
                    </div>
                    <span style="font-size: 1.25rem; font-weight: 700; color: #111827;">
                        @if($price === 'Free')
                            Free
                        @else
                            @php $amount = (float) $price; @endphp
                            {{ $amount > 0 ? '৳' . number_format($amount, 0) : 'Free' }}
                        @endif
                    </span>
                </div>

                <div class="rating mb-3">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star"
                            style="color: {{ $i <= $rating ? '#f4c150' : '#dedfe0' }}; font-size: 12px;"></i>
                    @endfor
                    <span class="d-block mt-1"
                        style="font-size: 0.85rem; font-weight: 600; color: #272b41;">{{ $thumbsUp }} Positive</span>
                </div>

                <div class="actions d-flex flex-column gap-2" style="gap: 10px; min-width: 160px;">
                    <a href="{{ $profileLink }}" class="btn btn-outline-primary btn-sm btn-block"
                        style="border-radius: 50px; font-weight: 600; padding: 8px 20px;">View Profile</a>
                    <a href="{{ $bookingLink }}" class="btn btn-primary btn-sm btn-block"
                        style="border-radius: 50px; font-weight: 600; padding: 8px 20px; box-shadow: 0 4px 10px rgba(13, 224, 254, 0.4); border: none;">Book
                        Now</a>
                </div>
            </div>
        </div>
    </div>
</div>

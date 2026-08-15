@props(['colClass' => 'col-lg-4 col-md-6 col-sm-6 col-6 mb-4'])

<div class="{{ $colClass }} product-skeleton-item">
    <div class="product-card-skeleton">
        <!-- Image placeholder -->
        <div class="skeleton-img-wrap">
            <div class="skeleton-badge skeleton-shimmer"></div>
        </div>

        <!-- Details placeholder -->
        <div class="skeleton-details">
            <!-- Rating -->
            <div class="skeleton-rating-row">
                <div class="skeleton-rating-stars skeleton-shimmer"></div>
                <div class="skeleton-rating-text skeleton-shimmer"></div>
            </div>

            <!-- Brand / Category -->
            <div class="skeleton-brand skeleton-shimmer"></div>

            <!-- Title (2 lines) -->
            <div class="skeleton-title-1 skeleton-shimmer"></div>
            <div class="skeleton-title-2 skeleton-shimmer"></div>

            <!-- Footer: Price & Actions -->
            <div class="skeleton-footer">
                <div class="skeleton-price-block">
                    <div class="skeleton-price-main skeleton-shimmer"></div>
                    <div class="skeleton-price-sub skeleton-shimmer"></div>
                </div>

                <div class="skeleton-btn-group">
                    <div class="skeleton-btn-cart skeleton-shimmer"></div>
                    <div class="skeleton-btn-buy skeleton-shimmer"></div>
                </div>
            </div>
        </div>
    </div>
</div>

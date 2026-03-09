<!-- Doctor Widget -->
<div class="profile-widget">
    <div class="doc-img">
        <a href="{{ $profileLink }}">
            <img class="img-fluid" alt="User Image" src="{{ $image }}">
        </a>
        <a href="javascript:void(0)" class="fav-btn">
            <i class="far fa-bookmark"></i>
        </a>
    </div>
    <div class="pro-content">
        <h3 class="title">
            <a href="{{ $profileLink }}">{{ $name }}</a>
            <i class="fas fa-check-circle verified"></i>
        </h3>
        <p class="speciality">{{ $speciality }}</p>
        <div class="rating">
            @for($i = 1; $i <= 5; $i++)
                <i class="fas fa-star {{ $i <= $rating ? 'filled' : '' }}"></i>
            @endfor
            <span class="d-inline-block average-rating">({{ $reviews }})</span>
        </div>
        <ul class="available-info">
            <li>
                <i class="fas fa-map-marker-alt"></i> {{ $location }}
            </li>
            <li>
                <i class="far fa-clock"></i> {{ $availability }}
            </li>
            <li>
                <i class="far fa-money-bill-alt"></i> {{ $price }}
                <i class="fas fa-info-circle" data-bs-toggle="tooltip" title="Lorem Ipsum"></i>
            </li>
        </ul>
        <div class="row row-sm">
            <div class="col-6">
                <a href="{{ $profileLink }}" class="btn view-btn">View Profile</a>
            </div>
            <div class="col-6">
                <a href="{{ $bookingLink }}" class="btn book-btn">Book Now</a>
            </div>
        </div>
    </div>
</div>
<!-- /Doctor Widget -->

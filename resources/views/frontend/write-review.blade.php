@extends('layouts.app')

@section('title', 'Write a Review - ' . ($siteSettings['site_name'] ?? 'abcsheba'))

@section('content')
    <!-- Breadcrumb -->
    <div class="breadcrumb-bar" style="background: #f8f9fa; padding: 20px 0; border-bottom: 1px solid #e9ecef;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-12 col-12">
                    <nav aria-label="breadcrumb" class="page-breadcrumb">
                        <ol class="breadcrumb mb-1">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('patient.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Write Review</li>
                        </ol>
                    </nav>
                    <h3 class="mb-0">Write a Review</h3>
                </div>
            </div>
        </div>
    </div>
    <!-- /Breadcrumb -->

    <!-- Page Content -->
    <div class="content mt-4">
        <div class="container">
            <div class="row">
                <!-- Profile Sidebar -->
                <div class="col-md-5 col-lg-4 col-xl-3 theiaStickySidebar">
                    @include('frontend.includes.patient-sidebar')
                </div>
                <!-- / Profile Sidebar -->

                <div class="col-md-7 col-lg-8 col-xl-9">
                    <div class="doc-review">
                        <!-- Doctor Widget -->
                        <div class="card mb-4 shadow-sm border-0">
                            <div class="card-body">
                                <div class="doctor-widget d-flex align-items-center gap-3">
                                    <a href="{{ route('doctors.profile', $appointment->doctor->id) }}" class="booking-doc-img">
                                        <img src="{{ $appointment->doctor->profile_image ? asset($appointment->doctor->profile_image) : asset('assets/img/doctors/doctor-thumb-01.jpg') }}" 
                                            class="img-fluid rounded-circle" alt="User Image" style="width: 70px; height: 70px; object-fit: cover;">
                                    </a>
                                    <div class="doc-info-cont ms-2">
                                        <h4 class="doc-name mb-1">
                                            <a href="{{ route('doctors.profile', $appointment->doctor->id) }}" style="text-decoration: none; color: #333; font-weight: 600;">
                                                {{ str_starts_with(strtolower($appointment->doctor->user->name), 'dr.') ? $appointment->doctor->user->name : 'Dr. ' . $appointment->doctor->user->name }}
                                            </a>
                                        </h4>
                                        <p class="doc-speciality text-muted mb-0">{{ $appointment->doctor->speciality->name ?? 'General Specialist' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /Doctor Widget -->

                        <!-- Write Review Form -->
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <h4 class="card-title mb-4" style="color: #333; font-weight: 600;">Write a Review</h4>
                                
                                <form action="{{ route('patient.appointment.review.store', $appointment->id) }}" method="POST">
                                    @csrf

                                    <div class="mb-4">
                                        <label class="form-label d-block text-secondary" style="font-weight: 500;">Your Rating</label>
                                        <div class="star-rating d-flex gap-2" style="font-size: 28px; cursor: pointer; color: #ccc;">
                                            <i class="far fa-star rating-star" data-value="1"></i>
                                            <i class="far fa-star rating-star" data-value="2"></i>
                                            <i class="far fa-star rating-star" data-value="3"></i>
                                            <i class="far fa-star rating-star" data-value="4"></i>
                                            <i class="far fa-star rating-star" data-value="5"></i>
                                        </div>
                                        <input type="hidden" name="rating" id="rating-input" value="5">
                                        @error('rating')
                                            <span class="text-danger small d-block mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label text-secondary" style="font-weight: 500;">Your Review</label>
                                        <textarea class="form-control" name="comment" rows="6" placeholder="Share your experience with this doctor..." style="border-radius: 8px;" max="1000">{{ old('comment') }}</textarea>
                                        @error('comment')
                                            <span class="text-danger small d-block mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="mt-4">
                                        <button type="submit" class="btn btn-primary px-4 py-2" style="border-radius: 6px; font-weight: 500;">Submit Review</button>
                                        <a href="{{ route('patient.dashboard') }}" class="btn btn-light px-4 py-2 ms-2" style="border-radius: 6px; font-weight: 500;">Cancel</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- /Write Review Form -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Star Rating Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const stars = document.querySelectorAll('.rating-star');
            const ratingInput = document.getElementById('rating-input');

            // Set default rating to 5 stars filled
            updateStars(5);

            stars.forEach(star => {
                star.addEventListener('click', function () {
                    const rating = this.getAttribute('data-value');
                    ratingInput.value = rating;
                    updateStars(rating);
                });

                star.addEventListener('mouseover', function () {
                    const rating = this.getAttribute('data-value');
                    updateStars(rating);
                });
            });

            document.querySelector('.star-rating').addEventListener('mouseleave', function () {
                updateStars(ratingInput.value);
            });

            function updateStars(rating) {
                stars.forEach(star => {
                    const value = star.getAttribute('data-value');
                    if (value <= rating) {
                        star.classList.remove('far');
                        star.classList.add('fas', 'text-warning');
                    } else {
                        star.classList.remove('fas', 'text-warning');
                        star.classList.add('far');
                    }
                });
            }
        });
    </script>
@endsection

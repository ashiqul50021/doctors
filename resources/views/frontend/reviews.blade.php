@extends('layouts.app')

@section('title', 'Reviews - ' . ($siteSettings['site_name'] ?? 'Doccure'))

@section('content')
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-md-5 col-lg-4 col-xl-3 theiaStickySidebar">
                @include('frontend.includes.doctor-sidebar')
            </div>
            <div class="col-md-7 col-lg-8 col-xl-9">
                <div class="doc-review review-listing">
                    <ul class="comments-list">
                        @forelse($reviews as $review)
                        <li>
                            <div class="comment">
                                <img class="avatar rounded-circle" alt="User Image"
                                    src="{{ optional($review->patient)->profile_image ? asset('storage/' . $review->patient->profile_image) : asset('assets/img/patients/patient.jpg') }}">
                                <div class="comment-body">
                                    <div class="meta-data">
                                        <span class="comment-author">{{ optional(optional($review->patient)->user)->name ?? 'Anonymous' }}</span>
                                        <span class="comment-date">Reviewed {{ $review->created_at->diffForHumans() }}</span>
                                        <div class="review-count rating">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $review->rating ? 'filled' : '' }}"></i>
                                            @endfor
                                        </div>
                                    </div>
                                    @if($review->comment)
                                    <p class="comment-content">{{ $review->comment }}</p>
                                    @endif
                                    @if($review->appointment)
                                    <small class="text-muted">
                                        Appointment: {{ \Carbon\Carbon::parse($review->appointment->appointment_date)->format('d M Y') }}
                                    </small>
                                    @endif
                                </div>
                            </div>
                        </li>
                        @empty
                        <li>
                            <div class="text-center py-5">
                                <i class="fas fa-star fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No reviews yet.</h5>
                            </div>
                        </li>
                        @endforelse
                    </ul>
                </div>

                <div class="mt-3">
                    {{ $reviews->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

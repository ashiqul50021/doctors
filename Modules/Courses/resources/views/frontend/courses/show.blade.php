@extends('layouts.app')

@section('title', $course->title . ' - Doccure')

@section('content')

    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/css/courses.css') }}">
    @endpush

    <!-- Breadcrumb -->
    <div class="breadcrumb-bar">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-12 col-12">
                    <nav aria-label="breadcrumb" class="page-breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('courses.index') }}">Courses</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($course->title, 50) }}</li>
                        </ol>
                    </nav>
                    <h2 class="breadcrumb-title">{{ Str::limit($course->title, 60) }}</h2>
                </div>
            </div>
        </div>
    </div>
    <!-- /Breadcrumb -->

    <!-- Page Content -->
    <div class="content">
        <div class="container">
            <div class="row">

                <!-- Main Content -->
                <div class="col-lg-8 col-md-12">

                    <div class="course-details-header">
                        <h1>{{ $course->title }}</h1>
                        <div class="course-meta">
                            <span class="badge badge-primary px-3 py-2 mr-3"
                                style="background: #e0f2fe; color: #1E40AF; border: none;">{{ $course->category->name ?? 'General' }}</span>
                            <span><i class="fas fa-clock text-primary"></i> Last updated
                                {{ $course->updated_at->format('M Y') }}</span>
                            <span><i class="fas fa-globe text-primary"></i> English</span>
                            <div class="rating d-inline-flex align-items-center ml-3">
                                <i class="fas fa-star filled"></i>
                                <i class="fas fa-star filled"></i>
                                <i class="fas fa-star filled"></i>
                                <i class="fas fa-star filled"></i>
                                <i class="fas fa-star"></i>
                                <span>(4.5)</span>
                            </div>
                        </div>
                    </div>

                    <!-- Course Description -->
                    <div class="course-description-box">
                        <h3>Description</h3>
                        <div class="mb-4 text-muted" style="line-height: 1.8;">
                            {!! $course->description !!}
                        </div>

                        <h3 class="mt-5">What you'll learn</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-unstyled">
                                    <li class="mb-2"><i class="fas fa-check text-success mr-2"></i> Comprehensive
                                        understanding of {{ $course->title }}</li>
                                    <li class="mb-2"><i class="fas fa-check text-success mr-2"></i> Practical management
                                        techniques</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-unstyled">
                                    <li class="mb-2"><i class="fas fa-check text-success mr-2"></i> Lifestyle modifications
                                    </li>
                                    <li class="mb-2"><i class="fas fa-check text-success mr-2"></i> Expert advice and tips
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Curriculum -->
                    <div class="course-description-box">
                        <h3>Course Content</h3>
                        <div class="accordion" id="courseCurriculum">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        Section 1: Introduction ({{ $course->lessons->count() }} Lessons)
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                                    data-bs-parent="#courseCurriculum">
                                    <div class="accordion-body">
                                        <ul class="lesson-list">
                                            @forelse($course->lessons as $lesson)
                                                <li class="{{ !$lesson->is_free ? 'locked' : '' }}">
                                                    <div class="d-flex align-items-center">
                                                        @if($lesson->is_free)
                                                            <i class="fas fa-play-circle text-primary"></i>
                                                        @else
                                                            <i class="fas fa-lock"></i>
                                                        @endif
                                                        <span class="ml-2">{{ $lesson->title }}</span>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        @if($lesson->is_free)
                                                            <a href="{{ route('courses.lesson', [$course->id, $lesson->id]) }}"
                                                                class="btn btn-sm btn-outline-primary py-0 px-2"
                                                                style="font-size: 12px;">Preview</a>
                                                        @endif
                                                        <span class="text-muted ml-3" style="font-size: 12px;">10:00</span>
                                                    </div>
                                                </li>
                                            @empty
                                                <li class="p-3 text-center text-muted">No lessons available yet.</li>
                                            @endforelse
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Instructor -->
                    <div class="course-description-box">
                        <h3>Instructor</h3>
                        <div class="d-flex align-items-start">
                            <img src="{{ $course->instructor && $course->instructor->profile ? asset('storage/' . $course->instructor->profile) : asset('assets/img/doctors/doctor-thumb-01.jpg') }}"
                                class="rounded-circle mr-4" width="80" height="80" alt="Instructor">
                            <div>
                                <h5 class="mb-1"><a href="#">Dr. {{ $course->instructor->name ?? 'Sarah Wilson' }}</a></h5>
                                <p class="text-muted mb-2">Senior Specialist</p>
                                <p class="text-muted mb-0">Dr. {{ $course->instructor->name ?? 'Wilson' }} is a renowned
                                    specialist with over 15 years of experience in medical education and patient care.</p>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Sidebar -->
                <div class="col-lg-4 col-md-12">
                    <div class="course-sidebar">

                        <!-- Video Widget -->
                        <div class="video-card">
                            @if(Str::startsWith($course->image, 'assets'))
                                <img src="{{ asset($course->image) }}" alt="{{ $course->title }}">
                            @else
                                <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->title }}">
                            @endif
                            <div class="video-play-btn">
                                <i class="fas fa-play"></i>
                            </div>
                        </div>

                        <!-- Price Widget -->
                        <div class="price-card">
                            @if($course->price == 0)
                                <div class="price text-success">Free</div>
                            @else
                                <div class="price">
                                    ৳{{ number_format($course->price) }}
                                    <span class="old-price">৳{{ number_format($course->price * 1.5) }}</span>
                                </div>
                            @endif

                            @auth
                                <form action="{{ route('courses.enroll', $course->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary w-100 btn-lg mb-3 shadow-sm"
                                        style="background: #1E40AF; border-color: #1E40AF;">Enroll Now</button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-primary w-100 btn-lg mb-3 shadow-sm"
                                    style="background: #1E40AF; border-color: #1E40AF;">Login to Enroll</a>
                            @endauth

                            <p class="text-center text-muted small mb-0">30-Day Money-Back Guarantee</p>

                            <ul class="course-includes">
                                <li><i class="fas fa-sliders-h mr-2"></i> {{ $course->level ?? 'Beginner' }} Level</li>
                                <li><i class="fas fa-clock mr-2"></i> {{ $course->duration_hours }} hours on-demand video
                                </li>
                                <li><i class="fas fa-file-alt mr-2"></i> {{ $course->lessons->count() }} articles</li>
                                <li><i class="fas fa-download mr-2"></i> Downloadable resources</li>
                                <li><i class="fas fa-mobile-alt mr-2"></i> Access on mobile and TV</li>
                                <li><i class="fas fa-trophy mr-2"></i> Certificate of completion</li>
                            </ul>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
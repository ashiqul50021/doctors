@extends('layouts.app')

@section('title', ($lesson->title ?? 'Lesson') . ' - Doccure')

@section('content')
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-md-12">
                    <h3 class="mb-3">{{ $lesson->title }}</h3>
                    <p class="text-muted mb-4">Course: {{ $course->title }}</p>

                    @if(!empty($lesson->video_url))
                        <div class="ratio ratio-16x9 mb-4">
                            <iframe src="{{ $lesson->video_url }}" allowfullscreen></iframe>
                        </div>
                    @endif

                    <div class="card">
                        <div class="card-body">
                            {!! $lesson->description ?: '<p class=\"mb-0 text-muted\">No lesson description available.</p>' !!}
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-3">Other Lessons</h5>
                            <ul class="list-unstyled mb-0">
                                @foreach($course->lessons as $item)
                                    <li class="mb-2">
                                        <a href="{{ route('courses.lesson', [$course->id, $item->id]) }}">
                                            {{ $item->title }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

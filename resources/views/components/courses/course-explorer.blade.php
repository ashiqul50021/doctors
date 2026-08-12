<div class="container mx-auto px-4 py-8 space-y-6">
    <h2 class="text-2xl font-bold text-gray-800">🎓 Explore Courses</h2>

    <!-- Filter Bar -->
    <div class="p-4 bg-white rounded-lg border shadow-sm flex flex-col md:flex-row gap-4">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search courses..." class="w-full md:w-1/2 border-gray-300 rounded-md text-sm shadow-sm">
        <select wire:model.live="level" class="w-full md:w-1/4 border-gray-300 rounded-md text-sm shadow-sm">
            <option value="">All Levels</option>
            <option value="beginner">Beginner</option>
            <option value="intermediate">Intermediate</option>
            <option value="advanced">Advanced</option>
        </select>
    </div>

    <!-- Course Grid -->
    <div wire:loading.flex class="items-center justify-center p-6 bg-blue-50 text-blue-700 rounded-lg">
        <span>Filtering courses...</span>
    </div>

    @if($courses->isEmpty())
        <div class="text-center py-12 bg-white rounded border">
            <p class="text-gray-500">No courses found matching your query.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($courses as $course)
                <div class="bg-white rounded-lg border shadow-sm overflow-hidden flex flex-col justify-between">
                    <div class="p-4">
                        <h4 class="font-bold text-lg text-gray-800 line-clamp-1">{{ $course->title ?? 'Medical Fundamentals Course' }}</h4>
                        <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $course->description ?? 'Learn the essentials of clinical practice and health guidelines.' }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 border-t flex justify-between items-center">
                        <span class="font-bold text-blue-600">৳{{ number_format($course->price ?? 1500, 2) }}</span>
                        <a href="/courses/{{ $course->id ?? 1 }}" wire:navigate class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded">
                            Enroll Now
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $courses->links() }}
        </div>
    @endif
</div>

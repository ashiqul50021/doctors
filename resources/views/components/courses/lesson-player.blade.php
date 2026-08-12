<div class="flex flex-col lg:flex-row h-screen bg-gray-900 text-white overflow-hidden">
    
    <!-- Video Player Container (Livewire Dynamic Video Switch) -->
    <div class="w-full lg:w-3/4 flex flex-col justify-between p-6">
        <div>
            <h3 class="text-xl font-bold text-gray-100 mb-4">{{ $lessons[$active_lesson_index]['title'] }}</h3>
            <div class="aspect-w-16 aspect-h-9 bg-black rounded-lg overflow-hidden border border-gray-800 shadow-2xl">
                <iframe class="w-full h-[450px]" src="{{ $lessons[$active_lesson_index]['video_url'] }}" frameborder="0" allowfullscreen></iframe>
            </div>
        </div>

        <div class="flex justify-between items-center mt-4">
            <button wire:click="selectLesson({{ max(0, $active_lesson_index - 1) }})" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 rounded text-sm font-semibold">
                ⬅️ Previous Lesson
            </button>
            <button wire:click="selectLesson({{ min(count($lessons) - 1, $active_lesson_index + 1) }})" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded text-sm font-semibold">
                Next Lesson ➡️
            </button>
        </div>
    </div>

    <!-- Live Playlist Sidebar -->
    <div class="w-full lg:w-1/4 bg-gray-800 border-l border-gray-700 p-4 overflow-y-auto space-y-3">
        <h4 class="font-bold text-gray-200 border-b border-gray-700 pb-2">📋 Course Syllabus</h4>
        
        <div class="space-y-2">
            @foreach($lessons as $idx => $lesson)
                <button type="button" wire:click="selectLesson({{ $idx }})" class="w-full p-3 rounded text-left transition flex justify-between items-center {{ $active_lesson_index === $idx ? 'bg-blue-600 text-white font-bold shadow' : 'bg-gray-700 hover:bg-gray-650 text-gray-300' }}">
                    <span class="text-sm line-clamp-1">{{ $lesson['title'] }}</span>
                    <span class="text-xs opacity-75 ml-2">{{ $lesson['duration'] }}</span>
                </button>
            @endforeach
        </div>
    </div>

</div>

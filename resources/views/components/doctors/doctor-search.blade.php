<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row gap-8">
        
        <!-- Live Doctor Filter Sidebar -->
        <div class="w-full md:w-1/4 bg-white p-6 rounded-lg shadow-sm border space-y-6">
            <h3 class="text-lg font-bold text-gray-800 border-b pb-2">🩺 Filter Doctors</h3>

            <!-- Search -->
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Doctor Name / Designation</label>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search doctor..." class="w-full border-gray-300 rounded-md text-sm shadow-sm">
            </div>

            <!-- Gender Filter -->
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Gender</label>
                <select wire:model.live="gender" class="w-full border-gray-300 rounded-md text-sm shadow-sm">
                    <option value="">Any Gender</option>
                    <option value="male">Male Doctor</option>
                    <option value="female">Female Doctor</option>
                </select>
            </div>
        </div>

        <!-- Doctor Results List -->
        <div class="w-full md:w-3/4 space-y-4">
            <div wire:loading.flex class="items-center justify-center p-6 bg-blue-50 text-blue-700 rounded-lg">
                <span>Searching doctors in real-time...</span>
            </div>

            @if($doctors->isEmpty())
                <div class="text-center py-12 bg-white rounded border">
                    <p class="text-gray-500">No doctors match your criteria.</p>
                </div>
            @else
                @foreach($doctors as $doc)
                    <div class="p-6 bg-white rounded-lg border shadow-sm flex flex-col md:flex-row justify-between items-center gap-4">
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center font-bold text-blue-600 text-xl">
                                👨‍⚕️
                            </div>
                            <div>
                                <h4 class="font-bold text-lg text-gray-800">{{ $doc->name ?? 'Dr. Specialist' }}</h4>
                                <p class="text-sm text-blue-600 font-semibold">{{ $doc->designation ?? 'Consultant Specialist' }}</p>
                                <p class="text-xs text-gray-500 mt-1">📍 Chamber: {{ $doc->address ?? 'Main Branch' }}</p>
                            </div>
                        </div>

                        <div>
                            <a href="/doctors/{{ $doc->id ?? 1 }}" wire:navigate class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-md shadow">
                                Book Appointment ➔
                            </a>
                        </div>
                    </div>
                @endforeach

                <div class="mt-4">
                    {{ $doctors->links() }}
                </div>
            @endif
        </div>

    </div>
</div>

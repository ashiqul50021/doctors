<div class="p-6 bg-white rounded-lg shadow-sm border space-y-6">
    <h3 class="text-xl font-bold text-gray-800">📅 Select Appointment Slot</h3>

    @if (session()->has('message'))
        <div class="p-4 bg-green-100 text-green-700 rounded-md">
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 bg-red-100 text-red-700 rounded-md">
            {{ session('error') }}
        </div>
    @endif

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Appointment Date</label>
        <input type="date" wire:model.live="selected_date" min="{{ date('Y-m-d') }}" class="border-gray-300 rounded-md shadow-sm">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Available Time Slots</label>
        <div class="grid grid-cols-3 sm:grid-cols-4 gap-3">
            @foreach($slots as $slot)
                <button type="button" wire:click="selectSlot('{{ $slot }}')" class="py-2 px-3 text-xs font-bold rounded-md border text-center transition {{ $selected_slot === $slot ? 'bg-blue-600 text-white border-blue-600 shadow' : 'bg-gray-50 text-gray-700 hover:bg-gray-100' }}">
                    ⏰ {{ $slot }}
                </button>
            @endforeach
        </div>
    </div>

    @if($selected_slot)
        <div class="p-4 bg-blue-50 border border-blue-200 rounded-md flex justify-between items-center">
            <div>
                <p class="text-xs text-blue-700">Selected Slot:</p>
                <p class="font-bold text-blue-900">{{ $selected_date }} at {{ $selected_slot }}</p>
            </div>
            <button wire:click="confirmBooking" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-bold rounded-md shadow">
                Confirm Booking
            </button>
        </div>
    @endif
</div>

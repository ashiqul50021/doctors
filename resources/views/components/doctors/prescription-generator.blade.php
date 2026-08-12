<div class="p-6 bg-white rounded-lg shadow-md border space-y-6">
    <h2 class="text-2xl font-bold text-gray-800">📝 Doctor Live Prescription Maker</h2>

    @if (session()->has('message'))
        <div class="p-4 bg-green-100 text-green-700 rounded-md">
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Patient Name</label>
            <input type="text" wire:model="patient_name" placeholder="Patient name" class="w-full mt-1 border-gray-300 rounded-md shadow-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Patient Age / Gender</label>
            <input type="text" wire:model="patient_age" placeholder="e.g. 32 Yrs / Male" class="w-full mt-1 border-gray-300 rounded-md shadow-sm">
        </div>
    </div>

    <!-- Medicines Repeater -->
    <div class="p-4 bg-gray-50 rounded-lg space-y-4">
        <div class="flex justify-between items-center">
            <h4 class="font-semibold text-gray-800">💊 Prescribed Medicines</h4>
            <button type="button" wire:click="addMedicine" class="px-3 py-1 bg-blue-600 text-white text-xs font-semibold rounded">+ Add Medicine</button>
        </div>

        @foreach($medicines as $index => $med)
            <div class="p-3 bg-white border rounded shadow-sm relative grid grid-cols-1 md:grid-cols-4 gap-2">
                <input type="text" wire:model="medicines.{{ $index }}.name" placeholder="Medicine Name (e.g. Napa Extra)" class="border-gray-200 text-sm rounded">
                <input type="text" wire:model="medicines.{{ $index }}.dosage" placeholder="Dosage (1+0+1)" class="border-gray-200 text-sm rounded">
                <input type="text" wire:model="medicines.{{ $index }}.duration" placeholder="Duration (7 Days)" class="border-gray-200 text-sm rounded">
                <div class="flex items-center space-x-2">
                    <input type="text" wire:model="medicines.{{ $index }}.instructions" placeholder="After meal" class="w-full border-gray-200 text-sm rounded">
                    <button type="button" wire:click="removeMedicine({{ $index }})" class="text-red-500 font-bold px-2">✕</button>
                </div>
            </div>
        @endforeach
    </div>

    <button wire:click="savePrescription" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-bold rounded-md shadow">
        Generate & Save Prescription
    </button>
</div>

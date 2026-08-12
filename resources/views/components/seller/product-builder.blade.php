<div class="p-6 bg-white rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">📦 {{ $product_id ? 'Edit Product' : 'Create New Product' }}</h2>

    @if (session()->has('message'))
        <div class="p-4 mb-4 text-green-700 bg-green-100 rounded-lg">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit.prevent="saveProduct" class="space-y-6">
        
        <!-- General vs Medical Toggle -->
        <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg flex items-center justify-between">
            <div>
                <h4 class="font-semibold text-blue-900">Product Classification</h4>
                <p class="text-sm text-blue-700">Is this a Medical / Healthcare item requiring prescription or generic details?</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" wire:model.live="is_medical" class="sr-only peer">
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                <span class="ml-3 text-sm font-medium text-gray-900">{{ $is_medical ? 'Medical Product' : 'General Product' }}</span>
            </label>
        </div>

        <!-- Title & Subtitle -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Product Title *</label>
                <input type="text" wire:model="title" class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Subtitle</label>
                <input type="text" wire:model="subtitle" class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>

        <!-- Dynamic Medical Section -->
        @if($is_medical)
        <div class="p-4 bg-red-50 border border-red-200 rounded-lg space-y-4">
            <h4 class="font-semibold text-red-900">🩺 Medical Specifics</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Generic Name</label>
                    <input type="text" wire:model="generic_name" placeholder="e.g. Paracetamol" class="w-full mt-1 border-gray-300 rounded-md shadow-sm">
                </div>
                <div class="flex items-center mt-6">
                    <input type="checkbox" wire:model="prescription_required" id="rx" class="h-4 w-4 text-blue-600 rounded">
                    <label for="rx" class="ml-2 text-sm font-medium text-gray-700">Prescription Required Flag (`Yes` / `No`)</label>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Side Effects & Warnings</label>
                <textarea wire:model="side_effects_warnings" rows="2" class="w-full mt-1 border-gray-300 rounded-md shadow-sm"></textarea>
            </div>
        </div>
        @endif

        <!-- Pricing & Stock -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Regular Price (৳) *</label>
                <input type="number" step="0.01" wire:model="regular_price" class="w-full mt-1 border-gray-300 rounded-md shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Sale Price (৳)</label>
                <input type="number" step="0.01" wire:model="sale_price" class="w-full mt-1 border-gray-300 rounded-md shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Stock Quantity</label>
                <input type="number" wire:model="single_stock" class="w-full mt-1 border-gray-300 rounded-md shadow-sm">
            </div>
        </div>

        <!-- Custom Details Section Builder (Landing Page Repeater) -->
        <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg space-y-4">
            <div class="flex justify-between items-center">
                <h4 class="font-semibold text-gray-800">🎨 Custom Details Page Builder (Landing Page Sections)</h4>
                <div class="space-x-2">
                    <button type="button" wire:click="addCustomSection('faq')" class="px-3 py-1 bg-gray-200 hover:bg-gray-300 text-xs font-semibold rounded">+ Add FAQ</button>
                    <button type="button" wire:click="addCustomSection('video')" class="px-3 py-1 bg-gray-200 hover:bg-gray-300 text-xs font-semibold rounded">+ Add Video</button>
                    <button type="button" wire:click="addCustomSection('steps')" class="px-3 py-1 bg-gray-200 hover:bg-gray-300 text-xs font-semibold rounded">+ Add Usage Steps</button>
                </div>
            </div>

            @foreach($custom_sections as $index => $section)
                <div class="p-3 bg-white border rounded shadow-sm relative">
                    <button type="button" wire:click="removeCustomSection({{ $index }})" class="absolute top-2 right-2 text-red-500 font-bold">✕</button>
                    <span class="inline-block px-2 py-0.5 bg-blue-100 text-blue-800 text-xs font-bold rounded mb-2 uppercase">{{ $section['type'] }} Section</span>
                    
                    @if($section['type'] === 'faq')
                        <input type="text" wire:model="custom_sections.{{ $index }}.question" placeholder="Question" class="w-full mb-2 border-gray-200 text-sm rounded">
                        <textarea wire:model="custom_sections.{{ $index }}.answer" placeholder="Answer" rows="2" class="w-full border-gray-200 text-sm rounded"></textarea>
                    @elseif($section['type'] === 'video')
                        <input type="text" wire:model="custom_sections.{{ $index }}.title" placeholder="Video Title" class="w-full mb-2 border-gray-200 text-sm rounded">
                        <input type="text" wire:model="custom_sections.{{ $index }}.video_url" placeholder="YouTube / Video URL" class="w-full border-gray-200 text-sm rounded">
                    @elseif($section['type'] === 'steps')
                        <input type="text" wire:model="custom_sections.{{ $index }}.title" placeholder="Step Title / Dosage" class="w-full mb-2 border-gray-200 text-sm rounded">
                        <textarea wire:model="custom_sections.{{ $index }}.description" placeholder="Instructions" rows="2" class="w-full border-gray-200 text-sm rounded"></textarea>
                    @endif
                </div>
            @endforeach
        </div>

        <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md shadow">
            Submit for Admin Approval
        </button>
    </form>
</div>

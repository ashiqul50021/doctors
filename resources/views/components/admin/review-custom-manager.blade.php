<div class="p-6 bg-white rounded-lg shadow-md space-y-8">
    <h2 class="text-2xl font-bold text-gray-800">⭐ Admin Review Moderation & Custom Review Builder</h2>

    @if (session()->has('message'))
        <div class="p-4 bg-green-100 text-green-700 rounded-md">
            {{ session('message') }}
        </div>
    @endif

    <!-- Create Custom Review Form -->
    <div class="p-4 bg-purple-50 border border-purple-200 rounded-lg space-y-4">
        <h3 class="text-lg font-semibold text-purple-900">➕ Add Custom Review (Admin Social Proof Entry)</h3>
        <form wire:submit.prevent="createCustomReview" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Select Product *</label>
                    <select wire:model="product_id" class="w-full mt-1 border-gray-300 rounded-md shadow-sm">
                        <option value="">-- Choose Product --</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}">{{ $p->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Reviewer Name *</label>
                    <input type="text" wire:model="reviewer_name" placeholder="e.g. Dr. Rahat Khan" class="w-full mt-1 border-gray-300 rounded-md shadow-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Rating (1-5 Star) *</label>
                    <select wire:model="rating" class="w-full mt-1 border-gray-300 rounded-md shadow-sm">
                        <option value="5">⭐⭐⭐⭐⭐ (5 Star)</option>
                        <option value="4">⭐⭐⭐⭐ (4 Star)</option>
                        <option value="3">⭐⭐⭐ (3 Star)</option>
                        <option value="2">⭐⭐ (2 Star)</option>
                        <option value="1">⭐ (1 Star)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Reviewer Avatar URL</label>
                    <input type="text" wire:model="reviewer_avatar" placeholder="https://..." class="w-full mt-1 border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Publish Date (Optional)</label>
                    <input type="date" wire:model="custom_date" class="w-full mt-1 border-gray-300 rounded-md shadow-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Review Comment *</label>
                <textarea wire:model="comment" rows="2" placeholder="Write custom review text..." class="w-full mt-1 border-gray-300 rounded-md shadow-sm"></textarea>
            </div>

            <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-md shadow">
                Publish Custom Review
            </button>
        </form>
    </div>

    <!-- Pending Reviews Queue -->
    <div class="space-y-4">
        <h3 class="text-lg font-semibold text-gray-800">⏳ Pending Reviews Queue (Requires Approval)</h3>
        @if($pending_reviews->isEmpty())
            <p class="text-sm text-gray-500 italic">No pending reviews awaiting approval.</p>
        @else
            <div class="space-y-3">
                @foreach($pending_reviews as $rev)
                    <div class="p-4 bg-gray-50 border rounded-md flex justify-between items-center">
                        <div>
                            <span class="font-bold text-gray-800">{{ $rev->reviewer_name }}</span>
                            <span class="text-yellow-500 text-sm ml-2">({{ $rev->rating }} ⭐)</span>
                            <p class="text-xs text-gray-500">Product: {{ $rev->product->title ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-700 mt-1">"{{ $rev->comment }}"</p>
                        </div>
                        <div class="space-x-2">
                            <button wire:click="approveReview({{ $rev->id }})" class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded">Approve</button>
                            <button wire:click="rejectReview({{ $rev->id }})" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded">Reject</button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

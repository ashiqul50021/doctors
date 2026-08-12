<div>
    <!-- Cart Trigger Floating Button (Optional) -->
    <button wire:click="openDrawer" class="fixed bottom-6 right-6 z-50 bg-blue-600 hover:bg-blue-700 text-white p-4 rounded-full shadow-2xl flex items-center space-x-2">
        <span>🛒 Cart</span>
        <span class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ count($cartItems) }}</span>
    </button>

    <!-- Drawer Backdrop -->
    @if($isOpen)
        <div wire:click="closeDrawer" class="fixed inset-0 bg-black bg-opacity-50 z-50 transition-opacity"></div>
    @endif

    <!-- Slide-over Drawer Panel -->
    <div class="fixed inset-y-0 right-0 max-w-full flex z-50 transform transition-transform duration-300 {{ $isOpen ? 'translate-x-0' : 'translate-x-full' }}">
        <div class="w-screen max-w-md bg-white shadow-xl flex flex-col justify-between">
            
            <!-- Drawer Header -->
            <div class="p-4 bg-gray-50 border-b flex justify-between items-center">
                <h3 class="font-bold text-lg text-gray-800">🛍️ Your Shopping Cart</h3>
                <button wire:click="closeDrawer" class="text-gray-500 hover:text-red-500 text-xl font-bold">✕</button>
            </div>

            <!-- Drawer Content / Cart Items -->
            <div class="p-4 flex-1 overflow-y-auto space-y-4">
                @if(empty($cartItems))
                    <div class="text-center py-12 text-gray-400">
                        <p>Your cart is empty.</p>
                    </div>
                @else
                    @foreach($cartItems as $id => $item)
                        <div class="p-3 bg-gray-50 rounded border flex justify-between items-center">
                            <div>
                                <h5 class="font-bold text-sm text-gray-800">{{ $item['title'] }}</h5>
                                <p class="text-xs text-blue-600 font-semibold">৳{{ number_format($item['price'], 2) }} x {{ $item['quantity'] }}</p>
                                @if($item['prescription_required'])
                                    <span class="inline-block mt-1 px-1.5 py-0.5 bg-red-100 text-red-700 text-[10px] font-bold rounded">Rx Required</span>
                                @endif
                            </div>
                            <div class="flex items-center space-x-2">
                                <button wire:click="updateQuantity({{ $id }}, {{ $item['quantity'] - 1 }})" class="px-2 py-0.5 bg-gray-200 rounded text-sm">-</button>
                                <span class="text-xs font-bold">{{ $item['quantity'] }}</span>
                                <button wire:click="updateQuantity({{ $id }}, {{ $item['quantity'] + 1 }})" class="px-2 py-0.5 bg-gray-200 rounded text-sm">+</button>
                                <button wire:click="removeItem({{ $id }})" class="text-red-500 text-xs ml-2">🗑️</button>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- Drawer Footer / Subtotal & Checkout -->
            <div class="p-4 bg-gray-50 border-t space-y-3">
                <div class="flex justify-between items-center text-lg font-bold text-gray-800">
                    <span>Subtotal:</span>
                    <span class="text-blue-600">৳{{ number_format($this->total, 2) }}</span>
                </div>
                <a href="/checkout" class="block w-full py-3 bg-green-600 hover:bg-green-700 text-white font-bold text-center rounded-lg shadow transition">
                    Proceed to Checkout ➔
                </a>
            </div>

        </div>
    </div>
</div>

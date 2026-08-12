<?php

namespace App\Livewire\Ecommerce;

use Livewire\Component;
use App\Models\Product;

class CartDrawer extends Component
{
    public $isOpen = false;
    public $cartItems = [];

    protected $listeners = [
        'add-to-cart' => 'addItem',
        'open-cart-drawer' => 'openDrawer',
        'close-cart-drawer' => 'closeDrawer'
    ];

    public function mount()
    {
        $this->cartItems = session()->get('cart', []);
    }

    public function addItem($productId)
    {
        $product = Product::find($productId);
        if (!$product) return;

        if (isset($this->cartItems[$productId])) {
            $this->cartItems[$productId]['quantity']++;
        } else {
            $this->cartItems[$productId] = [
                'id' => $product->id,
                'title' => $product->title,
                'price' => $product->sale_price ?? $product->regular_price,
                'quantity' => 1,
                'prescription_required' => $product->prescription_required
            ];
        }

        session()->put('cart', $this->cartItems);
        $this->isOpen = true;
    }

    public function updateQuantity($productId, $qty)
    {
        if ($qty <= 0) {
            unset($this->cartItems[$productId]);
        } else {
            $this->cartItems[$productId]['quantity'] = $qty;
        }

        session()->put('cart', $this->cartItems);
    }

    public function removeItem($productId)
    {
        unset($this->cartItems[$productId]);
        session()->put('cart', $this->cartItems);
    }

    public function openDrawer()
    {
        $this->isOpen = true;
    }

    public function closeDrawer()
    {
        $this->isOpen = false;
    }

    public function getTotalProperty()
    {
        return array_reduce($this->cartItems, function ($sum, $item) {
            return $sum + ($item['price'] * $item['quantity']);
        }, 0);
    }

    public function render()
    {
        return view('components.ecommerce.cart-drawer');
    }
}

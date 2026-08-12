<?php

namespace App\Livewire\Ecommerce;

use Livewire\Component;
use App\Models\Product;

class ProductDetails extends Component
{
    public $product_id;
    public $product;
    public $quantity = 1;
    public $selected_variant_id = null;

    public function mount($id)
    {
        $this->product_id = $id;
        $this->product = Product::with(['variants'])->find($id);
    }

    public function increaseQty()
    {
        $this->quantity++;
    }

    public function decreaseQty()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function addToCart()
    {
        $this->dispatch('add-to-cart', productId: $this->product_id, qty: $this->quantity);
        $this->dispatch('open-cart-drawer');
    }

    public function render()
    {
        return view('components.ecommerce.product-details');
    }
}

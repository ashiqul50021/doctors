<?php

namespace App\Livewire\Ecommerce;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;

class ProductCatalog extends Component
{
    use WithPagination;

    public $search = '';
    public $category_id = '';
    public $is_medical_filter = '';
    public $min_price = '';
    public $max_price = '';
    public $sort_by = 'latest';

    protected $queryString = [
        'search' => ['except' => ''],
        'category_id' => ['except' => ''],
        'is_medical_filter' => ['except' => ''],
        'sort_by' => ['except' => 'latest'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function addToCart($productId)
    {
        $this->dispatch('add-to-cart', productId: $productId);
        $this->dispatch('open-cart-drawer');
    }

    public function render()
    {
        $query = Product::query();

        if (\Schema::hasColumn('products', 'status')) {
            $query->where(function ($q) {
                $q->where('status', 'approved')
                  ->orWhereNull('status');
            });
        } elseif (\Schema::hasColumn('products', 'is_approved')) {
            $query->where('is_approved', 1);
        }

        if (!empty($this->search)) {
            $query->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('generic_name', 'like', '%' . $this->search . '%');
        }

        if ($this->category_id !== '') {
            $query->where('category_id', $this->category_id);
        }

        if ($this->is_medical_filter !== '') {
            $query->where('is_medical', $this->is_medical_filter);
        }

        if (!empty($this->min_price)) {
            $query->where('regular_price', '>=', $this->min_price);
        }

        if (!empty($this->max_price)) {
            $query->where('regular_price', '<=', $this->max_price);
        }

        if ($this->sort_by === 'price_low') {
            $query->orderBy('regular_price', 'asc');
        } elseif ($this->sort_by === 'price_high') {
            $query->orderBy('regular_price', 'desc');
        } else {
            $query->orderBy('id', 'desc');
        }

        $products = $query->paginate(12);

        return view('components.ecommerce.product-catalog', [
            'products' => $products
        ]);
    }
}

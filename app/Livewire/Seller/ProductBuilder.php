<?php

namespace App\Livewire\Seller;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductBuilder extends Component
{
    use WithFileUploads;

    public $product_id;
    public $is_medical = false;
    public $title = '';
    public $subtitle = '';
    public $category_id = '';
    public $short_description = '';
    public $long_description = '';
    public $thumbnail;
    
    // Medical fields
    public $generic_name = '';
    $prescription_required = false;
    public $side_effects_warnings = '';
    
    // Pricing & Stock
    public $regular_price = 0;
    public $sale_price = null;
    public $product_type = 'single';
    public $single_stock = 0;
    public $single_sku = '';

    // Variant attributes array
    public $variants = [];

    // Custom Details / Landing Page Sections
    public $custom_sections = [];

    protected $rules = [
        'title' => 'required|string|max:255',
        'regular_price' => 'required|numeric|min:0',
        'product_type' => 'required|in:single,variant',
    ];

    public function mount($id = null)
    {
        if ($id) {
            $product = Product::findOrFail($id);
            $this->product_id = $product->id;
            $this->title = $product->title;
            $this->is_medical = $product->is_medical;
            $this->generic_name = $product->generic_name;
            $this->prescription_required = $product->prescription_required;
            $this->side_effects_warnings = $product->side_effects_warnings;
            $this->regular_price = $product->regular_price;
            $this->sale_price = $product->sale_price;
            $this->product_type = $product->product_type;
            $this->single_stock = $product->single_stock;
            $this->single_sku = $product->single_sku;
            $this->custom_sections = $product->custom_sections ?? [];
        }
    }

    public function addVariant()
    {
        $this->variants[] = [
            'sku' => '',
            'attribute_name' => '',
            'attribute_value' => '',
            'price' => $this->regular_price,
            'stock' => 0
        ];
    }

    public function removeVariant($index)
    {
        unset($this->variants[$index]);
        $this->variants = array_values($this->variants);
    }

    public function addCustomSection($type = 'faq')
    {
        if ($type === 'faq') {
            $this->custom_sections[] = [
                'type' => 'faq',
                'question' => '',
                'answer' => ''
            ];
        } elseif ($type === 'video') {
            $this->custom_sections[] = [
                'type' => 'video',
                'title' => '',
                'video_url' => ''
            ];
        } elseif ($type === 'steps') {
            $this->custom_sections[] = [
                'type' => 'steps',
                'title' => '',
                'description' => ''
            ];
        }
    }

    public function removeCustomSection($index)
    {
        unset($this->custom_sections[$index]);
        $this->custom_sections = array_values($this->custom_sections);
    }

    public function saveProduct()
    {
        $this->validate();

        $data = [
            'seller_id' => auth()->id(),
            'is_medical' => $this->is_medical,
            'title' => $this->title,
            'slug' => Str::slug($this->title) . '-' . Str::random(5),
            'generic_name' => $this->is_medical ? $this->generic_name : null,
            'prescription_required' => $this->is_medical ? $this->prescription_required : false,
            'side_effects_warnings' => $this->is_medical ? $this->side_effects_warnings : null,
            'regular_price' => $this->regular_price,
            'sale_price' => $this->sale_price,
            'product_type' => $this->product_type,
            'single_stock' => $this->single_stock,
            'single_sku' => $this->single_sku,
            'status' => 'pending', // Undergoes Admin Approval
            'custom_sections' => $this->custom_sections
        ];

        if ($this->product_id) {
            Product::where('id', $this->product_id)->update($data);
            session()->flash('message', 'Product updated and submitted for approval.');
        } else {
            Product::create($data);
            session()->flash('message', 'Product created and submitted for Admin approval.');
        }

        return redirect()->to('/seller/products');
    }

    public function render()
    {
        return view('components.seller.product-builder');
    }
}

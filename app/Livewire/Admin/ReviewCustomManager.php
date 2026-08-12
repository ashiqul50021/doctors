<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\ProductReview;
use App\Models\Product;

class ReviewCustomManager extends Component
{
    public $product_id;
    public $reviewer_name = '';
    public $reviewer_avatar = '';
    public $rating = 5;
    public $comment = '';
    public $custom_date;

    protected $rules = [
        'product_id' => 'required|exists:products,id',
        'reviewer_name' => 'required|string|max:255',
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'required|string',
    ];

    public function createCustomReview()
    {
        $this->validate();

        ProductReview::create([
            'product_id' => $this->product_id,
            'reviewer_name' => $this->reviewer_name,
            'reviewer_avatar' => $this->reviewer_avatar,
            'rating' => $this->rating,
            'comment' => $this->comment,
            'status' => 'approved', // Auto-approved because created by Admin
            'is_admin_custom' => true,
            'created_at' => $this->custom_date ?: now(),
        ]);

        session()->flash('message', 'Custom review published successfully.');
        $this->reset(['reviewer_name', 'reviewer_avatar', 'comment']);
    }

    public function approveReview($id)
    {
        ProductReview::where('id', $id)->update(['status' => 'approved']);
        session()->flash('message', 'Review approved.');
    }

    public function rejectReview($id)
    {
        ProductReview::where('id', $id)->update(['status' => 'rejected']);
        session()->flash('message', 'Review rejected.');
    }

    public function render()
    {
        $pending_reviews = ProductReview::with('product')->where('status', 'pending')->get();
        $products = Product::select('id', 'title')->get();

        return view('components.admin.review-custom-manager', [
            'pending_reviews' => $pending_reviews,
            'products' => $products
        ]);
    }
}

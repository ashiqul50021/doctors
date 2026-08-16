<?php

namespace Modules\Ecommerce\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Product;
use App\Models\ProductReview;

class ProductReviewController extends Controller
{
    public function index()
    {
        $reviews = ProductReview::with('product')->latest()->paginate(15);
        return view('ecommerce::backend.product_reviews.index', compact('reviews'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->latest()->take(20)->get();
        return view('ecommerce::backend.product_reviews.create', compact('products'));
    }

    public function searchProducts(Request $request)
    {
        $query = $request->get('q');
        
        $products = Product::where('is_active', true)
            ->when($query, function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%");
            })
            ->select(['id', 'name', 'image', 'gallery'])
            ->latest()
            ->take(30)
            ->get()
            ->map(function ($product) {
                $image = $product->image;
                if (!$image && !empty($product->gallery) && is_array($product->gallery)) {
                    $image = $product->gallery[0] ?? null;
                }

                $imageUrl = $image ? asset($image) : asset('assets/img/products/default-product.png');

                return [
                    'id' => $product->id,
                    'text' => $product->name,
                    'image' => $imageUrl,
                ];
            });

        return response()->json(['results' => $products]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'reviewer_name' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:120',
            'comment' => 'required|string|max:1000',
            'is_verified_purchase' => 'nullable|boolean',
        ]);

        ProductReview::create([
            'product_id' => $validated['product_id'],
            'patient_id' => null,
            'order_id' => null,
            'reviewer_name' => $validated['reviewer_name'],
            'rating' => (int) $validated['rating'],
            'title' => $validated['title'] ?? null,
            'comment' => $validated['comment'],
            'is_verified_purchase' => $request->has('is_verified_purchase'),
            'is_approved' => true,
        ]);

        return redirect()
            ->route('ecommerce.admin.product-reviews.index')
            ->with('success', 'Custom review created successfully.');
    }

    public function approve(ProductReview $review)
    {
        $review->update(['is_approved' => true]);

        return redirect()->back()->with('success', 'Review approved successfully.');
    }

    public function destroy(ProductReview $review)
    {
        $review->delete();

        return redirect()->back()->with('success', 'Review deleted successfully.');
    }
}

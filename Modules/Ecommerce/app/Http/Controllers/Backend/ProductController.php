<?php

namespace Modules\Ecommerce\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use App\Services\ImageService;
use Modules\Ecommerce\Models\Product;
use Modules\Ecommerce\Models\ProductCategory;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->latest()->get();
        return view('ecommerce::backend.products.index', compact('products'));
    }

    public function create()
    {
        $categories = ProductCategory::where('is_active', true)->get();
        return view('ecommerce::backend.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'product_category_id' => 'required|exists:product_categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = ImageService::upload($request->file('image'), 'products');
        }

        Product::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'product_category_id' => $request->product_category_id,
            'description' => $request->description,
            'price' => $request->price,
            'sale_price' => $request->sale_price,
            'stock' => $request->stock,
            'image' => $imagePath,
            'is_active' => true,
            'is_featured' => $request->has('is_featured'),
        ]);

        return redirect()->route('ecommerce.admin.products.index')->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        return view('ecommerce::backend.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = ProductCategory::where('is_active', true)->get();
        return view('ecommerce::backend.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'product_category_id' => 'required|exists:product_categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'product_category_id' => $request->product_category_id,
            'description' => $request->description,
            'price' => $request->price,
            'sale_price' => $request->sale_price,
            'stock' => $request->stock,
            'is_active' => $request->has('is_active'),
            'is_featured' => $request->has('is_featured'),
        ];

        if ($request->hasFile('image')) {
            ImageService::delete($product->image);
            $data['image'] = ImageService::upload($request->file('image'), 'products');
        }

        $product->update($data);

        return redirect()->route('ecommerce.admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        ImageService::delete($product->image);
        $product->delete();

        return redirect()->route('ecommerce.admin.products.index')->with('success', 'Product deleted successfully.');
    }
}

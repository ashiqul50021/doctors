<?php

namespace Modules\Ecommerce\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use App\Services\ImageService;
use Modules\Ecommerce\Models\ProductCategory;

class ProductCategoryController extends Controller
{
    public function index()
    {
        $categories = ProductCategory::latest()->get();
        return view('ecommerce::backend.product-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('ecommerce::backend.product-categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = ImageService::upload($request->file('image'), 'product_categories');
        }

        ProductCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'image' => $imagePath,
            'is_active' => true,
        ]);

        return redirect()->route('ecommerce.admin.product-categories.index')->with('success', 'Category created successfully.');
    }

    public function show(ProductCategory $productCategory)
    {
        return view('ecommerce::backend.product-categories.show', compact('productCategory'));
    }

    public function edit(ProductCategory $productCategory)
    {
        return view('ecommerce::backend.product-categories.edit', compact('productCategory'));
    }

    public function update(Request $request, ProductCategory $productCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        $data = [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ];

        if ($request->hasFile('image')) {
            ImageService::delete($productCategory->image);
            $data['image'] = ImageService::upload($request->file('image'), 'product_categories');
        }

        $productCategory->update($data);

        return redirect()->route('ecommerce.admin.product-categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(ProductCategory $productCategory)
    {
        ImageService::delete($productCategory->image);
        $productCategory->delete();

        return redirect()->route('ecommerce.admin.product-categories.index')->with('success', 'Category deleted successfully.');
    }
}

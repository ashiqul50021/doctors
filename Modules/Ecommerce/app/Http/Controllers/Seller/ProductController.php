<?php

namespace Modules\Ecommerce\Http\Controllers\Seller;

use App\Services\ProductStockService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Services\ImageService;
use Modules\Ecommerce\Models\Product;
use Modules\Ecommerce\Models\ProductCategory;

class ProductController extends Controller
{
    public function __construct(protected ProductStockService $stockService)
    {
    }

    public function index()
    {
        $sellerId = auth()->id();
        $products = Product::where('seller_id', $sellerId)
            ->with(['category', 'variants'])
            ->latest()
            ->get();

        return view('ecommerce::seller.products.index', compact('products'));
    }

    public function show(Request $request, $id)
    {
        $sellerId = auth()->id();
        $product = Product::where('seller_id', $sellerId)
            ->with(['category', 'variants', 'seller'])
            ->findOrFail($id);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'html' => view('ecommerce::backend.products.partials.details-modal-body', compact('product'))->render()
            ]);
        }

        return view('ecommerce::seller.products.show', compact('product'));
    }

    public function create()
    {
        $categories = ProductCategory::with([
                'children' => function($query) {
                    $query->where('is_active', true)->orderBy('name');
                },
                'children.children' => function($query) {
                    $query->where('is_active', true)->orderBy('name');
                }
            ])
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('ecommerce::seller.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'product_category_id' => 'required|exists:product_categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'gallery' => 'nullable|array|max:30',
            'gallery.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'landing_settings' => 'nullable|array',
        ]);

        $variants = $this->extractVariantPayloads($request);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = ImageService::upload($request->file('image'), 'products');
        }

        $galleryPaths = $request->hasFile('gallery')
            ? ImageService::uploadMany($request->file('gallery'), 'products')
            : [];

        try {
            DB::transaction(function () use ($validated, $request, $imagePath, $galleryPaths, $variants) {
                $product = Product::create([
                    'name' => $validated['name'],
                    'slug' => $this->generateUniqueSlug($validated['name']),
                    'product_category_id' => $validated['product_category_id'],
                    'description' => $request->description,
                    'price' => $validated['price'],
                    'sale_price' => $request->sale_price,
                    'stock' => (int) ($validated['stock'] ?? 0),
                    'image' => $imagePath,
                    'gallery' => $galleryPaths,
                    'is_active' => false,
                    'is_approved' => false,
                    'status' => 'pending',
                    'rejection_reason' => null,
                    'is_featured' => false,
                    'landing_settings' => $request->landing_settings,
                    'seller_id' => auth()->id(),
                ]);

                $this->syncVariants($product, $variants);
            });
        } catch (\Throwable $exception) {
            ImageService::deleteMany(array_filter([$imagePath, ...$galleryPaths]));
            throw $exception;
        }

        return redirect()->route('ecommerce.seller.products.index')->with('success', 'Product submitted successfully! It will be live once approved by an Admin.');
    }

    public function edit(Product $product)
    {
        if ($product->seller_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $product->load([
            'variants' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order')->orderBy('id'),
        ]);

        $categories = ProductCategory::with([
                'children' => function($query) {
                    $query->where('is_active', true)->orderBy('name');
                },
                'children.children' => function($query) {
                    $query->where('is_active', true)->orderBy('name');
                }
            ])
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('ecommerce::seller.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        if ($product->seller_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'product_category_id' => 'required|exists:product_categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'gallery' => 'nullable|array|max:30',
            'gallery.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'removed_gallery' => 'nullable|array',
            'removed_gallery.*' => 'nullable|string',
            'landing_settings' => 'nullable|array',
        ]);

        $variants = $this->extractVariantPayloads($request);
        $currentGalleryPaths = collect($product->gallery ?? [])
            ->filter()
            ->values();
        $removedGalleryPaths = collect($request->input('removed_gallery', []))
            ->filter()
            ->values();
        $galleryPaths = $currentGalleryPaths
            ->reject(fn ($path) => $removedGalleryPaths->contains($path))
            ->values()
            ->all();
        $newGalleryPaths = $request->hasFile('gallery')
            ? ImageService::uploadMany($request->file('gallery'), 'products')
            : [];
        $galleryPaths = collect($galleryPaths)
            ->merge($newGalleryPaths)
            ->filter()
            ->unique()
            ->values()
            ->all();
        $oldPrimaryImagePath = $product->image;
        $newPrimaryImagePath = null;

        $data = [
            'name' => $validated['name'],
            'slug' => $this->generateUniqueSlug($validated['name'], $product->id),
            'product_category_id' => $validated['product_category_id'],
            'description' => $request->description,
            'price' => $validated['price'],
            'sale_price' => $request->sale_price,
            'stock' => (int) ($validated['stock'] ?? 0),
            'has_variants' => $request->has('has_variants'),
            'override_shipping' => $request->has('override_shipping'),
            'inside_dhaka_charge' => $request->has('override_shipping') ? $request->inside_dhaka_charge : null,
            'outside_dhaka_charge' => $request->has('override_shipping') ? $request->outside_dhaka_charge : null,
            'gallery' => $galleryPaths,
            'landing_settings' => $request->landing_settings,
        ];

        if ($request->hasFile('image')) {
            $newPrimaryImagePath = ImageService::upload($request->file('image'), 'products');
            $data['image'] = $newPrimaryImagePath;
        }

        try {
            DB::transaction(function () use ($product, $data, $variants) {
                $product->update($data);
                $this->syncVariants($product, $variants);
            });
        } catch (\Throwable $exception) {
            ImageService::deleteMany(array_filter([
                $newPrimaryImagePath,
                ...$newGalleryPaths,
            ]));

            throw $exception;
        }

        $pathsToDelete = $currentGalleryPaths
            ->intersect($removedGalleryPaths)
            ->reject(fn ($path) => $path === $product->image)
            ->values()
            ->all();

        if ($oldPrimaryImagePath && $newPrimaryImagePath && $oldPrimaryImagePath !== $newPrimaryImagePath && ! in_array($oldPrimaryImagePath, $galleryPaths, true)) {
            $pathsToDelete[] = $oldPrimaryImagePath;
        }

        ImageService::deleteMany(array_unique(array_filter($pathsToDelete)));

        return redirect()->route('ecommerce.seller.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        if ($product->seller_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        ImageService::deleteMany(
            collect([$product->image])
                ->merge($product->gallery ?? [])
                ->filter()
                ->unique()
                ->values()
                ->all()
        );
        $product->delete();

        return redirect()->route('ecommerce.seller.products.index')->with('success', 'Product deleted successfully.');
    }

    protected function extractVariantPayloads(Request $request): array
    {
        $variants = collect($request->input('variants', []))
            ->map(function ($variant) {
                return [
                    'id' => isset($variant['id']) && $variant['id'] !== '' ? (int) $variant['id'] : null,
                    'option_name' => trim((string) ($variant['option_name'] ?? '')),
                    'option_value' => trim((string) ($variant['option_value'] ?? '')),
                    'sku' => trim((string) ($variant['sku'] ?? '')),
                    'price' => $variant['price'] ?? null,
                    'sale_price' => $variant['sale_price'] ?? null,
                    'stock' => $variant['stock'] ?? null,
                    'is_active' => isset($variant['is_active']) ? (bool) $variant['is_active'] : false,
                ];
            })
            ->filter(function (array $variant) {
                return collect($variant)
                    ->except(['id'])
                    ->contains(fn ($value) => $value !== null && $value !== '' && $value !== false);
            })
            ->values();

        $validator = Validator::make(
            ['variants' => $variants->all()],
            [
                'variants' => 'array',
                'variants.*.id' => 'nullable|integer',
                'variants.*.option_name' => 'nullable|string|max:100',
                'variants.*.option_value' => 'required|string|max:100',
                'variants.*.sku' => 'nullable|string|max:100',
                'variants.*.price' => 'required|numeric|min:0',
                'variants.*.sale_price' => 'nullable|numeric|min:0',
                'variants.*.stock' => 'required|integer|min:0',
                'variants.*.is_active' => 'boolean',
            ],
            [],
            [
                'variants.*.option_name' => 'variant type',
                'variants.*.option_value' => 'variant value',
                'variants.*.sale_price' => 'variant sale price',
                'variants.*.stock' => 'variant stock',
            ]
        );

        $validator->after(function ($validator) use ($variants) {
            $labels = [];

            foreach ($variants as $index => $variant) {
                if ($variant['sale_price'] !== null && $variant['sale_price'] !== '' && (float) $variant['sale_price'] > (float) $variant['price']) {
                    $validator->errors()->add("variants.{$index}.sale_price", 'Variant sale price cannot be greater than the regular price.');
                }

                $labelKey = Str::lower(trim($variant['option_name'] . '|' . $variant['option_value']));
                if (isset($labels[$labelKey])) {
                    $validator->errors()->add("variants.{$index}.option_value", 'Duplicate variant rows are not allowed for the same product.');
                }

                $labels[$labelKey] = true;
            }
        });

        $validator->validate();

        return $variants->all();
    }

    protected function syncVariants(Product $product, array $variants): void
    {
        if (empty($variants)) {
            $product->variants()->update([
                'is_active' => false,
                'stock' => 0,
            ]);

            return;
        }

        $submittedIds = [];

        foreach ($variants as $index => $variant) {
            $payload = [
                'option_name' => $variant['option_name'] ?: null,
                'option_value' => $variant['option_value'],
                'sku' => $variant['sku'] ?: null,
                'price' => $variant['price'],
                'sale_price' => $variant['sale_price'] !== '' ? $variant['sale_price'] : null,
                'stock' => $variant['stock'],
                'is_active' => $variant['is_active'],
                'sort_order' => $index,
            ];

            if ($variant['id']) {
                $existingVariant = $product->variants()->whereKey($variant['id'])->first();

                if ($existingVariant) {
                    $existingVariant->update($payload);
                    $submittedIds[] = $existingVariant->id;
                    continue;
                }
            }

            $createdVariant = $product->variants()->create($payload);
            $submittedIds[] = $createdVariant->id;
        }

        $product->variants()
            ->whereNotIn('id', $submittedIds)
            ->update([
                'is_active' => false,
                'stock' => 0,
            ]);

        $this->stockService->syncAggregateStock($product->id);
    }

    public function toggleStatus(Product $product)
    {
        $sellerId = auth()->id();
        if ($product->seller_id && $product->seller_id != $sellerId) {
            abort(403, 'Unauthorized action.');
        }

        $product->update([
            'is_active' => !$product->is_active
        ]);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => (bool) $product->is_active,
                'message' => 'Product status updated successfully.'
            ]);
        }

        return redirect()->back()->with('success', 'Product status updated successfully.');
    }

    protected function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (Product::where('slug', $slug)->when($ignoreId, fn($query) => $query->where('id', '!=', $ignoreId))->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}

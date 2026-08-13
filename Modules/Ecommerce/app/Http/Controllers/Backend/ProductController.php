<?php

namespace Modules\Ecommerce\Http\Controllers\Backend;

use App\Services\ProductStockService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Services\ImageService;
use Modules\Ecommerce\Models\Product;
use Modules\Ecommerce\Models\ProductCategory;

use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    public function __construct(protected ProductStockService $stockService)
    {
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $products = Product::with(['category', 'variants'])->select('products.*');

            return DataTables::of($products)
                ->addIndexColumn()
                ->editColumn('code', function ($row) {
                    return '<span class="product-code-badge">#PRO' . $row->id . '</span>';
                })
                ->editColumn('name', function ($row) {
                    $image = $row->image;
                    if (!$image && !empty($row->gallery) && is_array($row->gallery)) {
                        $image = $row->gallery[0] ?? null;
                    }
                    $imgUrl = $image ? (Str::startsWith($image, ['http://', 'https://']) ? $image : asset($image)) : null;

                    $avatarHtml = $imgUrl 
                        ? '<a href="#" class="avatar avatar-sm me-2"><img class="avatar-img" src="' . $imgUrl . '" alt="Product"></a>'
                        : '<span class="avatar avatar-sm me-2 d-inline-flex align-items-center justify-content-center bg-light text-muted border" style="font-size: 9px; font-weight: 600;">No Image</span>';

                    return '<div class="table-avatar">' . $avatarHtml . '<a href="#" class="fw-semibold text-dark">' . e($row->name) . '</a></div>';
                })
                ->editColumn('category', function ($row) {
                    return '<span class="text-secondary fw-semibold">' . e($row->category->name ?? 'N/A') . '</span>';
                })
                ->editColumn('price', function ($row) {
                    return '<span class="price-text">৳' . number_format($row->price, 2) . '</span>';
                })
                ->editColumn('stock', function ($row) {
                    $availableStock = $row->availableStock();
                    $activeVariantCount = $row->activeVariantItems()->count();
                    if ($availableStock < 1) {
                        $stockBadgeClass = 'bg-danger-light';
                        $stockLabel = 'Out of Stock';
                    } elseif ($availableStock <= 10) {
                        $stockBadgeClass = 'bg-warning-light';
                        $stockLabel = 'Low Stock';
                    } else {
                        $stockBadgeClass = 'bg-success-light';
                        $stockLabel = 'In Stock';
                    }
                    $variantText = $activeVariantCount > 0 ? $activeVariantCount . ' variant(s)' : 'Simple product';
                    return '<div class="d-flex flex-column">
                        <span class="fw-bold text-dark">' . $availableStock . '</span>
                        <span class="badge ' . $stockBadgeClass . ' mt-1" style="width: fit-content;">' . $stockLabel . '</span>
                        <small class="text-muted mt-1" style="font-size: 11px;">' . $variantText . '</small>
                    </div>';
                })
                ->editColumn('status', function ($row) {
                    $checked = $row->is_active ? 'checked' : '';
                    return '<div class="status-toggle">
                        <input type="checkbox" id="status_' . $row->id . '" class="check status-toggle-btn" data-id="' . $row->id . '" ' . $checked . '>
                        <label for="status_' . $row->id . '" class="checktoggle">checkbox</label>
                    </div>';
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('ecommerce.admin.products.edit', $row->id);
                    $destroyUrl = route('ecommerce.admin.products.destroy', $row->id);
                    $csrf = csrf_field();
                    $method = method_field('DELETE');
                    return '<div class="actions text-end">
                        <a class="btn-action-edit" href="' . $editUrl . '"><i class="fe fe-pencil"></i> Edit</a>
                        <form action="' . $destroyUrl . '" method="POST" style="display:inline-block;" onsubmit="return confirm(\'Are you sure?\');">
                            ' . $csrf . '
                            ' . $method . '
                            <button type="submit" class="btn-action-delete"><i class="fe fe-trash"></i> Delete</button>
                        </form>
                    </div>';
                })
                ->rawColumns(['code', 'name', 'category', 'price', 'stock', 'status', 'action'])
                ->make(true);
        }

        $products = Product::with(['category', 'variants'])->latest()->get();
        return view('ecommerce::backend.products.index', compact('products'));
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
        return view('ecommerce::backend.products.create', compact('categories'));
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
                    'slug' => Str::slug($validated['name']),
                    'product_category_id' => $validated['product_category_id'],
                    'description' => $request->description,
                    'price' => $validated['price'],
                    'sale_price' => $request->sale_price,
                    'stock' => (int) ($validated['stock'] ?? 0),
                    'image' => $imagePath,
                    'gallery' => $galleryPaths,
                    'is_active' => true,
                    'is_featured' => $request->has('is_featured'),
                    'landing_settings' => $request->landing_settings,
                ]);

                $this->syncVariants($product, $variants);
            });
        } catch (\Throwable $exception) {
            ImageService::deleteMany(array_filter([$imagePath, ...$galleryPaths]));
            throw $exception;
        }

        return redirect()->route('ecommerce.admin.products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
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
        return view('ecommerce::backend.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
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
            'slug' => Str::slug($validated['name']),
            'product_category_id' => $validated['product_category_id'],
            'description' => $request->description,
            'price' => $validated['price'],
            'sale_price' => $request->sale_price,
            'stock' => (int) ($validated['stock'] ?? 0),
            'gallery' => $galleryPaths,
            'is_active' => $request->has('is_active'),
            'is_featured' => $request->has('is_featured'),
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

        return redirect()->route('ecommerce.admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        ImageService::deleteMany(
            collect([$product->image])
                ->merge($product->gallery ?? [])
                ->filter()
                ->unique()
                ->values()
                ->all()
        );
        $product->delete();

        return redirect()->route('ecommerce.admin.products.index')->with('success', 'Product deleted successfully.');
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
}

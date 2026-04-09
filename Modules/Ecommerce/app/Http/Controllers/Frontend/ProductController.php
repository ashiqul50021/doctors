<?php

namespace Modules\Ecommerce\Http\Controllers\Frontend;

use App\Services\ProductStockService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\Ecommerce\Models\Product;
use Modules\Ecommerce\Models\ProductCategory;
use Modules\Ecommerce\Models\Order;
use Modules\Ecommerce\Models\OrderItem;

class ProductController extends Controller
{
    public function __construct(protected ProductStockService $stockService)
    {
    }

    protected function shouldReturnJson(Request $request): bool
    {
        return $request->ajax() || $request->expectsJson();
    }

    protected function calculateCartTotal(array $cart): float
    {
        return collect($cart)->sum(function ($item) {
            return ((float) ($item['price'] ?? 0)) * ((int) ($item['quantity'] ?? 0));
        });
    }

    protected function handleInventoryException(Request $request, ValidationException $exception)
    {
        $message = collect($exception->errors())->flatten()->first()
            ?? 'Unable to process the requested stock quantity.';

        if ($this->shouldReturnJson($request)) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'errors' => $exception->errors(),
            ], 422);
        }

        return redirect()->back()
            ->withErrors($exception->errors())
            ->withInput();
    }

    protected function productImageUrl(Product $product): string
    {
        $image = $product->image;

        if (! $image && is_array($product->gallery) && ! empty($product->gallery)) {
            $image = $product->gallery[0] ?? null;
        }

        if (! $image) {
            return asset('assets/img/products/default-product.png');
        }

        if (Str::startsWith($image, ['http://', 'https://', '//'])) {
            return $image;
        }

        return asset(ltrim($image, '/'));
    }

    protected function ensureCartHasAvailableStock(array $cart): void
    {
        foreach ($cart as $cartKey => $item) {
            $this->stockService->ensureRequestedQuantityIsAvailable(
                $this->resolveCartProductId($cartKey, $item),
                (int) ($item['quantity'] ?? 0),
                $this->resolveCartVariantId($cartKey, $item)
            );
        }
    }

    protected function getNormalizedCart(): array
    {
        $cart = session()->get('cart', []);
        $normalizedCart = $this->normalizeCart($cart);

        if ($normalizedCart != $cart) {
            session()->put('cart', $normalizedCart);
        }

        return $normalizedCart;
    }

    protected function normalizeCart(array $cart): array
    {
        $normalizedCart = [];

        foreach ($cart as $cartKey => $item) {
            $productId = $this->resolveCartProductId($cartKey, $item);

            if ($productId < 1) {
                continue;
            }

            $variantId = $this->resolveCartVariantId($cartKey, $item);
            $normalizedKey = $this->buildCartKey($productId, $variantId);
            $quantity = max(1, (int) ($item['quantity'] ?? 1));

            if (isset($normalizedCart[$normalizedKey])) {
                $normalizedCart[$normalizedKey]['quantity'] += $quantity;
                continue;
            }

            $normalizedCart[$normalizedKey] = [
                'product_id' => $productId,
                'variant_id' => $variantId,
                'variant_label' => $item['variant_label'] ?? null,
                'name' => $item['name'] ?? '',
                'price' => (float) ($item['price'] ?? 0),
                'image' => $item['image'] ?? null,
                'quantity' => $quantity,
            ];
        }

        return $normalizedCart;
    }

    protected function resolveCartProductId(string|int $cartKey, array $item): int
    {
        if (!empty($item['product_id'])) {
            return (int) $item['product_id'];
        }

        if (is_numeric($cartKey)) {
            return (int) $cartKey;
        }

        if (preg_match('/product_(\d+)/', (string) $cartKey, $matches)) {
            return (int) $matches[1];
        }

        return 0;
    }

    protected function resolveCartVariantId(string|int $cartKey, array $item): ?int
    {
        if (!empty($item['variant_id'])) {
            return (int) $item['variant_id'];
        }

        if (preg_match('/variant_(\d+)/', (string) $cartKey, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    public function index(Request $request)
    {
        $query = Product::with(['category', 'variants'])->where('is_active', true);

        if ($request->has('category') && $request->category) {
            $query->where('product_category_id', $request->category);
        }

        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->latest()->paginate(12);
        $categories = ProductCategory::orderBy('name')->get();

        return view('ecommerce::frontend.products.index', compact('products', 'categories'));
    }

    public function show($id)
    {
        $product = Product::with(['category', 'variants'])->findOrFail($id);
        $relatedProducts = Product::with(['category', 'variants'])->where('product_category_id', $product->product_category_id)
            ->where('id', '!=', $id)
            ->where('is_active', true)
            ->limit(4)
            ->get();

        return view('ecommerce::frontend.products.show', compact('product', 'relatedProducts'));
    }

    public function filter(Request $request)
    {
        $query = Product::with(['category', 'variants'])->where('is_active', true);

        if ($request->category && $request->category !== 'all') {
            $query->where('product_category_id', $request->category);
        }

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->take(8)->latest()->get()
            ->map(function (Product $product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'image' => $product->image,
                    'image_url' => $this->productImageUrl($product),
                    'price' => $product->effectivePrice(),
                    'regular_price' => $product->effectiveRegularPrice(),
                    'sale_price' => $product->effectivePrice() < $product->effectiveRegularPrice()
                        ? $product->effectivePrice()
                        : null,
                    'stock' => $product->availableStock(),
                    'has_variants' => $product->hasActiveVariants(),
                    'uses_price_range' => $product->usesPriceRange(),
                    'category' => $product->category ? [
                        'id' => $product->category->id,
                        'name' => $product->category->name,
                    ] : null,
                    'rating' => $product->rating ?? null,
                    'reviews_count' => $product->reviews_count ?? null,
                ];
            });

        return response()->json($products->values());
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'variant_id' => 'nullable|integer|exists:product_variants,id',
        ]);

        $cart = $this->getNormalizedCart();
        $productId = (int) $request->product_id;
        $variantId = $request->filled('variant_id') ? (int) $request->variant_id : null;
        $cartKey = $this->buildCartKey($productId, $variantId);
        $requestedQuantity = (int) $request->quantity;
        $newQuantity = ((int) ($cart[$cartKey]['quantity'] ?? 0)) + $requestedQuantity;

        try {
            $inventory = $this->stockService->ensureRequestedQuantityIsAvailable($productId, $newQuantity, $variantId);
        } catch (ValidationException $exception) {
            return $this->handleInventoryException($request, $exception);
        }

        $product = $inventory['product'];
        $variant = $inventory['variant'];

        $cart[$cartKey] = [
            'product_id' => $productId,
            'variant_id' => $variant?->id,
            'variant_label' => $variant?->display_label,
            'name' => $product->name,
            'price' => $variant ? $variant->currentPrice() : ($product->sale_price ?? $product->price),
            'image' => $product->image,
            'quantity' => $newQuantity,
        ];

        session()->put('cart', $cart);

        if ($request->has('buy_now')) {
            return redirect()->route('ecommerce.checkout');
        }

        if ($this->shouldReturnJson($request)) {
            return response()->json([
                'success' => true,
                'message' => 'Product added to cart!',
                'cartCount' => count($cart),
                'total' => $this->calculateCartTotal($cart),
            ]);
        }

        return redirect()->back()->with('success', 'Product added to cart!');
    }

    public function cart()
    {
        $cart = $this->getNormalizedCart();
        $total = $this->calculateCartTotal($cart);

        return view('ecommerce::frontend.cart', compact('cart', 'total'));
    }

    public function removeFromCart(Request $request)
    {
        $cart = $this->getNormalizedCart();
        $cartKey = (string) $request->input('cart_key', $request->input('product_id'));

        if (isset($cart[$cartKey])) {
            unset($cart[$cartKey]);
            session()->put('cart', $cart);
        }

        if ($this->shouldReturnJson($request)) {
            return response()->json([
                'success' => true,
                'message' => 'Product removed from cart!',
                'cartCount' => count($cart),
                'total' => $this->calculateCartTotal($cart),
            ]);
        }

        return redirect()->back()->with('success', 'Product removed from cart!');
    }

    public function updateCart(Request $request)
    {
        $request->validate([
            'cart_key' => 'required|string',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = $this->getNormalizedCart();
        $cartKey = (string) $request->cart_key;

        if (!isset($cart[$cartKey])) {
            $message = 'This product is no longer in your cart.';

            if ($this->shouldReturnJson($request)) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 404);
            }

            return redirect()->route('ecommerce.cart')->with('error', $message);
        }

        $cartItem = $cart[$cartKey];
        $productId = $this->resolveCartProductId($cartKey, $cartItem);
        $variantId = $this->resolveCartVariantId($cartKey, $cartItem);

        try {
            $inventory = $this->stockService->ensureRequestedQuantityIsAvailable($productId, (int) $request->quantity, $variantId);
        } catch (ValidationException $exception) {
            return $this->handleInventoryException($request, $exception);
        }

        $product = $inventory['product'];
        $variant = $inventory['variant'];

        $cart[$cartKey] = [
            'product_id' => $productId,
            'variant_id' => $variant?->id,
            'variant_label' => $variant?->display_label,
            'name' => $product->name,
            'price' => $variant ? $variant->currentPrice() : ($product->sale_price ?? $product->price),
            'image' => $product->image,
            'quantity' => (int) $request->quantity,
        ];

        session()->put('cart', $cart);

        if ($this->shouldReturnJson($request)) {
            $itemSubtotal = isset($cart[$cartKey])
                ? ((float) $cart[$cartKey]['price']) * ((int) $cart[$cartKey]['quantity'])
                : 0;

            return response()->json([
                'success' => true,
                'message' => 'Cart updated!',
                'cartCount' => count($cart),
                'total' => $this->calculateCartTotal($cart),
                'itemSubtotal' => $itemSubtotal,
                'quantity' => (int) $cart[$cartKey]['quantity'],
            ]);
        }

        return redirect()->back()->with('success', 'Cart updated!');
    }

    public function checkout()
    {
        $cart = $this->getNormalizedCart();

        if (empty($cart)) {
            return redirect()->route('ecommerce.products')->with('error', 'Your cart is empty!');
        }

        try {
            $this->ensureCartHasAvailableStock($cart);
        } catch (ValidationException $exception) {
            return redirect()->route('ecommerce.cart')->withErrors($exception->errors());
        }

        $total = $this->calculateCartTotal($cart);

        return view('ecommerce::frontend.product-checkout', compact('cart', 'total'));
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
        ]);

        $cart = $this->getNormalizedCart();

        if (empty($cart)) {
            return redirect()->route('ecommerce.products')->with('error', 'Your cart is empty!');
        }

        $total = $this->calculateCartTotal($cart);

        try {
            $order = DB::transaction(function () use ($cart, $request, $total) {
                $this->stockService->reserveCart($cart);

                $order = Order::create([
                    'order_number' => 'ORD-' . strtoupper(uniqid()),
                    'customer_name' => $request->name,
                    'customer_email' => $request->email,
                    'customer_phone' => $request->phone,
                    'shipping_address' => $request->address,
                    'shipping_city' => $request->city ?? 'Dhaka',
                    'shipping_phone' => $request->phone,
                    'subtotal' => $total,
                    'shipping' => 0,
                    'total' => $total,
                    'status' => 'pending',
                    'notes' => $request->notes,
                ]);

                foreach ($cart as $cartKey => $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $this->resolveCartProductId($cartKey, $item),
                        'product_variant_id' => $this->resolveCartVariantId($cartKey, $item),
                        'variant_label' => $item['variant_label'] ?? null,
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'total' => $item['price'] * $item['quantity'],
                    ]);
                }

                return $order;
            });
        } catch (ValidationException $exception) {
            return $this->handleInventoryException($request, $exception);
        }

        session()->forget('cart');

        return redirect()->route('ecommerce.order.success', ['order' => $order->id]);
    }

    public function orderSuccess(Request $request)
    {
        $order = Order::with('items.product', 'items.variant')->findOrFail($request->order);

        return view('ecommerce::frontend.order-success', compact('order'));
    }

    protected function buildCartKey(int $productId, ?int $variantId): string
    {
        if ($variantId) {
            return "product_{$productId}_variant_{$variantId}";
        }

        return "product_{$productId}";
    }
}

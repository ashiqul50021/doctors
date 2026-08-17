<?php

namespace Modules\Ecommerce\Http\Controllers\Frontend;

use App\Models\Patient;
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
use Modules\Ecommerce\Models\ProductReview;
use App\Models\Coupon;

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

    protected function withReviewStats($query)
    {
        return $query
            ->withCount('approvedProductReviews as reviews_count')
            ->withAvg('approvedProductReviews as rating', 'rating');
    }

    protected function resolvePatientFromUser(Request $request): ?Patient
    {
        $user = $request->user();

        if (! $user || $user->role !== 'patient') {
            return null;
        }

        if ($user->patient) {
            return $user->patient;
        }

        return Patient::create([
            'user_id' => $user->id,
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
        ]);
    }

    protected function latestOrderContainingProduct(int $patientId, int $productId): ?Order
    {
        return Order::where('patient_id', $patientId)
            ->whereHas('items', function ($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->latest()
            ->first();
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
        $query = $this->withReviewStats(Product::with(['category', 'variants']))
            ->where('is_active', true);

        $selectedCategory = null;
        if ($request->has('category') && $request->category) {
            $categoryId = (int) $request->category;
            $selectedCategory = ProductCategory::find($categoryId);
            
            if ($selectedCategory) {
                $childIds = ProductCategory::where('parent_id', $categoryId)->pluck('id')->toArray();
                $grandchildIds = [];
                if (!empty($childIds)) {
                    $grandchildIds = ProductCategory::whereIn('parent_id', $childIds)->pluck('id')->toArray();
                }
                $categoryIds = array_merge([$categoryId], $childIds, $grandchildIds);
                $query->whereIn('product_category_id', $categoryIds);
            }
        }

        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->boolean('official')) {
            $query->where(function ($q) {
                $q->whereNull('seller_id')
                  ->orWhereHas('seller', function ($sq) {
                      $sq->where('role', '!=', 'seller');
                  });
            });
        }

        $products = $query->latest()->paginate(12);
        
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

        return view('ecommerce::frontend.products.index', compact('products', 'categories', 'selectedCategory'));
    }

    public function show($id)
    {
        $product = $this->withReviewStats(Product::with(['category', 'variants', 'approvedProductReviews.patient.user']))
            ->findOrFail($id);
        $relatedProducts = $this->withReviewStats(Product::with(['category', 'variants']))
            ->where('product_category_id', $product->product_category_id)
            ->where('id', '!=', $id)
            ->where('is_active', true)
            ->limit(4)
            ->get();

        return view('ecommerce::frontend.products.show', compact('product', 'relatedProducts'));
    }

    public function filter(Request $request)
    {
        $query = $this->withReviewStats(Product::with(['category', 'variants']))
            ->where('is_active', true);

        if ($request->category && $request->category !== 'all') {
            $categoryId = (int) $request->category;
            $childIds = ProductCategory::where('parent_id', $categoryId)->pluck('id')->toArray();
            $grandchildIds = [];
            if (!empty($childIds)) {
                $grandchildIds = ProductCategory::whereIn('parent_id', $childIds)->pluck('id')->toArray();
            }
            $categoryIds = array_merge([$categoryId], $childIds, $grandchildIds);
            $query->whereIn('product_category_id', $categoryIds);
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

    public function storeReview(Request $request, Product $product)
    {
        $rules = [
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:120',
            'comment' => 'required|string|max:1000',
        ];

        if (!auth()->check() || !auth()->user()->patient) {
            $rules['reviewer_name'] = 'required|string|max:255';
        }

        $validated = $request->validate($rules);

        $patient = $this->resolvePatientFromUser($request);
        $reviewerName = null;
        $order = null;

        if ($patient) {
            $reviewerName = $patient->user->name;
            $order = $this->latestOrderContainingProduct($patient->id, $product->id);

            $review = ProductReview::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'patient_id' => $patient->id,
                ],
                [
                    'order_id' => $order?->id,
                    'reviewer_name' => $reviewerName,
                    'rating' => (int) $validated['rating'],
                    'title' => $validated['title'] ?? null,
                    'comment' => $validated['comment'],
                    'is_verified_purchase' => (bool) $order,
                    'is_approved' => false, // Pending admin approval
                ]
            );
        } else {
            $reviewerName = $validated['reviewer_name'] ?? (auth()->check() ? auth()->user()->name : 'Guest');

            $review = ProductReview::create([
                'product_id' => $product->id,
                'patient_id' => null,
                'order_id' => null,
                'reviewer_name' => $reviewerName,
                'rating' => (int) $validated['rating'],
                'title' => $validated['title'] ?? null,
                'comment' => $validated['comment'],
                'is_verified_purchase' => false,
                'is_approved' => false, // Pending admin approval
            ]);
        }

        return redirect()
            ->route('ecommerce.products.show', $product->id)
            ->with('success', 'Review submitted successfully. It will be visible after admin approval.');
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

        // Resolve agent from session/cookie or logged in agent
        $refCode = session('ref_code') ?? request()->cookie('ref_code');
        $agent = null;
        if (auth()->check() && auth()->user()->role === 'agent') {
            $agent = auth()->user()->agent;
        } elseif ($refCode) {
            $agent = \Modules\Agents\Models\Agent::where('referral_code', $refCode)->where('status', 'active')->first();
        }

        $autoCoupon = null;
        $discount = 0;
        if ($agent) {
            $autoCoupon = Coupon::where('agent_id', $agent->id)->where('status', true)->first();
            if ($autoCoupon && $autoCoupon->isValid()) {
                if ($autoCoupon->type == 'fixed') {
                    $discount = $autoCoupon->amount;
                } else {
                    $discount = ($total * $autoCoupon->amount) / 100;
                }
            } else {
                $autoCoupon = null;
            }
        }

        $insideShippingCharge = (float) (\App\Models\SiteSetting::get('shipping_inside_dhaka', 80));
        $outsideShippingCharge = (float) (\App\Models\SiteSetting::get('shipping_outside_dhaka', 130));

        return view('ecommerce::frontend.product-checkout', compact('cart', 'total', 'autoCoupon', 'discount', 'insideShippingCharge', 'outsideShippingCharge'));
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'coupon_code' => 'nullable|string',
            'shipping_charge' => 'nullable|numeric|min:0',
        ]);

        if ($request->has('direct_order') && $request->filled('product_id')) {
            $productId = (int) $request->product_id;
            $variantId = $request->filled('variant_id') ? (int) $request->variant_id : null;
            $quantity = max(1, (int) ($request->quantity ?? 1));

            try {
                $inventory = $this->stockService->ensureRequestedQuantityIsAvailable($productId, $quantity, $variantId);
            } catch (ValidationException $exception) {
                return $this->handleInventoryException($request, $exception);
            }

            $product = $inventory['product'];
            $variant = $inventory['variant'];
            $cartKey = $this->buildCartKey($productId, $variantId);

            $cart = [
                $cartKey => [
                    'product_id' => $productId,
                    'variant_id' => $variant?->id,
                    'variant_label' => $variant?->display_label,
                    'name' => $product->name,
                    'price' => $variant ? $variant->currentPrice() : ($product->sale_price ?? $product->price),
                    'image' => $product->image,
                    'quantity' => $quantity,
                ]
            ];

            session()->put('cart', $cart);
        }

        $cart = $this->getNormalizedCart();

        if (empty($cart)) {
            return redirect()->route('ecommerce.products')->with('error', 'Your cart is empty!');
        }

        $total = $this->calculateCartTotal($cart);
        
        // Resolve patient (User might be guest, agent, or logged-in patient)
        $patient = null;
        if (auth()->check() && auth()->user()->role === 'patient') {
            $patient = auth()->user()->patient;
        } else {
            $customerUser = \App\Models\User::where('email', $request->email)->first();
            if (!$customerUser) {
                $customerUser = \App\Models\User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(12)),
                    'role' => 'patient',
                ]);
            }
            $patient = Patient::where('user_id', $customerUser->id)->first();
            if (!$patient) {
                $patient = Patient::create([
                    'user_id' => $customerUser->id,
                    'phone' => $request->phone,
                    'address' => $request->address,
                ]);
            }
        }

        // Coupon Logic
        $discount = 0;
        $couponCode = null;
        $coupon = null;
        if ($request->filled('coupon_code')) {
            $coupon = Coupon::where('code', $request->coupon_code)->first();
            if ($coupon && $coupon->isValid()) {
                if ($coupon->type == 'fixed') {
                    $discount = $coupon->amount;
                } else {
                    $discount = ($total * $coupon->amount) / 100;
                }
                $couponCode = $coupon->code;
            }
        }

        // Check for agent context (either coupon, direct order, or referral link)
        $refCode = session('ref_code') ?? request()->cookie('ref_code');
        $agent = null;
        if ($coupon && $coupon->agent_id) {
            $agent = $coupon->agent;
        } elseif (auth()->check() && auth()->user()->role === 'agent') {
            $agent = auth()->user()->agent;
        } elseif ($refCode) {
            $agent = \Modules\Agents\Models\Agent::where('referral_code', $refCode)->where('status', 'active')->first();
        }

        $shipping = $request->has('shipping_charge') ? (float) $request->shipping_charge : 0;
        $grandTotal = max(0, $total - $discount + $shipping);

        try {
            $order = DB::transaction(function () use ($cart, $request, $total, $discount, $couponCode, $grandTotal, $patient, $agent, $coupon, $shipping) {
                $this->stockService->reserveCart($cart);

                if ($coupon) {
                    $coupon->increment('used_count');
                }

                $order = Order::create([
                    'order_number' => 'ORD-' . strtoupper(uniqid()),
                    'patient_id' => $patient?->id,
                    'agent_id' => $agent?->id,
                    'customer_name' => $request->name,
                    'customer_email' => $request->email,
                    'customer_phone' => $request->phone,
                    'shipping_address' => $request->address,
                    'shipping_city' => $request->city ?? 'Dhaka',
                    'shipping_phone' => $request->phone,
                    'subtotal' => $total,
                    'discount' => $discount,
                    'coupon_code' => $couponCode,
                    'shipping' => $shipping,
                    'total' => $grandTotal,
                    'status' => 'pending',
                    'notes' => $request->notes,
                ]);

                foreach ($cart as $cartKey => $item) {
                    $productId = $this->resolveCartProductId($cartKey, $item);
                    $product = Product::find($productId);

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $productId,
                        'seller_id' => $product?->seller_id,
                        'product_variant_id' => $this->resolveCartVariantId($cartKey, $item),
                        'variant_label' => $item['variant_label'] ?? null,
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'total' => $item['price'] * $item['quantity'],
                    ]);
                }

                return $order;
            });

            // Create pending Agent Transaction if applicable
            if ($agent && $agent->can_sell_products) {
                $commissionBase = max(0, $total - $discount);
                $commission = $commissionBase * ($agent->product_commission_rate / 100);
                
                if ($commission > 0) {
                    \Modules\Agents\Models\AgentTransaction::create([
                        'agent_id' => $agent->id,
                        'type' => 'commission_product',
                        'amount' => $commission,
                        'description' => 'Commission of ৳' . number_format($commission, 2) . ' pending for Order #' . $order->order_number . ' (Customer: ' . $request->name . ')',
                        'reference_id' => $order->order_number,
                        'status' => 'pending',
                    ]);
                }
            }
        } catch (ValidationException $exception) {
            return $this->handleInventoryException($request, $exception);
        }

        session()->forget('cart');

        // Redirect back to agent dashboard if logged in as agent
        if (auth()->check() && auth()->user()->role === 'agent') {
            return redirect()->route('agent.dashboard')->with('success', 'Order #' . $order->order_number . ' placed successfully! Commission will be credited to your wallet upon delivery.');
        }

        return redirect()->route('ecommerce.order.success', ['order' => $order->id]);
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string'
        ]);

        $coupon = Coupon::where('code', $request->coupon_code)->first();

        if (!$coupon) {
            return response()->json(['success' => false, 'message' => 'Invalid coupon code.']);
        }

        if (!$coupon->isValid()) {
            return response()->json(['success' => false, 'message' => 'Coupon is expired or usage limit reached.']);
        }

        // Calculate discount
        $cart = $this->getNormalizedCart();
        $total = $this->calculateCartTotal($cart);

        $discount = 0;
        if ($coupon->type == 'fixed') {
            $discount = $coupon->amount;
        } else {
            $discount = ($total * $coupon->amount) / 100;
        }

        return response()->json([
            'success' => true,
            'message' => 'Coupon applied successfully!',
            'discount' => $discount,
            'code' => $coupon->code,
            'type' => $coupon->type,
            'amount' => $coupon->amount
        ]);
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

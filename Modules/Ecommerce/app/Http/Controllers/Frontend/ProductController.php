<?php

namespace Modules\Ecommerce\Http\Controllers\Frontend;

use App\Services\ProductStockService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
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

    protected function ensureCartHasAvailableStock(array $cart): void
    {
        foreach ($cart as $productId => $item) {
            $this->stockService->ensureRequestedQuantityIsAvailable(
                (int) $productId,
                (int) ($item['quantity'] ?? 0)
            );
        }
    }

    public function index(Request $request)
    {
        $query = Product::with('category')->where('is_active', true);

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
        $product = Product::with('category')->findOrFail($id);
        $relatedProducts = Product::where('product_category_id', $product->product_category_id)
            ->where('id', '!=', $id)
            ->where('is_active', true)
            ->limit(4)
            ->get();

        return view('ecommerce::frontend.products.show', compact('product', 'relatedProducts'));
    }

    public function filter(Request $request)
    {
        $query = Product::with('category')->where('is_active', true);

        if ($request->category && $request->category !== 'all') {
            $query->where('product_category_id', $request->category);
        }

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->take(8)->latest()->get();

        return response()->json($products);
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = session()->get('cart', []);
        $productId = (int) $request->product_id;
        $requestedQuantity = (int) $request->quantity;
        $newQuantity = ((int) ($cart[$productId]['quantity'] ?? 0)) + $requestedQuantity;

        try {
            $product = $this->stockService->ensureRequestedQuantityIsAvailable($productId, $newQuantity);
        } catch (ValidationException $exception) {
            return $this->handleInventoryException($request, $exception);
        }

        $cart[$productId] = [
            'name' => $product->name,
            'price' => $product->sale_price ?? $product->price,
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
        $cart = session()->get('cart', []);
        $total = $this->calculateCartTotal($cart);

        return view('ecommerce::frontend.cart', compact('cart', 'total'));
    }

    public function removeFromCart(Request $request)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$request->product_id])) {
            unset($cart[$request->product_id]);
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
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = session()->get('cart', []);
        $productId = (int) $request->product_id;

        if (!isset($cart[$productId])) {
            $message = 'This product is no longer in your cart.';

            if ($this->shouldReturnJson($request)) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 404);
            }

            return redirect()->route('ecommerce.cart')->with('error', $message);
        }

        try {
            $product = $this->stockService->ensureRequestedQuantityIsAvailable($productId, (int) $request->quantity);
        } catch (ValidationException $exception) {
            return $this->handleInventoryException($request, $exception);
        }

        $cart[$productId] = [
            'name' => $product->name,
            'price' => $product->sale_price ?? $product->price,
            'image' => $product->image,
            'quantity' => (int) $request->quantity,
        ];

        session()->put('cart', $cart);

        if ($this->shouldReturnJson($request)) {
            $itemSubtotal = isset($cart[$productId])
                ? ((float) $cart[$productId]['price']) * ((int) $cart[$productId]['quantity'])
                : 0;

            return response()->json([
                'success' => true,
                'message' => 'Cart updated!',
                'cartCount' => count($cart),
                'total' => $this->calculateCartTotal($cart),
                'itemSubtotal' => $itemSubtotal,
                'quantity' => (int) $cart[$productId]['quantity'],
            ]);
        }

        return redirect()->back()->with('success', 'Cart updated!');
    }

    public function checkout()
    {
        $cart = session()->get('cart', []);

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

        $cart = session()->get('cart', []);

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

                foreach ($cart as $productId => $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $productId,
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
        $order = Order::with('items.product')->findOrFail($request->order);

        return view('ecommerce::frontend.order-success', compact('order'));
    }
}

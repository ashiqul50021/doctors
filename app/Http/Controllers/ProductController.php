<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Patient;
use App\Models\Coupon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')->where('is_active', true);

        if ($request->has('category') && $request->category) {
            $query->where('product_category_id', $request->category);
        }

        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Price Filter
        if ($request->has('min_price') && $request->min_price != '') {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price') && $request->max_price != '') {
            $query->where('price', '<=', $request->max_price);
        }

        $products = $query->latest()->paginate(12);
        $categories = ProductCategory::orderBy('name')->get();

        return view('frontend.products.index', compact('products', 'categories'));
    }

    public function show($id)
    {
        $product = Product::with('category')->findOrFail($id);
        $relatedProducts = Product::where('product_category_id', $product->product_category_id)
            ->where('id', '!=', $id)
            ->where('is_active', true)
            ->limit(4)
            ->get();

        return view('frontend.products.show', compact('product', 'relatedProducts'));
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
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::findOrFail($request->product_id);
        $cart = session()->get('cart', []);

        if (isset($cart[$request->product_id])) {
            $cart[$request->product_id]['quantity'] += $request->quantity;
        } else {
            $cart[$request->product_id] = [
                'name' => $product->name,
                'price' => $product->sale_price ?? $product->price,
                'image' => $product->image,
                'quantity' => $request->quantity
            ];
        }

        session()->put('cart', $cart);

        if ($request->has('buy_now')) {
            return redirect()->route('product.checkout');
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Product added to cart!',
                'cartCount' => count($cart)
            ]);
        }

        return redirect()->back()->with('success', 'Product added to cart!');
    }

    public function cart()
    {
        $cart = session()->get('cart', []);
        $total = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return view('frontend.cart', compact('cart', 'total'));
    }

    public function removeFromCart(Request $request)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$request->product_id])) {
            unset($cart[$request->product_id]);
            session()->put('cart', $cart);
        }

        if ($request->ajax()) {
            $total = 0;
            foreach ($cart as $item) {
                $total += $item['price'] * $item['quantity'];
            }
            return response()->json([
                'success' => true,
                'message' => 'Product removed from cart!',
                'cartCount' => count($cart),
                'total' => $total
            ]);
        }

        return redirect()->back()->with('success', 'Product removed from cart!');
    }

    public function updateCart(Request $request)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$request->product_id])) {
            $cart[$request->product_id]['quantity'] = $request->quantity;
            session()->put('cart', $cart);
        }

        if ($request->ajax()) {
            $total = 0;
            foreach ($cart as $item) {
                $total += $item['price'] * $item['quantity'];
            }
            // Item subtotal
            $itemSubtotal = 0;
            if (isset($cart[$request->product_id])) {
                $itemSubtotal = $cart[$request->product_id]['price'] * $cart[$request->product_id]['quantity'];
            }

            return response()->json([
                'success' => true,
                'message' => 'Cart updated!',
                'cartCount' => count($cart),
                'total' => $total,
                'itemSubtotal' => $itemSubtotal
            ]);
        }

        return redirect()->back()->with('success', 'Cart updated!');
    }

    public function checkout()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('products')->with('error', 'Your cart is empty!');
        }

        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        $ecommerceSettings = SiteSetting::getByGroup('ecommerce');

        return view('frontend.product-checkout', compact('cart', 'total', 'ecommerceSettings'));
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('products')->with('error', 'Your cart is empty!');
        }

        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        // Coupon Logic
        $discount = 0;
        $couponCode = null;
        if ($request->coupon_code) {
            $coupon = Coupon::where('code', $request->coupon_code)->first();
            if ($coupon && $coupon->isValid()) {
                if ($coupon->type == 'fixed') {
                    $discount = $coupon->amount;
                } else {
                    $discount = ($total * $coupon->amount) / 100;
                }
                $couponCode = $coupon->code;

                // Increment usage
                $coupon->increment('used_count');
            }
        }

        $user = Auth::user();
        $patientId = null;

        if ($user) {
            // Logged in user
            if (!$user->patient) {
                Patient::create([
                    'user_id' => $user->id,
                    'phone' => $request->phone,
                    'address' => $request->address,
                ]);
                $user = $user->fresh('patient');
            }
            $patientId = $user->patient ? $user->patient->id : null;
        } else {
            // Guest or Login check (if email provided)
            if ($request->email) {
                $existingUser = User::where('email', $request->email)->first();
                if ($existingUser) {
                    // Cannot auto-login, but maybe link? For now, treat as Guest if not logged in.
                    // Or validation error: "Email exists, please login".
                    return redirect()->back()->with('error', 'Email already registered. Please login to continue.');
                }

                // Optional: Create User if desired, but requirement says "Email Optional", implies Guest Checkout.
                // We will NOT create user accounts for guest checkout to keep it simple as per "optional" request.
            }
        }

        // Calculate Shipping
        $ecommerceSettings = SiteSetting::getByGroup('ecommerce');
        $shippingCost = 0;
        $shippingMethod = $request->shipping_method ?? 'inside';

        if ($shippingMethod === 'inside') {
            $shippingCost = $ecommerceSettings['shipping_inside_dhaka'] ?? 60;
        } else {
            $shippingCost = $ecommerceSettings['shipping_outside_dhaka'] ?? 120;
        }

        // Final Calculation
        $grandTotal = max(0, ($total - $discount) + $shippingCost);

        // Create order
        $order = Order::create([
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'patient_id' => $patientId,
            'customer_name' => $request->name,
            'customer_email' => $request->email,
            'customer_phone' => $request->phone,
            'subtotal' => $total,
            'discount' => $discount,
            'coupon_code' => $couponCode,
            'shipping' => $shippingCost,
            'total' => $grandTotal,
            'status' => 'pending',
            'shipping_address' => $request->address,
            'shipping_city' => 'Dhaka', // Default or from form
            'shipping_phone' => $request->phone,
            'notes' => $request->notes,
        ]);

        foreach ($cart as $id => $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $id,
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total' => $item['quantity'] * $item['price'],
            ]);
        }

        session()->forget('cart');



        return redirect()->route('order.success', ['id' => $order->id])->with('success', 'Order placed successfully!');
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
        $cart = session()->get('cart', []);
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

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

    public function orderSuccess($id)
    {
        $order = Order::with('items.product')->findOrFail($id);

        return view('frontend.order-success', compact('order'));
    }

    public function invoice($id)
    {
        $order = Order::with('items.product')->findOrFail($id);
        $siteSettings = SiteSetting::pluck('value', 'key'); // Assuming SiteSetting model exists and has key-value pairs

        return view('frontend.order-invoice', compact('order', 'siteSettings'));
    }
}

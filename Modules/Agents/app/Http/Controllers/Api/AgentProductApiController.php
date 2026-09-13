<?php

namespace Modules\Agents\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Ecommerce\Models\Product;
use App\Models\Order;
use Modules\Agents\Models\AgentTransaction;

class AgentProductApiController extends Controller
{
    public function products(Request $request)
    {
        $query = Product::where('status', 'active');

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $products = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $agent = $request->user()->agent;
        $items = $request->items;

        $subtotal = 0;
        $orderItems = [];

        foreach ($items as $item) {
            $product = Product::findOrFail($item['product_id']);
            $price = $product->sale_price ?? $product->price;
            $total = $price * $item['quantity'];
            $subtotal += $total;

            $orderItems[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'price' => $price,
                'quantity' => $item['quantity'],
                'total' => $total,
            ];
        }

        $deliveryFee = 60.00; // Flat delivery fee
        $grandTotal = $subtotal + $deliveryFee;

        // Calculate Agent Commission (e.g. 5%)
        $commissionRate = (float) \App\Models\SiteSetting::get('agent_product_commission_percent', 5.00);
        $commissionAmount = ($subtotal * $commissionRate) / 100;

        $order = \Illuminate\Support\Facades\DB::transaction(function () use ($agent, $request, $subtotal, $deliveryFee, $grandTotal, $orderItems, $commissionAmount) {
            $o = Order::create([
                'agent_id' => $agent->id,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'shipping_address' => $request->shipping_address,
                'subtotal' => $subtotal,
                'shipping_cost' => $deliveryFee,
                'grand_total' => $grandTotal,
                'payment_method' => 'cod',
                'status' => 'pending',
            ]);

            // Add commission to agent's wallet
            $agent->increment('wallet_balance', $commissionAmount);

            AgentTransaction::create([
                'agent_id' => $agent->id,
                'type' => 'commission_product',
                'amount' => $commissionAmount,
                'description' => "Commission for order #{$o->id}",
                'status' => 'approved',
                'reference_id' => $o->id,
            ]);

            return $o;
        });

        return response()->json([
            'success' => true,
            'message' => 'Order placed successfully on behalf of customer.',
            'data' => [
                'order_id' => $order->id,
                'grand_total' => $grandTotal,
                'commission_earned' => $commissionAmount,
            ]
        ]);
    }

    public function orders(Request $request)
    {
        $agent = $request->user()->agent;

        $orders = Order::where('agent_id', $agent->id)
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }
}

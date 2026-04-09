<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\ProductStockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function __construct(protected ProductStockService $stockService)
    {
    }

    public function index()
    {
        $orders = Order::with('patient.user')
            ->withCount('items')
            ->latest()
            ->paginate(10);

        return view('admin.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with(['patient.user', 'items.product', 'items.variant'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => ['required', Rule::in(['pending', 'processing', 'shipped', 'delivered', 'cancelled'])],
        ]);

        $order = Order::with('items')->findOrFail($id);
        $previousStatus = $order->status;
        $nextStatus = $request->status;

        if ($previousStatus === $nextStatus) {
            return back()->with('success', 'Order status updated successfully!');
        }

        try {
            DB::transaction(function () use ($order, $previousStatus, $nextStatus) {
                if ($previousStatus !== 'cancelled' && $nextStatus === 'cancelled') {
                    $this->stockService->restoreOrderItems($order->items);
                }

                if ($previousStatus === 'cancelled' && $nextStatus !== 'cancelled') {
                    $this->stockService->reserveOrderItems($order->items);
                }

                $order->update([
                    'status' => $nextStatus,
                ]);
            });
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors());
        }

        if ($previousStatus !== 'cancelled' && $nextStatus === 'cancelled') {
            return back()->with('success', 'Order cancelled and stock restored successfully!');
        }

        if ($previousStatus === 'cancelled' && $nextStatus !== 'cancelled') {
            return back()->with('success', 'Order reopened and stock reserved successfully!');
        }

        return back()->with('success', 'Order status updated successfully!');
    }
}

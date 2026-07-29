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

                $this->handleAgentCommissionTransition($order, $previousStatus, $nextStatus);
                $this->handleSellerEarningsTransition($order, $previousStatus, $nextStatus);
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

    protected function handleAgentCommissionTransition($order, $previousStatus, $nextStatus)
    {
        $transaction = \Modules\Agents\Models\AgentTransaction::where('reference_id', $order->order_number)
            ->where('type', 'commission_product')
            ->first();

        if (!$transaction) {
            return;
        }

        $agent = $transaction->agent;
        if (!$agent) {
            return;
        }

        // 1. Moving to 'delivered'
        if ($nextStatus === 'delivered' && $previousStatus !== 'delivered') {
            if ($transaction->status === 'pending') {
                $agent->increment('wallet_balance', $transaction->amount);
                $transaction->update([
                    'status' => 'completed',
                    'description' => str_replace('pending', 'credited', $transaction->description),
                ]);
            } elseif ($transaction->status === 'rejected') {
                $agent->increment('wallet_balance', $transaction->amount);
                $transaction->update([
                    'status' => 'completed',
                    'description' => str_replace('cancelled', 'credited', $transaction->description),
                ]);
            }
        }

        // 2. Moving away from 'delivered' (reverting delivery)
        if ($previousStatus === 'delivered' && $nextStatus !== 'delivered') {
            if ($transaction->status === 'completed') {
                $agent->decrement('wallet_balance', $transaction->amount);
                
                if ($nextStatus === 'cancelled') {
                    $transaction->update([
                        'status' => 'rejected',
                        'description' => str_replace('credited', 'cancelled', $transaction->description),
                    ]);
                } else {
                    $transaction->update([
                        'status' => 'pending',
                        'description' => str_replace('credited', 'pending', $transaction->description),
                    ]);
                }
            }
        }

        // 3. Moving to 'cancelled' from a status other than 'delivered'
        if ($nextStatus === 'cancelled' && $previousStatus !== 'delivered') {
            if ($transaction->status === 'pending') {
                $transaction->update([
                    'status' => 'rejected',
                    'description' => str_replace('pending', 'cancelled', $transaction->description),
                ]);
            }
        }

        // 4. Moving away from 'cancelled' to a status other than 'delivered' (reopening order)
        if ($previousStatus === 'cancelled' && $nextStatus !== 'delivered' && $nextStatus !== 'cancelled') {
            if ($transaction->status === 'rejected') {
                $transaction->update([
                    'status' => 'pending',
                    'description' => str_replace('cancelled', 'pending', $transaction->description),
                ]);
            }
        }
    }

    protected function handleSellerEarningsTransition($order, $previousStatus, $nextStatus)
    {
        $order->loadMissing('items');

        // Group order items by seller_id
        $sellerItemsMap = [];
        foreach ($order->items as $item) {
            if (!empty($item->seller_id)) {
                if (!isset($sellerItemsMap[$item->seller_id])) {
                    $sellerItemsMap[$item->seller_id] = 0;
                }
                $sellerItemsMap[$item->seller_id] += ($item->price * $item->quantity);
            }
        }

        if (empty($sellerItemsMap)) {
            return;
        }

        // 1. Moving to 'delivered' -> Credit seller wallet
        if ($nextStatus === 'delivered' && $previousStatus !== 'delivered') {
            foreach ($sellerItemsMap as $sellerId => $grossAmount) {
                $sellerProfile = \Modules\Ecommerce\Models\SellerProfile::where('user_id', $sellerId)->first();
                if ($sellerProfile) {
                    $commissionRate = (float) ($sellerProfile->commission_rate ?? 0);
                    $netEarning = max(0, $grossAmount - ($grossAmount * ($commissionRate / 100)));
                    $sellerProfile->increment('wallet_balance', $netEarning);
                }
            }
        }

        // 2. Moving away from 'delivered' -> Debit seller wallet
        if ($previousStatus === 'delivered' && $nextStatus !== 'delivered') {
            foreach ($sellerItemsMap as $sellerId => $grossAmount) {
                $sellerProfile = \Modules\Ecommerce\Models\SellerProfile::where('user_id', $sellerId)->first();
                if ($sellerProfile) {
                    $commissionRate = (float) ($sellerProfile->commission_rate ?? 0);
                    $netEarning = max(0, $grossAmount - ($grossAmount * ($commissionRate / 100)));
                    $sellerProfile->decrement('wallet_balance', $netEarning);
                }
            }
        }
    }
}


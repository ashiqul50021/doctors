<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class ProductStockService
{
    public function ensureRequestedQuantityIsAvailable(
        int $productId,
        int $requestedQuantity,
        bool $requireActive = true,
        bool $lock = false
    ): Product {
        $query = Product::query();

        if ($lock) {
            $query->lockForUpdate();
        }

        $product = $query->find($productId);

        $this->assertProductCanFulfill($product, $requestedQuantity, $requireActive);

        return $product;
    }

    public function reserveCart(array $cart): void
    {
        foreach ($this->extractCartQuantities($cart) as $productId => $quantity) {
            $product = $this->ensureRequestedQuantityIsAvailable((int) $productId, $quantity, true, true);
            $product->decrement('stock', $quantity);
        }
    }

    public function reserveOrderItems(iterable $orderItems): void
    {
        foreach ($this->summarizeOrderItems($orderItems) as $productId => $quantity) {
            $product = $this->ensureRequestedQuantityIsAvailable((int) $productId, $quantity, false, true);
            $product->decrement('stock', $quantity);
        }
    }

    public function restoreOrderItems(iterable $orderItems): void
    {
        $quantities = $this->summarizeOrderItems($orderItems);

        if ($quantities->isEmpty()) {
            return;
        }

        $products = Product::query()
            ->whereIn('id', $quantities->keys()->all())
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        foreach ($quantities as $productId => $quantity) {
            $product = $products->get((int) $productId);

            if (!$product) {
                continue;
            }

            $product->increment('stock', $quantity);
        }
    }

    protected function extractCartQuantities(array $cart): Collection
    {
        return collect($cart)
            ->mapWithKeys(function ($item, $productId) {
                return [(int) $productId => (int) ($item['quantity'] ?? 0)];
            })
            ->filter(fn (int $quantity) => $quantity > 0)
            ->sortKeys();
    }

    protected function summarizeOrderItems(iterable $orderItems): Collection
    {
        return collect($orderItems)
            ->groupBy('product_id')
            ->map(fn (Collection $items) => (int) $items->sum('quantity'))
            ->filter(fn (int $quantity) => $quantity > 0)
            ->sortKeys();
    }

    protected function assertProductCanFulfill(?Product $product, int $requestedQuantity, bool $requireActive): void
    {
        if ($requestedQuantity < 1) {
            throw ValidationException::withMessages([
                'quantity' => 'Quantity must be at least 1.',
            ]);
        }

        if (!$product) {
            throw ValidationException::withMessages([
                'stock' => 'This product is no longer available.',
            ]);
        }

        if ($requireActive && !$product->is_active) {
            throw ValidationException::withMessages([
                'stock' => "{$product->name} is currently unavailable for ordering.",
            ]);
        }

        if ($product->stock < 1) {
            throw ValidationException::withMessages([
                'stock' => "{$product->name} is out of stock.",
            ]);
        }

        if ($requestedQuantity > $product->stock) {
            throw ValidationException::withMessages([
                'stock' => "Only {$product->stock} unit(s) of {$product->name} available right now.",
            ]);
        }
    }
}

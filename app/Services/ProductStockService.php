<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Validation\ValidationException;

class ProductStockService
{
    public function ensureRequestedQuantityIsAvailable(
        int $productId,
        int $requestedQuantity,
        ?int $variantId = null,
        bool $requireActive = true,
        bool $lock = false
    ): array {
        $productQuery = Product::query();

        if ($lock) {
            $productQuery->lockForUpdate();
        }

        $product = $productQuery->find($productId);

        $this->assertRequestedQuantity($requestedQuantity);
        $this->assertProductCanBeOrdered($product, $requireActive);

        if ($variantId) {
            $variantQuery = ProductVariant::query()
                ->where('product_id', $productId);

            if ($lock) {
                $variantQuery->lockForUpdate();
            }

            $variant = $variantQuery->whereKey($variantId)->first();

            $this->assertVariantCanFulfill($variant, $requestedQuantity, $requireActive);

            return [
                'product' => $product,
                'variant' => $variant,
            ];
        }

        if ($this->productHasActiveVariants($productId)) {
            throw ValidationException::withMessages([
                'variant_id' => "Please select a variant for {$product->name}.",
            ]);
        }

        $this->assertAvailableStock($product->name, (int) $product->stock, $requestedQuantity);

        return [
            'product' => $product,
            'variant' => null,
        ];
    }

    public function reserveCart(array $cart): void
    {
        $productsToSync = [];

        foreach ($cart as $item) {
            $productId = (int) ($item['product_id'] ?? 0);
            $variantId = isset($item['variant_id']) ? (int) $item['variant_id'] : null;
            $quantity = (int) ($item['quantity'] ?? 0);

            $inventory = $this->ensureRequestedQuantityIsAvailable($productId, $quantity, $variantId, true, true);

            if ($inventory['variant']) {
                $inventory['variant']->decrement('stock', $quantity);
                $productsToSync[] = $productId;
                continue;
            }

            $inventory['product']->decrement('stock', $quantity);
        }

        foreach (array_unique($productsToSync) as $productId) {
            $this->syncAggregateStock((int) $productId);
        }
    }

    public function reserveOrderItems(iterable $orderItems): void
    {
        $productsToSync = [];

        foreach ($orderItems as $orderItem) {
            $productId = (int) $orderItem->product_id;
            $variantId = $orderItem->product_variant_id ? (int) $orderItem->product_variant_id : null;
            $quantity = (int) $orderItem->quantity;

            $inventory = $this->ensureRequestedQuantityIsAvailable($productId, $quantity, $variantId, false, true);

            if ($inventory['variant']) {
                $inventory['variant']->decrement('stock', $quantity);
                $productsToSync[] = $productId;
                continue;
            }

            $inventory['product']->decrement('stock', $quantity);
        }

        foreach (array_unique($productsToSync) as $productId) {
            $this->syncAggregateStock((int) $productId);
        }
    }

    public function restoreOrderItems(iterable $orderItems): void
    {
        $productsToSync = [];

        foreach ($orderItems as $orderItem) {
            $quantity = (int) $orderItem->quantity;

            if ($orderItem->product_variant_id) {
                $variant = ProductVariant::query()
                    ->lockForUpdate()
                    ->whereKey((int) $orderItem->product_variant_id)
                    ->first();

                if ($variant) {
                    $variant->increment('stock', $quantity);
                    $productsToSync[] = (int) $orderItem->product_id;
                }

                continue;
            }

            $product = Product::query()
                ->lockForUpdate()
                ->find((int) $orderItem->product_id);

            if ($product) {
                $product->increment('stock', $quantity);
            }
        }

        foreach (array_unique($productsToSync) as $productId) {
            $this->syncAggregateStock((int) $productId);
        }
    }

    public function syncAggregateStock(int $productId): void
    {
        $product = Product::query()
            ->lockForUpdate()
            ->find($productId);

        if (! $product) {
            return;
        }

        $aggregateStock = (int) ProductVariant::query()
            ->where('product_id', $productId)
            ->where('is_active', true)
            ->sum('stock');

        $product->forceFill([
            'stock' => $aggregateStock,
        ])->saveQuietly();
    }

    protected function productHasActiveVariants(int $productId): bool
    {
        return ProductVariant::query()
            ->where('product_id', $productId)
            ->where('is_active', true)
            ->exists();
    }

    protected function assertRequestedQuantity(int $requestedQuantity): void
    {
        if ($requestedQuantity < 1) {
            throw ValidationException::withMessages([
                'quantity' => 'Quantity must be at least 1.',
            ]);
        }
    }

    protected function assertProductCanBeOrdered(?Product $product, bool $requireActive): void
    {
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
    }

    protected function assertVariantCanFulfill(?ProductVariant $variant, int $requestedQuantity, bool $requireActive): void
    {
        if (! $variant) {
            throw ValidationException::withMessages([
                'variant_id' => 'The selected variant is no longer available.',
            ]);
        }

        if ($requireActive && ! $variant->is_active) {
            throw ValidationException::withMessages([
                'variant_id' => "{$variant->display_label} is currently unavailable.",
            ]);
        }

        $this->assertAvailableStock($variant->display_label, (int) $variant->stock, $requestedQuantity);
    }

    protected function assertAvailableStock(string $name, int $availableStock, int $requestedQuantity): void
    {
        if ($availableStock < 1) {
            throw ValidationException::withMessages([
                'stock' => "{$name} is out of stock.",
            ]);
        }

        if ($requestedQuantity > $availableStock) {
            throw ValidationException::withMessages([
                'stock' => "Only {$availableStock} unit(s) of {$name} available right now.",
            ]);
        }
    }
}

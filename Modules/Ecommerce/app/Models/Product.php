<?php

namespace Modules\Ecommerce\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'gallery' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class)->orderBy('sort_order')->orderBy('id');
    }

    public function activeVariantItems(): Collection
    {
        if ($this->relationLoaded('variants')) {
            return $this->variants
                ->where('is_active', true)
                ->values();
        }

        return $this->variants()
            ->where('is_active', true)
            ->get();
    }

    public function hasActiveVariants(): bool
    {
        return $this->activeVariantItems()->isNotEmpty();
    }

    public function availableStock(): int
    {
        if ($this->hasActiveVariants()) {
            return (int) $this->activeVariantItems()->sum('stock');
        }

        return (int) $this->stock;
    }

    public function effectivePrice(): float
    {
        if ($this->hasActiveVariants()) {
            return (float) $this->activeVariantItems()
                ->map(fn (ProductVariant $variant) => $variant->currentPrice())
                ->min();
        }

        return (float) ($this->sale_price ?? $this->price);
    }

    public function effectiveRegularPrice(): float
    {
        if ($this->hasActiveVariants()) {
            return (float) $this->activeVariantItems()
                ->map(fn (ProductVariant $variant) => $variant->regularPrice())
                ->min();
        }

        return (float) $this->price;
    }

    public function usesPriceRange(): bool
    {
        if (! $this->hasActiveVariants()) {
            return false;
        }

        return $this->activeVariantItems()
            ->map(fn (ProductVariant $variant) => $variant->currentPrice())
            ->unique()
            ->count() > 1;
    }

    public function getDiscountPercentageAttribute()
    {
        if ($this->hasActiveVariants()) {
            return (int) $this->activeVariantItems()
                ->map(function (ProductVariant $variant) {
                    $regularPrice = $variant->regularPrice();
                    $currentPrice = $variant->currentPrice();

                    if ($regularPrice > 0 && $currentPrice < $regularPrice) {
                        return round((($regularPrice - $currentPrice) / $regularPrice) * 100);
                    }

                    return 0;
                })
                ->max();
        }

        $regularPrice = $this->effectiveRegularPrice();
        $currentPrice = $this->effectivePrice();

        if ($regularPrice > 0 && $currentPrice < $regularPrice) {
            return round((($regularPrice - $currentPrice) / $regularPrice) * 100);
        }

        return 0;
    }
}

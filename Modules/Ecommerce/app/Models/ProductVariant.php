<?php

namespace Modules\Ecommerce\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_variant_id');
    }

    public function currentPrice(): float
    {
        return (float) ($this->sale_price ?? $this->price);
    }

    public function regularPrice(): float
    {
        return (float) $this->price;
    }

    public function getDisplayLabelAttribute(): string
    {
        if ($this->option_name) {
            return "{$this->option_name}: {$this->option_value}";
        }

        return (string) $this->option_value;
    }
}

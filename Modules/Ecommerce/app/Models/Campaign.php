<?php

namespace Modules\Ecommerce\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'show_on_homepage' => 'boolean',
        'discount_value' => 'float',
    ];

    public function campaignProducts()
    {
        return $this->hasMany(CampaignProduct::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'campaign_products')
            ->withPivot('campaign_price')
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    public function isRunning(): bool
    {
        return $this->is_active && now()->between($this->start_date, $this->end_date);
    }

    public function isUpcoming(): bool
    {
        return $this->is_active && now()->lt($this->start_date);
    }

    public function isExpired(): bool
    {
        return now()->gt($this->end_date);
    }

    public function remainingSeconds(): int
    {
        if (!$this->isRunning()) {
            return 0;
        }

        return max(0, now()->diffInSeconds($this->end_date, false));
    }

    public function calculateCampaignPrice(float $regularPrice, ?float $customPrice = null): float
    {
        if ($customPrice !== null && $customPrice > 0) {
            return round($customPrice, 2);
        }

        if ($this->discount_type === 'percentage') {
            $discount = ($regularPrice * $this->discount_value) / 100;
            return max(0, round($regularPrice - $discount, 2));
        }

        if ($this->discount_type === 'fixed') {
            return max(0, round($regularPrice - $this->discount_value, 2));
        }

        return round($regularPrice, 2);
    }
}

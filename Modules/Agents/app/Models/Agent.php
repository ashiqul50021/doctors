<?php

namespace Modules\Agents\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Appointment;
use App\Models\Order;
use Modules\Courses\Models\Enrollment;

class Agent extends Model
{
    protected $fillable = [
        'user_id',
        'phone',
        'slug',
        'profile_image',
        'referral_code',
        'can_book_appointments',
        'can_sell_products',
        'can_sell_courses',
        'booking_commission_rate',
        'product_commission_rate',
        'course_commission_rate',
        'wallet_balance',
        'status',
    ];

    protected $casts = [
        'can_book_appointments' => 'boolean',
        'can_sell_products' => 'boolean',
        'can_sell_courses' => 'boolean',
        'booking_commission_rate' => 'decimal:2',
        'product_commission_rate' => 'decimal:2',
        'course_commission_rate' => 'decimal:2',
        'wallet_balance' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(AgentTransaction::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function coupons()
    {
        return $this->hasMany(\App\Models\Coupon::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    protected static function booted()
    {
        static::creating(function ($agent) {
            if (empty($agent->slug)) {
                $user = User::find($agent->user_id);
                $name = $user ? $user->name : 'agent';
                $slug = \Illuminate\Support\Str::slug($name);
                if (empty($slug)) {
                    $slug = 'agent';
                }
                $originalSlug = $slug;
                $count = 2;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $count;
                    $count++;
                }
                $agent->slug = $slug;
            }
        });

        static::saved(function ($agent) {
            if ($agent->status === 'active') {
                // Check if coupon already exists for this agent
                $couponExists = \App\Models\Coupon::where('agent_id', $agent->id)->exists();
                
                if (!$couponExists) {
                    \App\Models\Coupon::create([
                        'code' => $agent->referral_code,
                        'type' => 'percent',
                        'amount' => 5.00, // Default 5% discount
                        'status' => true,
                        'agent_id' => $agent->id,
                        'usage_limit' => null,
                        'used_count' => 0,
                    ]);
                } else {
                    // Make sure the coupon code matches the referral code and status is active
                    $coupon = \App\Models\Coupon::where('agent_id', $agent->id)->first();
                    if ($coupon) {
                        $coupon->update([
                            'code' => $agent->referral_code,
                            'status' => true,
                        ]);
                    }
                }
            } elseif (in_array($agent->status, ['pending', 'suspended'])) {
                // If suspended or pending, deactivate the coupon
                $coupon = \App\Models\Coupon::where('agent_id', $agent->id)->first();
                if ($coupon) {
                    $coupon->update([
                        'status' => false,
                    ]);
                }
            }
        });
    }
}

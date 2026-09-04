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
                // If coupon exists for this agent, ensure it is active
                \App\Models\Coupon::where('agent_id', $agent->id)->update([
                    'status' => true,
                ]);
            } elseif (in_array($agent->status, ['pending', 'suspended'])) {
                // If suspended or pending, deactivate the coupon
                \App\Models\Coupon::where('agent_id', $agent->id)->update([
                    'status' => false,
                ]);
            }
        });
    }
}

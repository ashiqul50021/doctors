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

    /**
     * Scope to only include active agents.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}

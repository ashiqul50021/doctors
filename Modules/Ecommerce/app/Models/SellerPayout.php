<?php

namespace Modules\Ecommerce\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerPayout extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'amount',
        'payment_method',
        'account_details',
        'status',
        'admin_note',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function sellerProfile()
    {
        return $this->hasOne(SellerProfile::class, 'user_id', 'seller_id');
    }
}

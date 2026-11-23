<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierProfile extends Model
{
    protected $fillable = [
        'user_id', 'company_name', 'phone', 'address',
        'trade_license_no', 'products_description', 'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

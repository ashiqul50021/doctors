<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'bn_name'];

    /**
     * Get the areas in this district
     */
    public function areas()
    {
        return $this->hasMany(Area::class);
    }

    /**
     * Get doctors in this district
     */
    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }
}

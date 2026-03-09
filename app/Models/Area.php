<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    protected $fillable = ['district_id', 'name', 'bn_name'];

    /**
     * Get the district this area belongs to
     */
    public function district()
    {
        return $this->belongsTo(District::class);
    }

    /**
     * Get doctors in this area
     */
    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }
}

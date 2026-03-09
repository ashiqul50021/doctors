<?php

namespace Modules\Courses\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_free' => 'boolean',
        'duration_minutes' => 'integer',
        'order' => 'integer',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}

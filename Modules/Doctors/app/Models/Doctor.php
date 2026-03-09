<?php

namespace Modules\Doctors\Models;

use App\Models\User;
use App\Models\District;
use App\Models\Area;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function speciality()
    {
        return $this->belongsTo(Speciality::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getAverageRatingAttribute()
    {
        return $this->reviews()->where('is_approved', true)->avg('rating') ?? 0;
    }

    public function getReviewCountAttribute()
    {
        return $this->reviews()->where('is_approved', true)->count();
    }

    public function getClinicNameAttribute($value): ?string
    {
        $names = $this->parseStoredList($value);
        return $names[0] ?? null;
    }

    public function getClinicAddressAttribute($value): ?string
    {
        $addresses = $this->parseStoredList($value);
        return $addresses[0] ?? null;
    }

    private function parseStoredList(?string $rawValue): array
    {
        if (blank($rawValue)) {
            return [];
        }

        $decoded = json_decode($rawValue, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return array_values(array_filter(array_map(function ($item) {
                return is_string($item) ? trim($item) : '';
            }, $decoded)));
        }

        return [trim((string) $rawValue)];
    }
}

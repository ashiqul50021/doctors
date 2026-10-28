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

    protected $casts = [
        'custom_sections' => 'array',
    ];

    public function getCustomSectionsAttribute($value): array
    {
        if (empty($value)) {
            return [];
        }
        if (is_array($value)) {
            return $value;
        }
        $decoded = json_decode($value, true);
        return (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : [];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($doctor) {
            if (empty($doctor->slug)) {
                $user = User::find($doctor->user_id);
                $name = $user ? $user->name : 'doctor';
                $slug = \Illuminate\Support\Str::slug($name);
                if (empty($slug)) {
                    $slug = 'doctor';
                }
                $originalSlug = $slug;
                $count = 2;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $count;
                    $count++;
                }
                $doctor->slug = $slug;
            }
        });
    }

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

    public function getClinicNamesAttribute(): array
    {
        return $this->parseStoredList($this->attributes['clinic_name'] ?? null);
    }

    public function getClinicAddressesAttribute(): array
    {
        return $this->parseStoredList($this->attributes['clinic_address'] ?? null);
    }

    public function getClinicFeesAttribute(): array
    {
        return $this->parseStoredList($this->attributes['clinic_fees'] ?? null);
    }

    public function getClinicLocationsAttribute(): array
    {
        $names = $this->clinic_names;
        $addresses = $this->clinic_addresses;
        $fees = $this->clinic_fees;
        $count = max(count($names), count($addresses), count($fees));
        $locations = [];

        for ($i = 0; $i < $count; $i++) {
            $name = $names[$i] ?? null;
            $address = $addresses[$i] ?? null;
            $fee = $fees[$i] ?? null;

            if (blank($name) && blank($address)) {
                continue;
            }

            $effectiveFee = (!empty($fee) && is_numeric($fee) && (float)$fee > 0)
                ? (float)$fee
                : (float)($this->consultation_fee ?? 0);

            $locations[] = [
                'name' => $name ?: 'Main Clinic',
                'address' => $address,
                'fee' => $effectiveFee,
                'custom_fee' => (!empty($fee) && is_numeric($fee) && (float)$fee > 0) ? (float)$fee : null,
            ];
        }

        return $locations;
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

<?php

namespace App\Models;

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

    public function offDays()
    {
        return $this->hasMany(DoctorOffDay::class);
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

    public function getPrimaryClinicNameAttribute(): ?string
    {
        return $this->clinic_names[0] ?? null;
    }

    public function getPrimaryClinicAddressAttribute(): ?string
    {
        return $this->clinic_addresses[0] ?? null;
    }

    public function getClinicLocationsAttribute(): array
    {
        $names = $this->clinic_names;
        $addresses = $this->clinic_addresses;
        $count = max(count($names), count($addresses));
        $locations = [];

        for ($i = 0; $i < $count; $i++) {
            $name = $names[$i] ?? null;
            $address = $addresses[$i] ?? null;

            if (blank($name) && blank($address)) {
                continue;
            }

            $locations[] = [
                'name' => $name ?: 'Main Clinic',
                'address' => $address,
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

    public function isProfileComplete(): bool
    {
        $requiredFields = [
            'phone',
            'gender',
            'date_of_birth',
            'speciality_id',
            'qualification',
            'registration_number',
            'registration_date',
            'bio',
            'clinic_name',
            'clinic_address',
            'district_id',
            'area_id',
        ];

        foreach ($requiredFields as $field) {
            if (blank($this->{$field})) {
                return false;
            }
        }

        return true;
    }
}

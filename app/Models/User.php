<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_seen_at' => 'datetime',
        ];
    }

    public function isOnline(int $minutes = 2): bool
    {
        if (!$this->last_seen_at) {
            return false;
        }

        return $this->last_seen_at->gt(now()->subMinutes($minutes));
    }

    public function patient()
    {
        return $this->hasOne(Patient::class);
    }

    public function doctor()
    {
        return $this->hasOne(Doctor::class);
    }

    public function agent()
    {
        return $this->hasOne(\Modules\Agents\Models\Agent::class);
    }

    public function isAgent(): bool
    {
        return $this->role === 'agent';
    }

    public function getProfileImageUrlAttribute(): string
    {
        if ($this->role === 'doctor') {
            if ($this->doctor && $this->doctor->profile_image) {
                return asset($this->doctor->profile_image);
            }
            return asset('assets/img/doctors/doctor-thumb-02.jpg');
        }
        if ($this->role === 'agent') {
            if ($this->agent && $this->agent->profile_image) {
                return asset($this->agent->profile_image);
            }
            return asset('assets/img/patients/patient.jpg');
        }
        if ($this->role === 'patient') {
            if ($this->patient && $this->patient->profile_image) {
                return asset($this->patient->profile_image);
            }
            return asset('assets/img/patients/patient.jpg');
        }
        
        return asset('assets/img/patients/patient.jpg');
    }
}

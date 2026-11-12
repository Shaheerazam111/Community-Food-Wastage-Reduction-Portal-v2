<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'business_name',
        'phone',
        'ngo_name',
        'contact_person',
        'address',
        'latitude',
        'longitude',
        'is_active',
        'verification_status',
    ];

    public function verificationDocuments()
    {
        return $this->hasMany(VerificationDocument::class);
    }

    public function isNgoApproved(): bool
    {
        return $this->role !== 'recipient' || $this->verification_status === 'approved';
    }

    // First page after login for this role
    public static function homePathForRole(string $role): string
    {
        return match ($role) {
            'admin' => '/admin/dashboard',
            'volunteer' => '/volunteer/tasks',
            'donor' => '/donor/dashboard',
            default => '/recipient/food',
        };
    }

    public function homePath(): string
    {
        return self::homePathForRole($this->role);
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected $attributes = [
        'is_active' => true,
    ];

    public function foodListings()
    {
        return $this->hasMany(FoodListing::class, 'donor_id');
    }
}

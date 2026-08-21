<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodListing extends Model
{
    use HasFactory;

    protected $fillable = [
        'donor_id',
        'title',
        'category',
        'quantity',
        'pickup_address',
        'latitude',
        'longitude',
        'expiry_at',
        'status',
        'claimed_by',
        'claimed_at',
        'delivered_at',
    ];

    protected $casts = [
        'expiry_at' => 'datetime',
        'claimed_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function donor()
    {
        return $this->belongsTo(User::class, 'donor_id');
    }

    public function claimedBy()
    {
        return $this->belongsTo(User::class, 'claimed_by');
    }
}

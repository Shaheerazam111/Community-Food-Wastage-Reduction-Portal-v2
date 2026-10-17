<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    /**
     * Find AVAILABLE listings near a point (same query as GET /food/search).
     */
    public static function searchAvailableNearby(float $latitude, float $longitude, float $radiusKm = 5)
    {
        $distanceExpr = '6371 * ACOS(
            COS(RADIANS(?)) * COS(RADIANS(latitude)) * COS(RADIANS(longitude) - RADIANS(?))
            + SIN(RADIANS(?)) * SIN(RADIANS(latitude))
        )';

        return DB::table('food_listings')
            ->selectRaw('*, '.$distanceExpr.' AS distance_km', [
                $latitude, $longitude, $latitude,
            ])
            ->where('status', 'AVAILABLE')
            ->where('expiry_at', '>', now())
            ->havingRaw('distance_km <= ?', [$radiusKm])
            ->orderBy('distance_km')
            ->get();
    }
}

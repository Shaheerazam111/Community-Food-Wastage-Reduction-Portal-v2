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
        'photo_path',
        'pickup_address',
        'city',
        'latitude',
        'longitude',
        'expiry_at',
        'status',
        'moderation_status',
        'rejection_reason',
        'moderated_by',
        'moderated_at',
        'claimed_by',
        'claimed_at',
        'delivered_at',
    ];

    protected $casts = [
        'expiry_at' => 'datetime',
        'claimed_at' => 'datetime',
        'delivered_at' => 'datetime',
        'moderated_at' => 'datetime',
    ];

    public function donor()
    {
        return $this->belongsTo(User::class, 'donor_id');
    }

    public function claimedBy()
    {
        return $this->belongsTo(User::class, 'claimed_by');
    }

    public function feedbacks()
    {
        return $this->hasMany(ListingFeedback::class, 'food_listing_id');
    }

    /**
     * Find AVAILABLE listings near a point (same query as GET /food/search).
     */
    public static function searchAvailableNearby(float $latitude, float $longitude, float $radiusKm = 5)
    {
        if (DB::getDriverName() === 'sqlite') {
            return DB::table('food_listings')
                ->where('status', 'AVAILABLE')
                ->where('moderation_status', 'APPROVED')
                ->where('expiry_at', '>', now())
                ->get()
                ->map(function ($row) use ($latitude, $longitude) {
                    $row->distance_km = self::distanceKm(
                        $latitude,
                        $longitude,
                        (float) $row->latitude,
                        (float) $row->longitude
                    );

                    return $row;
                })
                ->filter(fn ($row) => $row->distance_km <= $radiusKm)
                ->sortBy('distance_km')
                ->values();
        }

        $distanceExpr = '6371 * ACOS(
            COS(RADIANS(?)) * COS(RADIANS(latitude)) * COS(RADIANS(longitude) - RADIANS(?))
            + SIN(RADIANS(?)) * SIN(RADIANS(latitude))
        )';

        return DB::table('food_listings')
            ->selectRaw('*, '.$distanceExpr.' AS distance_km', [
                $latitude, $longitude, $latitude,
            ])
            ->where('status', 'AVAILABLE')
            ->where('moderation_status', 'APPROVED')
            ->where('expiry_at', '>', now())
            ->havingRaw('distance_km <= ?', [$radiusKm])
            ->orderBy('distance_km')
            ->get();
    }

    // Add km distance when we have a search origin
    public static function attachDistanceKm($listings, ?float $originLat, ?float $originLng)
    {
        if ($originLat === null || $originLng === null) {
            return $listings;
        }

        return collect($listings)->map(function ($row) use ($originLat, $originLng) {
            if ($row->latitude !== null && $row->longitude !== null) {
                $row->distance_km = self::distanceKm(
                    $originLat,
                    $originLng,
                    (float) $row->latitude,
                    (float) $row->longitude
                );
            }

            return $row;
        });
    }

    // PENDING listings in radius (not shown in search yet)
    public static function countPendingModerationNearby(float $latitude, float $longitude, float $radiusKm = 5): int
    {
        if (DB::getDriverName() === 'sqlite') {
            return DB::table('food_listings')
                ->where('status', 'AVAILABLE')
                ->where('moderation_status', 'PENDING')
                ->where('expiry_at', '>', now())
                ->get()
                ->filter(function ($row) use ($latitude, $longitude, $radiusKm) {
                    return self::distanceKm(
                        $latitude,
                        $longitude,
                        (float) $row->latitude,
                        (float) $row->longitude
                    ) <= $radiusKm;
                })
                ->count();
        }

        $distanceExpr = '6371 * ACOS(
            COS(RADIANS(?)) * COS(RADIANS(latitude)) * COS(RADIANS(longitude) - RADIANS(?))
            + SIN(RADIANS(?)) * SIN(RADIANS(latitude))
        )';

        return (int) DB::table('food_listings')
            ->selectRaw('id, '.$distanceExpr.' AS distance_km', [
                $latitude, $longitude, $latitude,
            ])
            ->where('status', 'AVAILABLE')
            ->where('moderation_status', 'PENDING')
            ->where('expiry_at', '>', now())
            ->havingRaw('distance_km <= ?', [$radiusKm])
            ->count();
    }

    // AVAILABLE listings in a city (address/city text match)
    public static function searchAvailableByCity(string $city)
    {
        $term = '%'.trim($city).'%';

        return DB::table('food_listings')
            ->where('status', 'AVAILABLE')
            ->where('moderation_status', 'APPROVED')
            ->where('expiry_at', '>', now())
            ->where(function ($q) use ($term) {
                $q->where('city', 'like', $term)
                    ->orWhere('pickup_address', 'like', $term);
            })
            ->orderByDesc('created_at')
            ->get();
    }

    // Haversine distance in km
    public static function distanceKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadiusKm = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return $earthRadiusKm * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}

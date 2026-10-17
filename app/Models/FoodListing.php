<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FoodListing extends Model
{
    use HasFactory;

    protected $fillable = [
        'donor_id',
        'category_id',
        'title',
        'description',
        'quantity_value',
        'quantity_unit',
        'photo_path',
        'pickup_address',
        'city',
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
        'quantity_value' => 'float',
    ];

    public function donor()
    {
        return $this->belongsTo(User::class, 'donor_id');
    }

    public function claimedBy()
    {
        return $this->belongsTo(User::class, 'claimed_by');
    }

    public function foodCategory()
    {
        return $this->belongsTo(FoodCategory::class, 'category_id');
    }

    protected function quantityDisplay(): Attribute
    {
        return Attribute::get(function () {
            if ($this->quantity_value === null) {
                return '—';
            }

            $unit = $this->quantity_unit === 'kg' ? 'kg' : 'servings';

            return rtrim(rtrim(number_format((float) $this->quantity_value, 2, '.', ''), '0'), '.').' '.$unit;
        });
    }

    /**
     * Find AVAILABLE listings near a point (same query as GET /food/search).
     */
    public static function searchAvailableNearby(float $latitude, float $longitude, float $radiusKm = 5): Collection
    {
        $distanceExpr = '6371 * ACOS(
            COS(RADIANS(?)) * COS(RADIANS(latitude)) * COS(RADIANS(longitude) - RADIANS(?))
            + SIN(RADIANS(?)) * SIN(RADIANS(latitude))
        )';

        return DB::table('food_listings')
            ->selectRaw('food_listings.*, '.$distanceExpr.' AS distance_km', [
                $latitude, $longitude, $latitude,
            ])
            ->where('food_listings.status', 'AVAILABLE')
            ->where('food_listings.expiry_at', '>', now())
            ->havingRaw('distance_km <= ?', [$radiusKm])
            ->orderBy('distance_km')
            ->get();
    }

    /**
     * List AVAILABLE food in one city.
     */
    public static function searchAvailableByCity(string $city, ?float $latitude = null, ?float $longitude = null): Collection
    {
        $cityNorm = strtolower(trim($city));

        if ($latitude !== null && $longitude !== null) {
            return static::searchAvailableNearby($latitude, $longitude, 500)
                ->filter(function ($listing) use ($cityNorm) {
                    return strtolower(trim((string) $listing->city)) === $cityNorm;
                })
                ->values();
        }

        return DB::table('food_listings')
            ->where('status', 'AVAILABLE')
            ->where('expiry_at', '>', now())
            ->whereRaw('LOWER(TRIM(city)) = ?', [$cityNorm])
            ->orderBy('expiry_at')
            ->get();
    }

    /**
     * Cities that have food still available.
     */
    public static function citiesWithAvailableFood(): array
    {
        return DB::table('food_listings')
            ->where('status', 'AVAILABLE')
            ->where('expiry_at', '>', now())
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->distinct()
            ->orderBy('city')
            ->pluck('city')
            ->all();
    }
}

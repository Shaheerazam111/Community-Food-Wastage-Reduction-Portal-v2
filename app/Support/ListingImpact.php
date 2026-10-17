<?php

namespace App\Support;

use App\Models\FoodListing;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ListingImpact
{
    /** Meals + kg saved from DELIVERED listings (optional donor scope). */
    public static function totals(?int $donorId = null): array
    {
        $base = FoodListing::query()->where('status', 'DELIVERED');
        if ($donorId !== null) {
            $base->where('donor_id', $donorId);
        }

        $meals = (float) (clone $base)->where('quantity_unit', 'servings')->sum('quantity_value');
        $kg = (float) (clone $base)->where('quantity_unit', 'kg')->sum('quantity_value');
        $delivered = (int) (clone $base)->count();

        return [
            'delivered_count' => $delivered,
            'meals_saved' => $meals,
            'kg_saved' => $kg,
        ];
    }

    /** Per-city listing and impact breakdown. */
    public static function byCity(): Collection
    {
        $rows = DB::table('food_listings')
            ->selectRaw("
                COALESCE(NULLIF(TRIM(city), ''), 'Unknown') AS city_name,
                COUNT(*) AS listings_count,
                SUM(CASE WHEN status = 'DELIVERED' THEN 1 ELSE 0 END) AS delivered_count,
                SUM(CASE WHEN status = 'DELIVERED' AND quantity_unit = 'servings' THEN quantity_value ELSE 0 END) AS meals_saved,
                SUM(CASE WHEN status = 'DELIVERED' AND quantity_unit = 'kg' THEN quantity_value ELSE 0 END) AS kg_saved
            ")
            ->groupBy('city_name')
            ->orderBy('city_name')
            ->get();

        return collect($rows);
    }

    /** Format quantity for dashboard display. */
    public static function formatAmount(float $value): string
    {
        if ($value <= 0) {
            return '0';
        }

        return rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.');
    }
}

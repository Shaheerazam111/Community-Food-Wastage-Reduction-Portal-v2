<?php

namespace App\Http\Controllers;

use App\Models\FoodListing;
use App\Support\ListingImpact;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    public function reports()
    {
        $totals = ListingImpact::totals();
        $totals['listings_count'] = FoodListing::count();
        $cityRows = ListingImpact::byCity();

        return view('admin.reports.index', [
            'totals' => $totals,
            'cityRows' => $cityRows,
        ]);
    }

    public function listings(Request $request)
    {
        $filters = $request->validate([
            'status' => 'nullable|in:AVAILABLE,CLAIMED,DELIVERED,EXPIRED,CANCELLED',
            'city' => 'nullable|string|max:100',
        ]);

        $query = FoodListing::with([
            'donor:id,name,business_name',
            'claimedBy:id,name,ngo_name',
        ])->latest();

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['city'])) {
            $cityNorm = strtolower(trim($filters['city']));
            $query->whereRaw('LOWER(TRIM(city)) = ?', [$cityNorm]);
        }

        $listings = $query->paginate(25)->withQueryString();

        $cities = FoodListing::query()
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->distinct()
            ->orderBy('city')
            ->pluck('city');

        return view('admin.listings.index', [
            'listings' => $listings,
            'cities' => $cities,
            'filters' => $filters,
        ]);
    }
}

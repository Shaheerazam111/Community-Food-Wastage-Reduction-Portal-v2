<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AdminReportService
{
    /** Max days in one report query */
    public const MAX_RANGE_DAYS = 366;

    // Build full report payload for admin screens
    public function build(Carbon $from, Carbon $to): array
    {
        $fromStart = $from->copy()->startOfDay();
        $toEnd = $to->copy()->endOfDay();

        $summary = $this->summary($fromStart, $toEnd);
        $byStatus = $this->countsByStatus($fromStart, $toEnd);
        $byCategory = $this->countsByCategory($fromStart, $toEnd);
        $dailyTrend = $this->listingsPerDay($fromStart, $toEnd);
        $byArea = $this->topPickupAreas($fromStart, $toEnd, 10);
        $topDonors = $this->topDonors($fromStart, $toEnd, 8);
        $topRecipients = $this->topRecipients($fromStart, $toEnd, 8);
        $recentListings = $this->recentListings($fromStart, $toEnd, 20);

        return compact(
            'summary',
            'byStatus',
            'byCategory',
            'dailyTrend',
            'byArea',
            'topDonors',
            'topRecipients',
            'recentListings',
            'fromStart',
            'toEnd'
        );
    }

    // Normalize date range from request
    public function resolveDateRange(?string $fromInput, ?string $toInput): array
    {
        $to = $toInput ? Carbon::parse($toInput)->startOfDay() : Carbon::today();
        $from = $fromInput ? Carbon::parse($fromInput)->startOfDay() : $to->copy()->subDays(29);

        if ($from->gt($to)) {
            [$from, $to] = [$to->copy(), $from->copy()];
        }

        if ($from->diffInDays($to) > self::MAX_RANGE_DAYS) {
            $from = $to->copy()->subDays(self::MAX_RANGE_DAYS);
        }

        return [$from, $to];
    }

    // Headline KPI numbers
    private function summary(Carbon $from, Carbon $to): array
    {
        $listingQuery = DB::table('food_listings')
            ->whereBetween('created_at', [$from, $to]);

        $totalListings = (clone $listingQuery)->count();
        $delivered = (clone $listingQuery)->where('status', 'DELIVERED')->count();
        $claimed = (clone $listingQuery)->where('status', 'CLAIMED')->count();
        $available = (clone $listingQuery)->where('status', 'AVAILABLE')->count();
        $expired = (clone $listingQuery)->where('status', 'EXPIRED')->count();

        $completedOrActive = $delivered + $claimed;
        $claimRate = $totalListings > 0
            ? round((($delivered + $claimed) / $totalListings) * 100, 1)
            : 0.0;
        $deliveryRate = $completedOrActive > 0
            ? round(($delivered / $completedOrActive) * 100, 1)
            : 0.0;

        $donors = DB::table('users')->where('role', 'donor')->count();
        $recipients = DB::table('users')->where('role', 'recipient')->count();
        $newDonors = DB::table('users')
            ->where('role', 'donor')
            ->whereBetween('created_at', [$from, $to])
            ->count();
        $newRecipients = DB::table('users')
            ->where('role', 'recipient')
            ->whereBetween('created_at', [$from, $to])
            ->count();

        return [
            'total_listings' => $totalListings,
            'available' => $available,
            'claimed' => $claimed,
            'delivered' => $delivered,
            'expired' => $expired,
            'claim_rate_pct' => $claimRate,
            'delivery_rate_pct' => $deliveryRate,
            'donors_total' => $donors,
            'recipients_total' => $recipients,
            'donors_new' => $newDonors,
            'recipients_new' => $newRecipients,
        ];
    }

    // Listings grouped by status
    private function countsByStatus(Carbon $from, Carbon $to): Collection
    {
        return DB::table('food_listings')
            ->select('status', DB::raw('COUNT(*) as total'))
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('status')
            ->orderByDesc('total')
            ->get();
    }

    // Listings grouped by veg / non_veg
    private function countsByCategory(Carbon $from, Carbon $to): Collection
    {
        return DB::table('food_listings')
            ->select('category', DB::raw('COUNT(*) as total'))
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();
    }

    // New listings per calendar day
    private function listingsPerDay(Carbon $from, Carbon $to): Collection
    {
        return DB::table('food_listings')
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('day')
            ->orderBy('day')
            ->get();
    }

    // Busiest pickup areas (address text)
    private function topPickupAreas(Carbon $from, Carbon $to, int $limit): Collection
    {
        return DB::table('food_listings')
            ->select('pickup_address', DB::raw('COUNT(*) as total'))
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('pickup_address')
            ->orderByDesc('total')
            ->limit($limit)
            ->get();
    }

    // Donors with most listings in range
    private function topDonors(Carbon $from, Carbon $to, int $limit): Collection
    {
        return DB::table('food_listings')
            ->join('users', 'food_listings.donor_id', '=', 'users.id')
            ->select(
                'users.id',
                'users.name',
                'users.business_name',
                DB::raw('COUNT(*) as listings'),
                DB::raw("SUM(CASE WHEN food_listings.status = 'DELIVERED' THEN 1 ELSE 0 END) as delivered")
            )
            ->whereBetween('food_listings.created_at', [$from, $to])
            ->groupBy('users.id', 'users.name', 'users.business_name')
            ->orderByDesc('listings')
            ->limit($limit)
            ->get();
    }

    // Recipients with most claims in range
    private function topRecipients(Carbon $from, Carbon $to, int $limit): Collection
    {
        return DB::table('food_listings')
            ->join('users', 'food_listings.claimed_by', '=', 'users.id')
            ->select(
                'users.id',
                'users.name',
                'users.ngo_name',
                DB::raw('COUNT(*) as claims'),
                DB::raw("SUM(CASE WHEN food_listings.status = 'DELIVERED' THEN 1 ELSE 0 END) as delivered")
            )
            ->whereNotNull('food_listings.claimed_by')
            ->whereBetween('food_listings.claimed_at', [$from, $to])
            ->groupBy('users.id', 'users.name', 'users.ngo_name')
            ->orderByDesc('claims')
            ->limit($limit)
            ->get();
    }

    // Latest listings in date window
    private function recentListings(Carbon $from, Carbon $to, int $limit): Collection
    {
        return DB::table('food_listings')
            ->join('users as donors', 'food_listings.donor_id', '=', 'donors.id')
            ->leftJoin('users as recipients', 'food_listings.claimed_by', '=', 'recipients.id')
            ->select(
                'food_listings.id',
                'food_listings.title',
                'food_listings.category',
                'food_listings.status',
                'food_listings.pickup_address',
                'food_listings.created_at',
                'donors.name as donor_name',
                'recipients.ngo_name as recipient_ngo'
            )
            ->whereBetween('food_listings.created_at', [$from, $to])
            ->orderByDesc('food_listings.created_at')
            ->limit($limit)
            ->get();
    }
}

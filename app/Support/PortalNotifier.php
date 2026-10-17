<?php

namespace App\Support;

use App\Models\FoodListing;
use App\Models\PortalNotification;
use App\Models\User;

class PortalNotifier
{
    /** Save one dashboard alert for a user. */
    public static function notifyUser(int $userId, string $message, ?int $listingId = null): void
    {
        PortalNotification::create([
            'user_id' => $userId,
            'food_listing_id' => $listingId,
            'message' => $message,
            'is_read' => false,
        ]);
    }

    /** Alert verified recipients in the listing city. */
    public static function notifyRecipientsNewListing(FoodListing $listing): void
    {
        $cityNorm = strtolower(trim((string) $listing->city));
        if ($cityNorm === '') {
            return;
        }

        $recipientIds = User::query()
            ->where('role', 'recipient')
            ->where('is_verified', true)
            ->where('is_active', true)
            ->whereRaw('LOWER(TRIM(city)) = ?', [$cityNorm])
            ->pluck('id');

        $message = 'New food in '.$listing->city.': '.$listing->title.'.';

        foreach ($recipientIds as $recipientId) {
            self::notifyUser((int) $recipientId, $message, $listing->id);
        }
    }
}

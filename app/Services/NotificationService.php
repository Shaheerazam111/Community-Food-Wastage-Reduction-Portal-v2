<?php

namespace App\Services;

use App\Models\PortalNotification;
use App\Models\User;

class NotificationService
{
    // Save dashboard alert for one user
    public function notify(User $user, string $title, string $body): PortalNotification
    {
        return PortalNotification::create([
            'user_id' => $user->id,
            'title' => $title,
            'body' => $body,
        ]);
    }
}

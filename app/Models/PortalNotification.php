<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortalNotification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'food_listing_id',
        'message',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function foodListing()
    {
        return $this->belongsTo(FoodListing::class, 'food_listing_id');
    }
}

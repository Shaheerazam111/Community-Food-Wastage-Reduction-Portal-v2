<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListingFeedback extends Model
{
    protected $table = 'listing_feedback';

    protected $fillable = [
        'food_listing_id',
        'recipient_id',
        'rating',
        'comment',
    ];

    public function listing()
    {
        return $this->belongsTo(FoodListing::class, 'food_listing_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }
}

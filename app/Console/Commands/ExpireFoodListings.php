<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExpireFoodListings extends Command
{
    protected $signature = 'food:expire';

    protected $description = 'Mark AVAILABLE food listings past their expiry time as EXPIRED';

    public function handle()
    {
        $count = DB::table('food_listings')
            ->where('status', 'AVAILABLE')
            ->where('expiry_at', '<=', now())
            ->update(['status' => 'EXPIRED', 'updated_at' => now()]);

        $this->info("Marked {$count} listing(s) as EXPIRED.");

        return Command::SUCCESS;
    }
}

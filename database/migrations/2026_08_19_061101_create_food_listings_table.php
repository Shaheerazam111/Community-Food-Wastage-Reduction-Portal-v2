<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donor_id')->constrained('users')->cascadeOnDelete();

            $table->string('title');
            $table->enum('category', ['veg', 'non_veg']);
            $table->string('quantity');
            $table->string('pickup_address');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->dateTime('expiry_at');

            $table->enum('status', ['AVAILABLE', 'CLAIMED', 'DELIVERED', 'EXPIRED'])
                ->default('AVAILABLE');

            $table->foreignId('claimed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('claimed_at')->nullable();
            $table->dateTime('delivered_at')->nullable();

            $table->timestamps();

            $table->index(['status', 'expiry_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_listings');
    }
};

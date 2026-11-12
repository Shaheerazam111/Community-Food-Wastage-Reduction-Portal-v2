<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('donor', 'recipient', 'admin', 'volunteer') NOT NULL");
        }
        // SQLite stores role as string — no enum alter needed

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('role');
            }
        });

        Schema::table('food_listings', function (Blueprint $table) {
            if (! Schema::hasColumn('food_listings', 'photo_path')) {
                $table->string('photo_path')->nullable()->after('quantity');
            }
            if (! Schema::hasColumn('food_listings', 'city')) {
                $table->string('city')->nullable()->after('pickup_address');
            }
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('body');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::create('listing_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('food_listing_id')->constrained('food_listings')->cascadeOnDelete();
            $table->foreignId('recipient_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->string('comment')->nullable();
            $table->timestamps();
            $table->unique(['food_listing_id', 'recipient_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listing_feedback');
        Schema::dropIfExists('notifications');

        Schema::table('food_listings', function (Blueprint $table) {
            $table->dropColumn(['photo_path', 'city']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('donor', 'recipient', 'admin') NOT NULL");

        Schema::table('users', function (Blueprint $table) {
            $table->string('city', 100)->default('Lahore')->after('address');
            $table->boolean('is_verified')->default(false)->after('longitude');
            $table->boolean('is_active')->default(true)->after('is_verified');
        });

        Schema::table('food_listings', function (Blueprint $table) {
            $table->string('city', 100)->default('Lahore')->after('pickup_address');
        });
    }

    public function down(): void
    {
        Schema::table('food_listings', function (Blueprint $table) {
            $table->dropColumn('city');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['city', 'is_verified', 'is_active']);
        });

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('donor', 'recipient') NOT NULL");
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['donor', 'recipient'])->after('email');

            // Donor profile fields
            $table->string('business_name')->nullable();
            $table->string('phone')->nullable();

            // Recipient profile fields
            $table->string('ngo_name')->nullable();
            $table->string('contact_person')->nullable();

            // Shared location fields
            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'business_name',
                'phone',
                'ngo_name',
                'contact_person',
                'address',
                'latitude',
                'longitude',
            ]);
        });
    }
};

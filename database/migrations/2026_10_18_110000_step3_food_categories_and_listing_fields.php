<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $vegId = DB::table('food_categories')->insertGetId([
            'name' => 'Vegetarian',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $nonVegId = DB::table('food_categories')->insertGetId([
            'name' => 'Non-vegetarian',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Schema::table('food_listings', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('title')->constrained('food_categories');
            $table->text('description')->nullable()->after('category_id');
            $table->decimal('quantity_value', 10, 2)->nullable()->after('description');
            $table->enum('quantity_unit', ['kg', 'servings'])->nullable()->after('quantity_value');
            $table->string('photo_path')->nullable()->after('quantity_unit');
        });

        DB::table('food_listings')->orderBy('id')->chunkById(100, function ($rows) use ($vegId, $nonVegId) {
            foreach ($rows as $row) {
                $categoryId = $row->category === 'non_veg' ? $nonVegId : $vegId;
                DB::table('food_listings')->where('id', $row->id)->update([
                    'category_id' => $categoryId,
                    'quantity_value' => 1,
                    'quantity_unit' => 'servings',
                ]);
            }
        });

        Schema::table('food_listings', function (Blueprint $table) {
            $table->dropColumn(['category', 'quantity']);
        });

        DB::statement("ALTER TABLE food_listings MODIFY COLUMN status ENUM('AVAILABLE', 'CLAIMED', 'DELIVERED', 'EXPIRED', 'CANCELLED') NOT NULL DEFAULT 'AVAILABLE'");
    }

    public function down(): void
    {
        Schema::table('food_listings', function (Blueprint $table) {
            $table->enum('category', ['veg', 'non_veg'])->nullable();
            $table->string('quantity')->nullable();
        });

        DB::statement("ALTER TABLE food_listings MODIFY COLUMN status ENUM('AVAILABLE', 'CLAIMED', 'DELIVERED', 'EXPIRED') NOT NULL DEFAULT 'AVAILABLE'");

        Schema::table('food_listings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
            $table->dropColumn(['description', 'quantity_value', 'quantity_unit', 'photo_path']);
        });

        Schema::dropIfExists('food_categories');
    }
};

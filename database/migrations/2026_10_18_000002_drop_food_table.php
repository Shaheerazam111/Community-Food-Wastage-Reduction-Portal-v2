<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('food');
    }

    public function down(): void
    {
        Schema::create('food', function ($table) {
            $table->id();
            $table->string('name');
            $table->integer('price');
            $table->text('description');
            $table->timestamps();
        });
    }
};

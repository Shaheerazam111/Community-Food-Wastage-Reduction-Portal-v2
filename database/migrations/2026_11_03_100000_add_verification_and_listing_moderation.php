<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'verification_status')) {
                $table->string('verification_status', 20)->default('approved')->after('is_active');
            }
        });

        DB::table('users')->where('role', 'recipient')->update(['verification_status' => 'approved']);

        Schema::create('verification_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('file_path');
            $table->string('original_name')->nullable();
            $table->string('status', 20)->default('pending');
            $table->text('admin_note')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });

        Schema::table('food_listings', function (Blueprint $table) {
            if (! Schema::hasColumn('food_listings', 'moderation_status')) {
                $table->string('moderation_status', 20)->default('PENDING')->after('status');
                $table->string('rejection_reason')->nullable()->after('moderation_status');
                $table->foreignId('moderated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('moderated_at')->nullable()->after('moderated_by');
            }
        });

        if (Schema::hasColumn('food_listings', 'moderation_status')) {
            DB::table('food_listings')->update(['moderation_status' => 'APPROVED']);
        }
    }

    public function down(): void
    {
        Schema::table('food_listings', function (Blueprint $table) {
            $table->dropForeign(['moderated_by']);
            $table->dropColumn(['moderation_status', 'rejection_reason', 'moderated_by', 'moderated_at']);
        });

        Schema::dropIfExists('verification_documents');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('verification_status');
        });
    }
};

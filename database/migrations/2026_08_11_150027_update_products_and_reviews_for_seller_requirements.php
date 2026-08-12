<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'is_medical')) {
                $table->boolean('is_medical')->default(false)->after('seller_id');
            }
            if (!Schema::hasColumn('products', 'generic_name')) {
                $table->string('generic_name')->nullable()->after('is_medical');
            }
            if (!Schema::hasColumn('products', 'prescription_required')) {
                $table->boolean('prescription_required')->default(false)->after('generic_name');
            }
            if (!Schema::hasColumn('products', 'side_effects_warnings')) {
                $table->text('side_effects_warnings')->nullable()->after('prescription_required');
            }
            if (!Schema::hasColumn('products', 'custom_sections')) {
                $table->json('custom_sections')->nullable()->after('side_effects_warnings');
            }
            if (!Schema::hasColumn('products', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable();
            }
        });

        Schema::table('product_reviews', function (Blueprint $table) {
            if (!Schema::hasColumn('product_reviews', 'status')) {
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('comment');
            }
            if (!Schema::hasColumn('product_reviews', 'is_admin_custom')) {
                $table->boolean('is_admin_custom')->default(false)->after('status');
            }
            if (!Schema::hasColumn('product_reviews', 'reviewer_avatar')) {
                $table->string('reviewer_avatar')->nullable();
            }
        });

        if (!Schema::hasTable('product_review_replies')) {
            Schema::create('product_review_replies', function (Blueprint $table) {
                $table->id();
                $table->foreignId('review_id')->constrained('product_reviews')->onDelete('cascade');
                $table->unsignedBigInteger('user_id');
                $table->text('reply_text');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['is_medical', 'generic_name', 'prescription_required', 'side_effects_warnings', 'custom_sections', 'rejection_reason']);
        });

        Schema::table('product_reviews', function (Blueprint $table) {
            $table->dropColumn(['status', 'is_admin_custom', 'reviewer_avatar']);
        });

        Schema::dropIfExists('product_review_replies');
    }
};

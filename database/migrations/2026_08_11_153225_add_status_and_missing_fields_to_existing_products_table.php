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
            if (!Schema::hasColumn('products', 'status')) {
                $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'disabled'])->default('approved')->after('is_active');
            }
            if (!Schema::hasColumn('products', 'product_type')) {
                $table->enum('product_type', ['single', 'variant'])->default('single');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['status', 'product_type']);
        });
    }
};

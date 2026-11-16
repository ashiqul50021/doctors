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
            $table->string('coupon_commission_type')->default('percentage')->after('agent_commission_value');
            $table->decimal('coupon_commission_value', 10, 2)->nullable()->after('coupon_commission_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['coupon_commission_type', 'coupon_commission_value']);
        });
    }
};

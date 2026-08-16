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
            $table->boolean('override_shipping')->default(false)->after('landing_settings');
            $table->decimal('inside_dhaka_charge', 10, 2)->nullable()->after('override_shipping');
            $table->decimal('outside_dhaka_charge', 10, 2)->nullable()->after('inside_dhaka_charge');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['override_shipping', 'inside_dhaka_charge', 'outside_dhaka_charge']);
        });
    }
};

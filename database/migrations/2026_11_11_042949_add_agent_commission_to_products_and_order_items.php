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
            $table->string('agent_commission_type')->default('percentage')->after('price');
            $table->decimal('agent_commission_value', 10, 2)->nullable()->after('agent_commission_type');
        });

        if (Schema::hasTable('order_items')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->decimal('agent_commission', 10, 2)->default(0.00)->after('total');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['agent_commission_type', 'agent_commission_value']);
        });

        if (Schema::hasTable('order_items') && Schema::hasColumn('order_items', 'agent_commission')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropColumn('agent_commission');
            });
        }
    }
};

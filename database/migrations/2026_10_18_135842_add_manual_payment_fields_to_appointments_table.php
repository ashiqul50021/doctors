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
        Schema::table('appointments', function (Blueprint $table) {
            if (!Schema::hasColumn('appointments', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('fee');
            }
            if (!Schema::hasColumn('appointments', 'payment_status')) {
                $table->string('payment_status')->default('pending')->after('payment_method');
            }
            if (!Schema::hasColumn('appointments', 'sender_number')) {
                $table->string('sender_number')->nullable()->after('payment_status');
            }
            if (!Schema::hasColumn('appointments', 'transaction_id')) {
                $table->string('transaction_id')->nullable()->after('sender_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'payment_status', 'sender_number', 'transaction_id']);
        });
    }
};

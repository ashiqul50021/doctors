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
        if (Schema::hasTable('seller_profiles')) {
            Schema::table('seller_profiles', function (Blueprint $table) {
                if (!Schema::hasColumn('seller_profiles', 'wallet_balance')) {
                    $table->decimal('wallet_balance', 12, 2)->default(0.00)->after('commission_rate');
                }
            });
        }

        if (!Schema::hasTable('seller_payouts')) {
            Schema::create('seller_payouts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
                $table->decimal('amount', 12, 2);
                $table->string('payment_method')->default('bank');
                $table->text('account_details')->nullable();
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->text('admin_note')->nullable();
                $table->timestamp('processed_at')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_payouts');

        if (Schema::hasTable('seller_profiles')) {
            Schema::table('seller_profiles', function (Blueprint $table) {
                if (Schema::hasColumn('seller_profiles', 'wallet_balance')) {
                    $table->dropColumn('wallet_balance');
                }
            });
        }
    }
};

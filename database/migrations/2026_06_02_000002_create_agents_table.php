<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('phone')->nullable();
            $table->string('referral_code')->unique();
            
            // Permissions toggled by Admin
            $table->boolean('can_book_appointments')->default(true);
            $table->boolean('can_sell_products')->default(true);
            $table->boolean('can_sell_courses')->default(true);
            
            // Commission Rates configured by Admin
            $table->decimal('booking_commission_rate', 10, 2)->default(0.00); // flat fee per booking
            $table->decimal('product_commission_rate', 5, 2)->default(0.00); // percentage of sale
            $table->decimal('course_commission_rate', 5, 2)->default(0.00); // percentage of course sale
            
            // Wallet Balance
            $table->decimal('wallet_balance', 12, 2)->default(0.00);
            
            // Status
            $table->enum('status', ['pending', 'active', 'suspended'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};

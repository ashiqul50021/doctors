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
        Schema::create('agent_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained()->onDelete('cascade');
            $table->enum('type', [
                'commission_booking',
                'commission_product',
                'commission_course',
                'payout_request',
                'payout_approved',
                'payout_rejected'
            ]);
            $table->decimal('amount', 12, 2);
            $table->text('description')->nullable();
            $table->string('reference_id')->nullable(); // e.g. order_number or appointment_id
            $table->enum('status', ['pending', 'completed', 'rejected'])->default('completed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_transactions');
    }
};

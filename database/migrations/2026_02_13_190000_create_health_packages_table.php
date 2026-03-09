<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('health_packages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('badge_label');        // e.g. Basic, Standard, Premium, Specialized
            $table->string('icon')->default('fas fa-heartbeat'); // FontAwesome icon class
            $table->integer('test_count')->default(0);  // e.g. 15, 40, 70
            $table->json('features')->nullable();  // array of feature strings
            $table->decimal('price', 10, 2);
            $table->string('price_label')->default('one-time'); // e.g. one-time, monthly
            $table->string('link')->nullable();    // custom link for "Book Now"
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('health_packages');
    }
};

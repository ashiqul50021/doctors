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
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->nullable()->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('phone')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->foreignId('speciality_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('qualification')->nullable();
            $table->string('registration_number')->nullable();
            $table->date('registration_date')->nullable();
            $table->text('bio')->nullable();
            $table->json('languages')->nullable();
            $table->json('education')->nullable();
            $table->json('services')->nullable();
            $table->json('awards')->nullable();
            $table->string('clinic_name')->nullable();
            $table->string('clinic_address')->nullable();
            $table->string('website')->nullable();
            $table->string('facebook')->nullable();
            $table->string('linkedin')->nullable();
            $table->foreignId('district_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('area_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('consultation_fee', 10, 2)->default(0);
            $table->boolean('online_consultation')->default(false);
            $table->decimal('online_fee', 10, 2)->nullable();
            $table->boolean('home_visit')->default(false);
            $table->decimal('home_visit_fee', 10, 2)->nullable();
            $table->integer('experience_years')->default(0);
            $table->string('profile_image')->nullable();
            $table->string('cover_image')->nullable();
            $table->json('gallery')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};

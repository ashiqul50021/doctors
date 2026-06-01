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
        Schema::table('users', function (Blueprint $table) {
            // Modify role column to a string to allow adding 'agent' and other roles without strict enum constraints
            $table->string('role')->default('patient')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Restrict back to enum if needed, though string is more flexible
            $table->enum('role', ['patient', 'doctor', 'admin'])->default('patient')->change();
        });
    }
};

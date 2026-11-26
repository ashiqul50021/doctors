<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            if (!Schema::hasColumn('doctors', 'designation')) {
                $table->string('designation')->nullable()->after('qualification');
            }
            if (!Schema::hasColumn('doctors', 'organization_name')) {
                $table->string('organization_name')->nullable()->after('designation');
            }
        });
    }

    public function down(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->dropColumn(['designation', 'organization_name']);
        });
    }
};

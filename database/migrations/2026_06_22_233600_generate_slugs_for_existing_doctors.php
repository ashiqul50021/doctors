<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Doctor;
use Illuminate\Support\Str;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $doctors = Doctor::whereNull('slug')->get();
        foreach ($doctors as $doctor) {
            $name = $doctor->user ? $doctor->user->name : 'doctor';
            $slug = Str::slug($name);
            if (empty($slug)) {
                $slug = 'doctor';
            }
            $originalSlug = $slug;
            $count = 2;
            while (Doctor::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count;
                $count++;
            }
            $doctor->update(['slug' => $slug]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Slugs can remain, no action needed on rollback
    }
};

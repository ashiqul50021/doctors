<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TopCategory;

class TopCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Cardiology',
                'slug' => 'cardiology',
                'icon' => 'fas fa-heartbeat',
                'description' => 'Heart and cardiovascular specialists',
                'order' => 1,
            ],
            [
                'name' => 'Neurology',
                'slug' => 'neurology',
                'icon' => 'fas fa-brain',
                'description' => 'Brain and nervous system specialists',
                'order' => 2,
            ],
            [
                'name' => 'Orthopedics',
                'slug' => 'orthopedics',
                'icon' => 'fas fa-bone',
                'description' => 'Bone and joint specialists',
                'order' => 3,
            ],
            [
                'name' => 'Dermatology',
                'slug' => 'dermatology',
                'icon' => 'fas fa-allergies',
                'description' => 'Skin care specialists',
                'order' => 4,
            ],
            [
                'name' => 'Pediatrics',
                'slug' => 'pediatrics',
                'icon' => 'fas fa-baby',
                'description' => 'Child health specialists',
                'order' => 5,
            ],
            [
                'name' => 'Ophthalmology',
                'slug' => 'ophthalmology',
                'icon' => 'fas fa-eye',
                'description' => 'Eye care specialists',
                'order' => 6,
            ],
            [
                'name' => 'Dentistry',
                'slug' => 'dentistry',
                'icon' => 'fas fa-tooth',
                'description' => 'Dental care specialists',
                'order' => 7,
            ],
            [
                'name' => 'Gynecology',
                'slug' => 'gynecology',
                'icon' => 'fas fa-female',
                'description' => 'Women health specialists',
                'order' => 8,
            ],
        ];

        foreach ($categories as $category) {
            TopCategory::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}

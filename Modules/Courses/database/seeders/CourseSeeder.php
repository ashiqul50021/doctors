<?php

namespace Modules\Courses\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Courses\Models\Course;
use Modules\Courses\Models\CourseCategory;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure we have some categories
        $categories = [
            'Health Management' => 'health-management',
            'Cardiology' => 'cardiology',
            'Mental Health' => 'mental-health',
            'Nutrition' => 'nutrition',
        ];

        foreach ($categories as $name => $slug) {
            CourseCategory::firstOrCreate(
                ['slug' => $slug],
                ['name' => $name]
            );
        }

        // Create sample courses
        $courses = [
            [
                'title' => 'Diabetes Management',
                'description' => 'Comprehensive guide to managing diabetes effectively.',
                'short_description' => 'Learn how to manage blood sugar levels, diet plans, and lifestyle changes for diabetes control.',
                'price' => 0,
                'image' => 'assets/img/features/feature-01.jpg', // Using existing asset
                'duration_hours' => 3,
                'level' => 'beginner',
                'category_slug' => 'health-management',
            ],
            [
                'title' => 'Heart Health Awareness',
                'description' => 'Understanding cardiovascular health and changes.',
                'short_description' => 'Understanding cardiovascular health, risk factors, prevention strategies and heart-healthy lifestyle.',
                'price' => 500,
                'image' => 'assets/img/features/feature-02.jpg',
                'duration_hours' => 4,
                'level' => 'intermediate',
                'category_slug' => 'cardiology',
            ],
            [
                'title' => 'Mental Health & Wellness',
                'description' => 'Strategies for better mental health.',
                'short_description' => 'Techniques for stress management, anxiety relief, and maintaining positive mental health.',
                'price' => 0,
                'image' => 'assets/img/features/feature-03.jpg',
                'duration_hours' => 2,
                'level' => 'beginner',
                'category_slug' => 'mental-health',
            ],
            [
                'title' => 'Nutrition for Healthy Life',
                'description' => 'Basics of nutrition and healthy eating.',
                'short_description' => 'Discover the secrets of a balanced diet and how nutrition impacts your overall well-being.',
                'price' => 1200,
                'image' => 'assets/img/features/feature-04.jpg',
                'duration_hours' => 5,
                'level' => 'beginner',
                'category_slug' => 'nutrition',
            ],
        ];

        $admin = \App\Models\User::first(); // Assign to first user/admin if exists

        foreach ($courses as $data) {
            $category = CourseCategory::where('slug', $data['category_slug'])->first();

            Course::updateOrCreate(
                ['slug' => Str::slug($data['title'])],
                [
                    'title' => $data['title'],
                    'course_category_id' => $category->id,
                    'instructor_id' => $admin ? $admin->id : null,
                    'description' => $data['description'],
                    'short_description' => $data['short_description'],
                    'price' => $data['price'],
                    'sale_price' => $data['price'] > 0 ? $data['price'] * 0.8 : null,
                    'image' => $data['image'],
                    'duration_hours' => $data['duration_hours'],
                    'level' => $data['level'],
                    'is_active' => true,
                    'is_featured' => true,
                ]
            );
        }
    }
}

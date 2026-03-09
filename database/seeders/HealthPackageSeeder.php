<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HealthPackage;

class HealthPackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'title' => 'Basic Health Checkup',
                'badge_label' => 'Basic',
                'icon' => 'fas fa-heartbeat',
                'test_count' => 15,
                'features' => ['Blood Sugar Test', 'Lipid Profile', 'Liver Function', 'Kidney Function'],
                'price' => 1500,
                'price_label' => 'one-time',
                'is_featured' => false,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Full Body Checkup',
                'badge_label' => 'Standard',
                'icon' => 'fas fa-shield-alt',
                'test_count' => 40,
                'features' => ['Complete Blood Count', 'Thyroid Profile', 'Vitamin Tests', 'ECG & X-Ray'],
                'price' => 3500,
                'price_label' => 'one-time',
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'title' => 'Executive Checkup',
                'badge_label' => 'Premium',
                'icon' => 'fas fa-gem',
                'test_count' => 70,
                'features' => ['Full Body Screening', 'Cardiac Risk Markers', 'Cancer Markers', 'Doctor Consultation'],
                'price' => 7000,
                'price_label' => 'one-time',
                'is_featured' => false,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'title' => 'Diabetes Care',
                'badge_label' => 'Specialized',
                'icon' => 'fas fa-tint',
                'test_count' => 25,
                'features' => ['HbA1c Test', 'Fasting Blood Sugar', 'Insulin Level', 'Kidney Profile'],
                'price' => 2500,
                'price_label' => 'one-time',
                'is_featured' => false,
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($packages as $package) {
            HealthPackage::create($package);
        }
    }
}

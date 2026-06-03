<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Speciality;
use App\Models\District;
use App\Models\Area;
use Modules\Ecommerce\Models\Product;
use Modules\Ecommerce\Models\ProductCategory;

class RealDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Resolve Specialities
        $cardiology = Speciality::updateOrCreate(
            ['slug' => 'cardiology'],
            ['name' => 'Cardiology', 'description' => 'Heart specialist', 'is_active' => true]
        );

        $gynecology = Speciality::updateOrCreate(
            ['slug' => 'gynecology'],
            ['name' => 'Gynecology', 'description' => 'Women health specialist', 'is_active' => true]
        );

        // 2. Resolve District and Area
        $dhaka = District::where('name', 'like', '%Dhaka%')->first();
        if (!$dhaka) {
            $dhaka = District::create(['name' => 'Dhaka']);
        }

        $dhanmondi = Area::where('district_id', $dhaka->id)->where('name', 'like', '%Dhanmondi%')->first();
        if (!$dhanmondi) {
            $dhanmondi = Area::create(['name' => 'Dhanmondi', 'district_id' => $dhaka->id]);
        }

        $gulshan = Area::where('district_id', $dhaka->id)->where('name', 'like', '%Gulshan%')->first();
        if (!$gulshan) {
            $gulshan = Area::create(['name' => 'Gulshan', 'district_id' => $dhaka->id]);
        }

        // 3. Create Doctor 1 (Male)
        $user1 = User::updateOrCreate(
            ['email' => 'dr.james@example.com'],
            [
                'name' => 'Dr. James Anderson',
                'password' => Hash::make('password123'),
                'role' => 'doctor',
            ]
        );

        Doctor::updateOrCreate(
            ['user_id' => $user1->id],
            [
                'phone' => '01711122233',
                'gender' => 'male',
                'date_of_birth' => '1982-05-15',
                'speciality_id' => $cardiology->id,
                'qualification' => 'MBBS, MD, FCPS (Cardiology)',
                'registration_number' => 'BMDC-C-9872',
                'registration_date' => '2010-06-12',
                'bio' => 'Senior Consultant Cardiologist specializing in interventional cardiology and preventive cardiovascular health with over 15 years of clinical practice.',
                'clinic_name' => 'Dhaka Heart & Vascular Clinic',
                'clinic_address' => 'House 45, Road 15, Dhanmondi, Dhaka',
                'district_id' => $dhaka->id,
                'area_id' => $dhanmondi->id,
                'consultation_fee' => 1200.00,
                'pricing' => 'custom',
                'custom_price' => 1200.00,
                'online_consultation' => true,
                'online_fee' => 1000.00,
                'home_visit' => false,
                'experience_years' => 16,
                'profile_image' => 'uploads/doctors/doctor_male_real.png',
                'status' => 'approved',
                'is_featured' => true,
                'is_verified' => true,
                'verified_at' => now(),
            ]
        );

        // 4. Create Doctor 2 (Female)
        $user2 = User::updateOrCreate(
            ['email' => 'dr.sarah@example.com'],
            [
                'name' => 'Dr. Sarah Jenkins',
                'password' => Hash::make('password123'),
                'role' => 'doctor',
            ]
        );

        Doctor::updateOrCreate(
            ['user_id' => $user2->id],
            [
                'phone' => '01711122244',
                'gender' => 'female',
                'date_of_birth' => '1986-09-22',
                'speciality_id' => $gynecology->id,
                'qualification' => 'MBBS, MS, MCPS (Gynecology & Obstetrics)',
                'registration_number' => 'BMDC-G-6541',
                'registration_date' => '2012-08-20',
                'bio' => 'Dedicated Obstetrician and Gynecologist with expertise in high-risk pregnancies, reproductive endocrinology, and minimally invasive gynecologic surgery.',
                'clinic_name' => 'Sarah Women\'s Care Center',
                'clinic_address' => 'Plot 12, Gulshan Avenue, Gulshan-2, Dhaka',
                'district_id' => $dhaka->id,
                'area_id' => $gulshan->id,
                'consultation_fee' => 1000.00,
                'pricing' => 'custom',
                'custom_price' => 1000.00,
                'online_consultation' => true,
                'online_fee' => 800.00,
                'home_visit' => true,
                'home_visit_fee' => 1500.00,
                'experience_years' => 14,
                'profile_image' => 'uploads/doctors/doctor_female_real.png',
                'status' => 'approved',
                'is_featured' => true,
                'is_verified' => true,
                'verified_at' => now(),
            ]
        );

        // 5. Resolve Product Category
        $wellnessCat = ProductCategory::updateOrCreate(
            ['slug' => 'wellness'],
            ['name' => 'Wellness', 'description' => 'Wellness and health supplements', 'is_active' => true]
        );

        // 6. Create Product
        Product::updateOrCreate(
            ['slug' => 'cepodrox-500mg-antibiotics'],
            [
                'name' => 'Cepodrox 500mg Antibiotics',
                'product_category_id' => $wellnessCat->id,
                'description' => 'High-quality Cepodrox 500mg capsules for treating bacterial infections. Please consume strictly under professional doctor prescriptions.',
                'price' => 350.00,
                'sale_price' => 299.00,
                'stock' => 120,
                'image' => 'uploads/products/health_product_real.png',
                'is_active' => true,
                'is_featured' => true,
            ]
        );
    }
}

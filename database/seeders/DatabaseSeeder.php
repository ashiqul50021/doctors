<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Admin User',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        $this->call([
            SiteSettingSeeder::class,
            MenuSeeder::class,
            TopCategorySeeder::class,
            DistrictSeeder::class,
            AreaSeeder::class,
            AllDistrictAreasSeeder::class,
            SpecialitySeeder::class,
            DoctorSeeder::class,
            ProductSeeder::class,
        ]);
    }
}

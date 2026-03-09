<?php

namespace Database\Seeders;

use App\Models\Speciality;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SpecialitySeeder extends Seeder
{
    public function run(): void
    {
        $specialities = [
            ['name' => 'Cardiology', 'image' => 'assets/img/specialities/specialities-01.png'],
            ['name' => 'Neurology', 'image' => 'assets/img/specialities/specialities-02.png'],
            ['name' => 'Orthopedics', 'image' => 'assets/img/specialities/specialities-03.png'],
            ['name' => 'Dermatology', 'image' => 'assets/img/specialities/specialities-04.png'],
            ['name' => 'Pediatrics', 'image' => 'assets/img/specialities/specialities-05.png'],
            ['name' => 'Gynecology', 'image' => 'assets/img/specialities/specialities-01.png'],
            ['name' => 'Dentistry', 'image' => 'assets/img/specialities/specialities-02.png'],
            ['name' => 'Urology', 'image' => 'assets/img/specialities/specialities-03.png'],
            ['name' => 'Psychiatry', 'image' => 'assets/img/specialities/specialities-04.png'],
            ['name' => 'ENT', 'image' => 'assets/img/specialities/specialities-05.png'],
        ];

        foreach ($specialities as $item) {
            Speciality::updateOrCreate(
                ['slug' => Str::slug($item['name'])],
                [
                    'name' => $item['name'],
                    'image' => $item['image'],
                    'description' => $item['name'] . ' specialist doctors',
                    'is_active' => true,
                ]
            );
        }
    }
}

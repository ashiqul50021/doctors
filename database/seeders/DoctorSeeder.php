<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\District;
use App\Models\Doctor;
use App\Models\Speciality;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        $doctors = [
            [
                'name' => 'Dr. Rafiqul Islam',
                'email' => 'dr.rafiq@example.com',
                'speciality' => 'Cardiology',
                'district' => 'Dhaka',
                'area' => 'Dhanmondi',
                'qualification' => 'MBBS, FCPS (Cardiology)',
                'experience_years' => 15,
                'consultation_fee' => 1500,
                'clinic_name' => 'Dhanmondi Heart Care',
                'clinic_address' => 'Road 27, Dhanmondi, Dhaka',
                'gender' => 'male',
                'phone' => '01710000001',
                'profile_image' => 'assets/img/doctors/doctor-thumb-01.jpg',
            ],
            [
                'name' => 'Dr. Fatema Akter',
                'email' => 'dr.fatema@example.com',
                'speciality' => 'Gynecology',
                'district' => 'Dhaka',
                'area' => 'Gulshan',
                'qualification' => 'MBBS, FCPS (Gynecology)',
                'experience_years' => 12,
                'consultation_fee' => 1200,
                'clinic_name' => 'Women Care Clinic',
                'clinic_address' => 'Gulshan-1, Dhaka',
                'gender' => 'female',
                'phone' => '01710000002',
                'profile_image' => 'assets/img/doctors/doctor-thumb-02.jpg',
            ],
            [
                'name' => 'Dr. Kamal Hossain',
                'email' => 'dr.kamal@example.com',
                'speciality' => 'Neurology',
                'district' => 'Dhaka',
                'area' => 'Banani',
                'qualification' => 'MBBS, MD (Neurology)',
                'experience_years' => 18,
                'consultation_fee' => 2000,
                'clinic_name' => 'Brain & Spine Center',
                'clinic_address' => 'Banani, Dhaka',
                'gender' => 'male',
                'phone' => '01710000003',
                'profile_image' => 'assets/img/doctors/doctor-thumb-03.jpg',
            ],
            [
                'name' => 'Dr. Nasreen Begum',
                'email' => 'dr.nasreen@example.com',
                'speciality' => 'Pediatrics',
                'district' => 'Dhaka',
                'area' => 'Uttara',
                'qualification' => 'MBBS, DCH (Pediatrics)',
                'experience_years' => 10,
                'consultation_fee' => 800,
                'clinic_name' => 'Child Care Center',
                'clinic_address' => 'Uttara Sector 7, Dhaka',
                'gender' => 'female',
                'phone' => '01710000004',
                'profile_image' => 'assets/img/doctors/doctor-thumb-04.jpg',
            ],
            [
                'name' => 'Dr. Abdul Hamid',
                'email' => 'dr.hamid@example.com',
                'speciality' => 'Orthopedics',
                'district' => 'Dhaka',
                'area' => 'Mirpur',
                'qualification' => 'MBBS, MS (Orthopedics)',
                'experience_years' => 14,
                'consultation_fee' => 1000,
                'clinic_name' => 'Bone & Joint Clinic',
                'clinic_address' => 'Mirpur-10, Dhaka',
                'gender' => 'male',
                'phone' => '01710000005',
                'profile_image' => 'assets/img/doctors/doctor-thumb-05.jpg',
            ],
            [
                'name' => 'Dr. Shirin Akhtar',
                'email' => 'dr.shirin@example.com',
                'speciality' => 'Dentistry',
                'district' => 'Dhaka',
                'area' => 'Mohammadpur',
                'qualification' => 'BDS, MS (Dental Surgery)',
                'experience_years' => 8,
                'consultation_fee' => 600,
                'clinic_name' => 'Smile Dental Care',
                'clinic_address' => 'Mohammadpur, Dhaka',
                'gender' => 'female',
                'phone' => '01710000006',
                'profile_image' => 'assets/img/doctors/doctor-thumb-06.jpg',
            ],
            [
                'name' => 'Dr. Jahangir Alam',
                'email' => 'dr.jahangir@example.com',
                'speciality' => 'Urology',
                'district' => 'Dhaka',
                'area' => 'Motijheel',
                'qualification' => 'MBBS, MS (Urology)',
                'experience_years' => 16,
                'consultation_fee' => 1500,
                'clinic_name' => 'Kidney & Urology Center',
                'clinic_address' => 'Motijheel, Dhaka',
                'gender' => 'male',
                'phone' => '01710000007',
                'profile_image' => 'assets/img/doctors/doctor-thumb-07.jpg',
            ],
            [
                'name' => 'Dr. Salma Khatun',
                'email' => 'dr.salma@example.com',
                'speciality' => 'Gynecology',
                'district' => 'Gazipur',
                'area' => 'Gazipur Sadar',
                'qualification' => 'MBBS, DGO',
                'experience_years' => 9,
                'consultation_fee' => 800,
                'clinic_name' => 'Mother & Child Hospital',
                'clinic_address' => 'Tongi, Gazipur',
                'gender' => 'female',
                'phone' => '01710000008',
                'profile_image' => 'assets/img/doctors/doctor-thumb-08.jpg',
            ],
        ];

        foreach ($doctors as $index => $data) {
            $speciality = Speciality::where('name', $data['speciality'])->first();
            $district = District::where('name', $data['district'])->first();
            $area = $district
                ? Area::where('district_id', $district->id)->where('name', $data['area'])->first()
                : null;

            if (!$speciality || !$district) {
                continue;
            }

            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password123'),
                    'role' => 'doctor',
                ]
            );

            Doctor::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'speciality_id' => $speciality->id,
                    'district_id' => $district->id,
                    'area_id' => $area?->id,
                    'phone' => $data['phone'],
                    'gender' => $data['gender'],
                    'date_of_birth' => now()->subYears(30 + $index)->toDateString(),
                    'qualification' => $data['qualification'],
                    'registration_number' => 'REG-' . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
                    'registration_date' => now()->subYears(5)->toDateString(),
                    'bio' => 'Experienced ' . $data['speciality'] . ' specialist with ' . $data['experience_years'] . ' years of practice.',
                    'clinic_name' => $data['clinic_name'],
                    'clinic_address' => $data['clinic_address'],
                    'consultation_fee' => $data['consultation_fee'],
                    'online_consultation' => true,
                    'online_fee' => max(0, $data['consultation_fee'] - 200),
                    'home_visit' => true,
                    'home_visit_fee' => $data['consultation_fee'] + 300,
                    'experience_years' => $data['experience_years'],
                    'profile_image' => $data['profile_image'],
                    'status' => 'approved',
                    'is_featured' => $index < 4,
                    'is_verified' => true,
                    'verified_at' => now(),
                ]
            );
        }

        $this->command->info('Doctors seeded with district, area, and speciality mapping.');
    }
}

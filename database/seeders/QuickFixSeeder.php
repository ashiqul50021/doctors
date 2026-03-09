<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Speciality;
use App\Models\Schedule;

class QuickFixSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Activate Specialities
        $speciality = Speciality::first();
        if ($speciality) {
            $speciality->update(['is_active' => true]);
            $this->command->info('Speciality activated: ' . $speciality->name);
        } else {
            $speciality = Speciality::create([
                'name' => 'Cardiology',
                'slug' => 'cardiology',
                'description' => 'Heart specialist',
                'is_active' => true,
            ]);
            $this->command->info('Created speciality: Cardiology');
        }

        // 2. Create Doctor User
        $user = User::where('email', 'doctor@example.com')->first();
        if (!$user) {
            $user = User::create([
                'name' => 'Dr. Darren Elder',
                'email' => 'doctor@example.com',
                'password' => Hash::make('password'),
            ]);
        }

        // 3. Create Doctor Profile
        $doctor = Doctor::where('user_id', $user->id)->first();
        if (!$doctor) {
            $doctor = Doctor::create([
                'user_id' => $user->id,
                'speciality_id' => $speciality->id,
                'qualification' => 'BDS, MDS - Oral & Maxillofacial Surgery',
                'experience_years' => 15,
                'clinic_name' => 'Georgia Dental Clinic',
                'clinic_address' => '123, Washington Ave, Manchester',
                'consultation_fee' => 150,
                'status' => 'approved',
            ]);
            $this->command->info('Doctor profile created and approved.');
        } else {
            $doctor->update(['status' => 'approved', 'speciality_id' => $speciality->id]);
            $this->command->info('Doctor profile updated to approved.');
        }

        // 5. Create Product Categories
        if (\App\Models\ProductCategory::count() == 0) {
            \App\Models\ProductCategory::create([
                'name' => 'Healthcare',
                'slug' => 'healthcare',
                'description' => 'General healthcare products',
                'is_active' => true,
            ]);
            \App\Models\ProductCategory::create([
                 'name' => 'Skincare',
                 'slug' => 'skincare',
                 'description' => 'Skin care and beauty',
                 'is_active' => true,
            ]);
             \App\Models\ProductCategory::create([
                 'name' => 'Wellness',
                 'slug' => 'wellness',
                 'description' => 'Wellness and supplements',
                 'is_active' => true,
            ]);
            $this->command->info('Product Categories created.');
        }

        // 4. Create Schedule (for today and tomorrow)
        Schedule::create([
            'doctor_id' => $doctor->id,
            'day' => strtolower(now()->format('l')), // Today
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'slot_duration' => 60,
            'is_available' => true,
        ]);

        Schedule::create([
            'doctor_id' => $doctor->id,
            'day' => strtolower(now()->addDay()->format('l')), // Tomorrow
            'start_time' => '10:00:00',
            'end_time' => '18:00:00',
            'slot_duration' => 60,
            'is_available' => true,
        ]);

        $this->command->info('Doctor schedules created.');
    }
}

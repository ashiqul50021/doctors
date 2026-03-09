<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\District;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds - All 64 Bangladesh Districts
     */
    public function run(): void
    {
        $districts = [
            ['name' => 'Dhaka', 'bn_name' => 'ঢাকা'],
            ['name' => 'Gazipur', 'bn_name' => 'গাজীপুর'],
            ['name' => 'Narayanganj', 'bn_name' => 'নারায়ণগঞ্জ'],
            ['name' => 'Tangail', 'bn_name' => 'টাঙ্গাইল'],
            ['name' => 'Kishoreganj', 'bn_name' => 'কিশোরগঞ্জ'],
            ['name' => 'Manikganj', 'bn_name' => 'মানিকগঞ্জ'],
            ['name' => 'Munshiganj', 'bn_name' => 'মুন্সিগঞ্জ'],
            ['name' => 'Narsingdi', 'bn_name' => 'নরসিংদী'],
            ['name' => 'Faridpur', 'bn_name' => 'ফরিদপুর'],
            ['name' => 'Gopalganj', 'bn_name' => 'গোপালগঞ্জ'],
            ['name' => 'Madaripur', 'bn_name' => 'মাদারীপুর'],
            ['name' => 'Rajbari', 'bn_name' => 'রাজবাড়ী'],
            ['name' => 'Shariatpur', 'bn_name' => 'শরীয়তপুর'],
            ['name' => 'Chattogram', 'bn_name' => 'চট্টগ্রাম'],
            ['name' => 'Coxs Bazar', 'bn_name' => 'কক্সবাজার'],
            ['name' => 'Comilla', 'bn_name' => 'কুমিল্লা'],
            ['name' => 'Feni', 'bn_name' => 'ফেনী'],
            ['name' => 'Brahmanbaria', 'bn_name' => 'ব্রাহ্মণবাড়িয়া'],
            ['name' => 'Chandpur', 'bn_name' => 'চাঁদপুর'],
            ['name' => 'Lakshmipur', 'bn_name' => 'লক্ষ্মীপুর'],
            ['name' => 'Noakhali', 'bn_name' => 'নোয়াখালী'],
            ['name' => 'Rangamati', 'bn_name' => 'রাঙ্গামাটি'],
            ['name' => 'Khagrachhari', 'bn_name' => 'খাগড়াছড়ি'],
            ['name' => 'Bandarban', 'bn_name' => 'বান্দরবান'],
            ['name' => 'Rajshahi', 'bn_name' => 'রাজশাহী'],
            ['name' => 'Chapainawabganj', 'bn_name' => 'চাঁপাইনবাবগঞ্জ'],
            ['name' => 'Naogaon', 'bn_name' => 'নওগাঁ'],
            ['name' => 'Natore', 'bn_name' => 'নাটোর'],
            ['name' => 'Nawabganj', 'bn_name' => 'নওয়াবগঞ্জ'],
            ['name' => 'Pabna', 'bn_name' => 'পাবনা'],
            ['name' => 'Sirajganj', 'bn_name' => 'সিরাজগঞ্জ'],
            ['name' => 'Bogra', 'bn_name' => 'বগুড়া'],
            ['name' => 'Joypurhat', 'bn_name' => 'জয়পুরহাট'],
            ['name' => 'Khulna', 'bn_name' => 'খুলনা'],
            ['name' => 'Bagerhat', 'bn_name' => 'বাগেরহাট'],
            ['name' => 'Satkhira', 'bn_name' => 'সাতক্ষীরা'],
            ['name' => 'Jessore', 'bn_name' => 'যশোর'],
            ['name' => 'Magura', 'bn_name' => 'মাগুরা'],
            ['name' => 'Jhenaidah', 'bn_name' => 'ঝিনাইদহ'],
            ['name' => 'Narail', 'bn_name' => 'নড়াইল'],
            ['name' => 'Kushtia', 'bn_name' => 'কুষ্টিয়া'],
            ['name' => 'Chuadanga', 'bn_name' => 'চুয়াডাঙ্গা'],
            ['name' => 'Meherpur', 'bn_name' => 'মেহেরপুর'],
            ['name' => 'Sylhet', 'bn_name' => 'সিলেট'],
            ['name' => 'Moulvibazar', 'bn_name' => 'মৌলভীবাজার'],
            ['name' => 'Habiganj', 'bn_name' => 'হবিগঞ্জ'],
            ['name' => 'Sunamganj', 'bn_name' => 'সুনামগঞ্জ'],
            ['name' => 'Rangpur', 'bn_name' => 'রংপুর'],
            ['name' => 'Dinajpur', 'bn_name' => 'দিনাজপুর'],
            ['name' => 'Kurigram', 'bn_name' => 'কুড়িগ্রাম'],
            ['name' => 'Lalmonirhat', 'bn_name' => 'লালমনিরহাট'],
            ['name' => 'Nilphamari', 'bn_name' => 'নীলফামারী'],
            ['name' => 'Gaibandha', 'bn_name' => 'গাইবান্ধা'],
            ['name' => 'Thakurgaon', 'bn_name' => 'ঠাকুরগাঁও'],
            ['name' => 'Panchagarh', 'bn_name' => 'পঞ্চগড়'],
            ['name' => 'Barishal', 'bn_name' => 'বরিশাল'],
            ['name' => 'Bhola', 'bn_name' => 'ভোলা'],
            ['name' => 'Jhalokati', 'bn_name' => 'ঝালকাঠি'],
            ['name' => 'Patuakhali', 'bn_name' => 'পটুয়াখালী'],
            ['name' => 'Pirojpur', 'bn_name' => 'পিরোজপুর'],
            ['name' => 'Barguna', 'bn_name' => 'বরগুনা'],
            ['name' => 'Mymensingh', 'bn_name' => 'ময়মনসিংহ'],
            ['name' => 'Jamalpur', 'bn_name' => 'জামালপুর'],
            ['name' => 'Netrokona', 'bn_name' => 'নেত্রকোণা'],
            ['name' => 'Sherpur', 'bn_name' => 'শেরপুর'],
        ];

        foreach ($districts as $district) {
            District::firstOrCreate(['name' => $district['name']], $district);
        }
    }
}

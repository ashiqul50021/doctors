<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\District;
use App\Models\Area;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds - Major areas/upazilas for each district
     */
    public function run(): void
    {
        $districtAreas = [
            'Dhaka' => [
                ['name' => 'Dhanmondi', 'bn_name' => 'ধানমন্ডি'],
                ['name' => 'Gulshan', 'bn_name' => 'গুলশান'],
                ['name' => 'Banani', 'bn_name' => 'বনানী'],
                ['name' => 'Uttara', 'bn_name' => 'উত্তরা'],
                ['name' => 'Mirpur', 'bn_name' => 'মিরপুর'],
                ['name' => 'Mohammadpur', 'bn_name' => 'মোহাম্মদপুর'],
                ['name' => 'Motijheel', 'bn_name' => 'মতিঝিল'],
                ['name' => 'Badda', 'bn_name' => 'বাড্ডা'],
                ['name' => 'Tejgaon', 'bn_name' => 'তেজগাঁও'],
                ['name' => 'Savar', 'bn_name' => 'সাভার'],
                ['name' => 'Keraniganj', 'bn_name' => 'কেরানীগঞ্জ'],
                ['name' => 'Dhamrai', 'bn_name' => 'ধামরাই'],
                ['name' => 'Dohar', 'bn_name' => 'দোহার'],
                ['name' => 'Nawabganj', 'bn_name' => 'নবাবগঞ্জ'],
            ],
            'Gazipur' => [
                ['name' => 'Gazipur Sadar', 'bn_name' => 'গাজীপুর সদর'],
                ['name' => 'Kaliakair', 'bn_name' => 'কালিয়াকৈর'],
                ['name' => 'Kaliganj', 'bn_name' => 'কালীগঞ্জ'],
                ['name' => 'Kapasia', 'bn_name' => 'কাপাসিয়া'],
                ['name' => 'Sreepur', 'bn_name' => 'শ্রীপুর'],
            ],
            'Chattogram' => [
                ['name' => 'Chattogram Sadar', 'bn_name' => 'চট্টগ্রাম সদর'],
                ['name' => 'Agrabad', 'bn_name' => 'আগ্রাবাদ'],
                ['name' => 'Pahartali', 'bn_name' => 'পাহাড়তলী'],
                ['name' => 'Halishahar', 'bn_name' => 'হালিশহর'],
                ['name' => 'Patenga', 'bn_name' => 'পতেঙ্গা'],
                ['name' => 'Boalkhali', 'bn_name' => 'বোয়ালখালী'],
                ['name' => 'Patiya', 'bn_name' => 'পটিয়া'],
                ['name' => 'Sitakunda', 'bn_name' => 'সীতাকুণ্ড'],
                ['name' => 'Mirsharai', 'bn_name' => 'মিরসরাই'],
            ],
            'Sylhet' => [
                ['name' => 'Sylhet Sadar', 'bn_name' => 'সিলেট সদর'],
                ['name' => 'Beanibazar', 'bn_name' => 'বিয়ানীবাজার'],
                ['name' => 'Golapganj', 'bn_name' => 'গোলাপগঞ্জ'],
                ['name' => 'Jaintiapur', 'bn_name' => 'জৈন্তাপুর'],
                ['name' => 'Kanaighat', 'bn_name' => 'কানাইঘাট'],
            ],
            'Rajshahi' => [
                ['name' => 'Rajshahi Sadar', 'bn_name' => 'রাজশাহী সদর'],
                ['name' => 'Paba', 'bn_name' => 'পবা'],
                ['name' => 'Durgapur', 'bn_name' => 'দুর্গাপুর'],
                ['name' => 'Mohanpur', 'bn_name' => 'মোহনপুর'],
                ['name' => 'Bagmara', 'bn_name' => 'বাগমারা'],
            ],
            'Khulna' => [
                ['name' => 'Khulna Sadar', 'bn_name' => 'খুলনা সদর'],
                ['name' => 'Daulatpur', 'bn_name' => 'দৌলতপুর'],
                ['name' => 'Dumuria', 'bn_name' => 'ডুমুরিয়া'],
                ['name' => 'Batiaghata', 'bn_name' => 'বটিয়াঘাটা'],
                ['name' => 'Rupsha', 'bn_name' => 'রূপসা'],
            ],
            'Rangpur' => [
                ['name' => 'Rangpur Sadar', 'bn_name' => 'রংপুর সদর'],
                ['name' => 'Badarganj', 'bn_name' => 'বদরগঞ্জ'],
                ['name' => 'Gangachara', 'bn_name' => 'গঙ্গাচড়া'],
                ['name' => 'Kaunia', 'bn_name' => 'কাউনিয়া'],
                ['name' => 'Pirganj', 'bn_name' => 'পীরগঞ্জ'],
            ],
            'Barishal' => [
                ['name' => 'Barishal Sadar', 'bn_name' => 'বরিশাল সদর'],
                ['name' => 'Bakerganj', 'bn_name' => 'বাকেরগঞ্জ'],
                ['name' => 'Banaripara', 'bn_name' => 'বানারীপাড়া'],
                ['name' => 'Gournadi', 'bn_name' => 'গৌরনদী'],
                ['name' => 'Mehendiganj', 'bn_name' => 'মেহেন্দিগঞ্জ'],
            ],
            'Narayanganj' => [
                ['name' => 'Narayanganj Sadar', 'bn_name' => 'নারায়ণগঞ্জ সদর'],
                ['name' => 'Araihazar', 'bn_name' => 'আড়াইহাজার'],
                ['name' => 'Rupganj', 'bn_name' => 'রূপগঞ্জ'],
                ['name' => 'Sonargaon', 'bn_name' => 'সোনারগাঁও'],
                ['name' => 'Bandar', 'bn_name' => 'বন্দর'],
            ],
            'Comilla' => [
                ['name' => 'Comilla Sadar', 'bn_name' => 'কুমিল্লা সদর'],
                ['name' => 'Debidwar', 'bn_name' => 'দেবিদ্বার'],
                ['name' => 'Barura', 'bn_name' => 'বরুড়া'],
                ['name' => 'Chandina', 'bn_name' => 'চান্দিনা'],
                ['name' => 'Chauddagram', 'bn_name' => 'চৌদ্দগ্রাম'],
            ],
            'Mymensingh' => [
                ['name' => 'Mymensingh Sadar', 'bn_name' => 'ময়মনসিংহ সদর'],
                ['name' => 'Trishal', 'bn_name' => 'ত্রিশাল'],
                ['name' => 'Bhaluka', 'bn_name' => 'ভালুকা'],
                ['name' => 'Muktagacha', 'bn_name' => 'মুক্তাগাছা'],
                ['name' => 'Fulbaria', 'bn_name' => 'ফুলবাড়িয়া'],
            ],
        ];

        foreach ($districtAreas as $districtName => $areas) {
            $district = District::where('name', $districtName)->first();
            if ($district) {
                foreach ($areas as $area) {
                    Area::firstOrCreate(
                        ['district_id' => $district->id, 'name' => $area['name']],
                        array_merge($area, ['district_id' => $district->id])
                    );
                }
            }
        }
    }
}

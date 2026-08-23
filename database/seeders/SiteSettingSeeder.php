<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SiteSetting;

class SiteSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General Settings
            ['key' => 'site_name', 'value' => 'abcSheba', 'type' => 'text', 'group' => 'general'],
            ['key' => 'site_tagline', 'value' => 'Doctor Appointment Booking', 'type' => 'text', 'group' => 'general'],
            ['key' => 'logo', 'value' => 'assets/img/logo.png', 'type' => 'image', 'group' => 'general'],
            ['key' => 'favicon', 'value' => 'assets/img/favicon.png', 'type' => 'image', 'group' => 'general'],
            ['key' => 'footer_logo', 'value' => 'assets/img/footer-logo.png', 'type' => 'image', 'group' => 'general'],

            // Contact Settings
            ['key' => 'contact_email', 'value' => 'info@abcsheba.com', 'type' => 'text', 'group' => 'contact'],
            ['key' => 'contact_phone', 'value' => '+1 66589 14356', 'type' => 'text', 'group' => 'contact'],
            ['key' => 'contact_address', 'value' => '3556 Beech Street, San Francisco, CA 94108', 'type' => 'text', 'group' => 'contact'],

            // Social Links
            ['key' => 'facebook_url', 'value' => 'https://facebook.com/abcsheba', 'type' => 'text', 'group' => 'social'],
            ['key' => 'twitter_url', 'value' => 'https://twitter.com/abcsheba', 'type' => 'text', 'group' => 'social'],
            ['key' => 'instagram_url', 'value' => 'https://instagram.com/abcsheba', 'type' => 'text', 'group' => 'social'],
            ['key' => 'linkedin_url', 'value' => 'https://linkedin.com/company/abcsheba', 'type' => 'text', 'group' => 'social'],

            // Banner Settings
            ['key' => 'banner_title', 'value' => 'Discover Health: Find Your Trusted Doctors Today', 'type' => 'text', 'group' => 'banner'],
            ['key' => 'banner_subtitle', 'value' => 'Connect with 500+ expert doctors across 50+ specialties', 'type' => 'text', 'group' => 'banner'],
            ['key' => 'banner_image', 'value' => 'assets/img/doctors/doctor-banner.png', 'type' => 'image', 'group' => 'banner'],
            ['key' => 'banner_stats_text', 'value' => '5K+ Appointments', 'type' => 'text', 'group' => 'banner'],
            ['key' => 'banner_rating', 'value' => '5.0', 'type' => 'text', 'group' => 'banner'],

            // E-commerce & Landing Page Settings
            ['key' => 'shipping_inside_dhaka', 'value' => '80', 'type' => 'text', 'group' => 'ecommerce'],
            ['key' => 'shipping_outside_dhaka', 'value' => '150', 'type' => 'text', 'group' => 'ecommerce'],
            ['key' => 'helpline_number', 'value' => '01915714222', 'type' => 'text', 'group' => 'general'],
            ['key' => 'landing_countdown_hours', 'value' => '3', 'type' => 'text', 'group' => 'general'],
            ['key' => 'landing_countdown_title', 'value' => 'আজকের বিশেষ ছাড় অফার!', 'type' => 'text', 'group' => 'general'],
            ['key' => 'landing_countdown_subtitle', 'value' => 'অফারটি শেষ হতে আর মাত্র সময় বাকি আছে:', 'type' => 'text', 'group' => 'general'],
            ['key' => 'landing_trust_title', 'value' => 'আমাদের থেকে কেন সংগ্রহ করবেন?', 'type' => 'text', 'group' => 'general'],
            ['key' => 'landing_feature_1_title', 'value' => '১০০% আসল প্রোডাক্ট (100% Original)', 'type' => 'text', 'group' => 'general'],
            ['key' => 'landing_feature_1_desc', 'value' => 'আমরা কোনো নকল পণ্য বিক্রি করি না। সরাসরি ভেরিফাইড ব্র্যান্ড ও ইমপোর্টার থেকে পণ্য সংগ্রহ করি।', 'type' => 'text', 'group' => 'general'],
            ['key' => 'landing_feature_2_title', 'value' => 'নিরাপদ প্যাকেজিং ও ডেলিভারি (Secure Shipping)', 'type' => 'text', 'group' => 'general'],
            ['key' => 'landing_feature_2_desc', 'value' => 'আপনার পণ্যটি যাতে অক্ষত অবস্থায় পৌঁছায়, সেজন্য আমাদের রয়েছে নিখুঁত বাবল-র‍্যাপড প্যাকেজিং ব্যবস্থা।', 'type' => 'text', 'group' => 'general'],
            ['key' => 'landing_feature_3_title', 'value' => 'সহজ রিটার্ন পলিসি (Easy Returns)', 'type' => 'text', 'group' => 'general'],
            ['key' => 'landing_feature_3_desc', 'value' => 'পণ্য গ্রহণের পর কোনো ত্রুটি পেলে ৭ দিনের মধ্যে আমাদের সাথে যোগাযোগ করে রিফান্ড বা এক্সচেঞ্জ করতে পারবেন।', 'type' => 'text', 'group' => 'general'],
            ['key' => 'landing_feature_4_title', 'value' => '২৪/৭ কাস্টমার সাপোর্ট (Dedicated Hotline)', 'type' => 'text', 'group' => 'general'],
            ['key' => 'landing_feature_4_desc', 'value' => 'অর্ডার করার আগে বা পরে যেকোনো গাইডলাইনের জন্য আমাদের কাস্টমার কেয়ার হেল্পলাইন সর্বদা উন্মুক্ত।', 'type' => 'text', 'group' => 'general'],
        ];

        foreach ($settings as $setting) {
            SiteSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}

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
            ['key' => 'site_name', 'value' => 'Doccure', 'type' => 'text', 'group' => 'general'],
            ['key' => 'site_tagline', 'value' => 'Doctor Appointment Booking', 'type' => 'text', 'group' => 'general'],
            ['key' => 'logo', 'value' => 'assets/img/logo.png', 'type' => 'image', 'group' => 'general'],
            ['key' => 'favicon', 'value' => 'assets/img/favicon.png', 'type' => 'image', 'group' => 'general'],
            ['key' => 'footer_logo', 'value' => 'assets/img/footer-logo.png', 'type' => 'image', 'group' => 'general'],

            // Contact Settings
            ['key' => 'contact_email', 'value' => 'info@doccure.com', 'type' => 'text', 'group' => 'contact'],
            ['key' => 'contact_phone', 'value' => '+1 66589 14356', 'type' => 'text', 'group' => 'contact'],
            ['key' => 'contact_address', 'value' => '3556 Beech Street, San Francisco, CA 94108', 'type' => 'text', 'group' => 'contact'],

            // Social Links
            ['key' => 'facebook_url', 'value' => 'https://facebook.com/doccure', 'type' => 'text', 'group' => 'social'],
            ['key' => 'twitter_url', 'value' => 'https://twitter.com/doccure', 'type' => 'text', 'group' => 'social'],
            ['key' => 'instagram_url', 'value' => 'https://instagram.com/doccure', 'type' => 'text', 'group' => 'social'],
            ['key' => 'linkedin_url', 'value' => 'https://linkedin.com/company/doccure', 'type' => 'text', 'group' => 'social'],

            // Banner Settings
            ['key' => 'banner_title', 'value' => 'Discover Health: Find Your Trusted Doctors Today', 'type' => 'text', 'group' => 'banner'],
            ['key' => 'banner_subtitle', 'value' => 'Connect with 500+ expert doctors across 50+ specialties', 'type' => 'text', 'group' => 'banner'],
            ['key' => 'banner_image', 'value' => 'assets/img/doctors/doctor-banner.png', 'type' => 'image', 'group' => 'banner'],
            ['key' => 'banner_stats_text', 'value' => '5K+ Appointments', 'type' => 'text', 'group' => 'banner'],
            ['key' => 'banner_rating', 'value' => '5.0', 'type' => 'text', 'group' => 'banner'],
        ];

        foreach ($settings as $setting) {
            SiteSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}

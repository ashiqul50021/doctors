<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use App\Services\ImageService;

class SiteSettingController extends Controller
{
    /**
     * Display site settings page
     */
    public function index()
    {
        $settings = SiteSetting::getAllSettings();
        $generalSettings = SiteSetting::getByGroup('general');
        $contactSettings = SiteSetting::getByGroup('contact');
        $socialSettings = SiteSetting::getByGroup('social');
        $ecommerceSettings = SiteSetting::getByGroup('ecommerce');
        $bannerSettings = SiteSetting::getByGroup('banner');

        return view('admin.site-settings.index', compact(
            'settings',
            'generalSettings',
            'contactSettings',
            'socialSettings',
            'ecommerceSettings',
            'bannerSettings'
        ));
    }

    /**
     * Display banner settings page (dedicated)
     */
    public function bannerIndex()
    {
        $bannerSettings = SiteSetting::getByGroup('banner');
        return view('admin.banner-settings.index', compact('bannerSettings'));
    }

    /**
     * Update site settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'site_name' => 'nullable|string|max:255',
            'site_tagline' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string|max:50',
            'contact_address' => 'nullable|string',
            'facebook_url' => 'nullable|url',
            'twitter_url' => 'nullable|url',
            'instagram_url' => 'nullable|url',
            'linkedin_url' => 'nullable|url',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:20480',
            'favicon' => 'nullable|image|mimes:png,ico,webp|max:1024',
            'footer_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:20480',
            'shipping_inside_dhaka' => 'nullable|numeric|min:0',
            'shipping_outside_dhaka' => 'nullable|numeric|min:0',
        ]);

        // Text settings
        $textSettings = [
            'site_name',
            'site_tagline',
            'contact_email',
            'contact_phone',
            'contact_address',
            'facebook_url',
            'twitter_url',
            'instagram_url',
            'linkedin_url',
            'shipping_inside_dhaka',
            'shipping_outside_dhaka'
        ];

        foreach ($textSettings as $key) {
            if ($request->has($key)) {
                $group = $this->getGroupForKey($key);
                SiteSetting::set($key, $request->$key, 'text', $group);
            }
        }

        // Image settings
        $imageSettings = ['logo', 'favicon', 'footer_logo'];
        foreach ($imageSettings as $key) {
            if ($request->hasFile($key)) {
                // Delete old image
                $oldSetting = SiteSetting::where('key', $key)->first();
                if ($oldSetting && $oldSetting->value) {
                    ImageService::delete($oldSetting->value);
                }

                $path = ImageService::upload($request->file($key), 'settings');
                SiteSetting::set($key, $path, 'image', 'general');
            }
        }

        return redirect()->route('admin.site-settings.index')->with('success', 'Settings updated successfully!');
    }

    /**
     * Update banner settings
     */
    public function updateBanner(Request $request)
    {
        $request->validate([
            'banner_title' => 'nullable|string|max:500',
            'banner_subtitle' => 'nullable|string|max:1000',
            'banner_feature_1' => 'nullable|string|max:100',
            'banner_feature_2' => 'nullable|string|max:100',
            'banner_feature_3' => 'nullable|string|max:100',
            'banner_stats_text' => 'nullable|string|max:100',
            'banner_rating' => 'nullable|string|max:10',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:20480',
        ]);

        // Text settings
        $bannerTextSettings = ['banner_title', 'banner_subtitle', 'banner_feature_1', 'banner_feature_2', 'banner_feature_3', 'banner_stats_text', 'banner_rating'];
        foreach ($bannerTextSettings as $key) {
            if ($request->has($key)) {
                SiteSetting::set($key, $request->$key, 'text', 'banner');
            }
        }

        // Banner image
        if ($request->hasFile('banner_image')) {
            $oldSetting = SiteSetting::where('key', 'banner_image')->first();
            if ($oldSetting && $oldSetting->value) {
                ImageService::delete($oldSetting->value);
            }

            $path = ImageService::upload($request->file('banner_image'), 'settings');
            SiteSetting::set('banner_image', $path, 'image', 'banner');
        }

        return back()->with('success', 'Banner settings updated successfully!');
    }

    /**
     * Get group for a setting key
     */
    private function getGroupForKey($key)
    {
        if (str_starts_with($key, 'contact_'))
            return 'contact';
        if (str_ends_with($key, '_url'))
            return 'social';
        if (str_starts_with($key, 'banner_'))
            return 'banner';
        if (str_starts_with($key, 'shipping_'))
            return 'ecommerce';
        return 'general';
    }
}

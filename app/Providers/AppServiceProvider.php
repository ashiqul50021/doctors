<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Models\SiteSetting;
use App\Models\Menu;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Pagination\Paginator::useBootstrap();

        // Share global data with all views
        View::composer('*', function ($view) {
            // Check if tables exist before querying
            if (Schema::hasTable('site_settings')) {
                $view->with('siteSettings', SiteSetting::getAllSettings());
            }

            if (Schema::hasTable('menus')) {
                $view->with('mainMenu', Menu::getMenus('main'));
            }
        });
    }
}

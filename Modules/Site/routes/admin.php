<?php

use Illuminate\Support\Facades\Route;
use Modules\Site\Http\Controllers\SiteController;

/*
|--------------------------------------------------------------------------
| Site Admin Routes
|--------------------------------------------------------------------------
*/

// Admin Auth Routes (Guest)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [App\Http\Controllers\Admin\AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\Admin\AuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('logout');
});

// Admin Protected Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('dashboard');

    // Admin Schedule Management
    Route::get('/doctors/{doctor}/schedule', [App\Http\Controllers\Admin\DoctorScheduleController::class, 'edit'])->name('doctors.schedule');
    Route::post('/doctors/{doctor}/schedule', [App\Http\Controllers\Admin\DoctorScheduleController::class, 'update'])->name('doctors.schedule.update');
    Route::delete('/doctors/schedule/{id}', [App\Http\Controllers\Admin\DoctorScheduleController::class, 'destroy'])->name('doctors.schedule.destroy');

    // Order Management
    Route::get('/orders', [App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{id}/status', [App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.status');

    // Coupon Management
    Route::resource('coupons', App\Http\Controllers\Admin\CouponController::class);

    // Health Packages Management
    Route::resource('health-packages', App\Http\Controllers\Admin\HealthPackageController::class);

    Route::get('/patients', [App\Http\Controllers\AdminController::class, 'patients'])->name('patients');
    Route::get('/appointments', [App\Http\Controllers\AdminController::class, 'appointments'])->name('appointments');
    Route::get('/reviews', [App\Http\Controllers\AdminController::class, 'reviews'])->name('reviews');
    Route::get('/transactions', [App\Http\Controllers\AdminController::class, 'transactions'])->name('transactions');
    Route::get('/invoice-report', [App\Http\Controllers\AdminController::class, 'reports'])->name('invoice.report');

    // Site Settings
    Route::get('/site-settings', [App\Http\Controllers\Admin\SiteSettingController::class, 'index'])->name('site-settings.index');
    Route::put('/site-settings', [App\Http\Controllers\Admin\SiteSettingController::class, 'update'])->name('site-settings.update');
    Route::put('/site-settings/banner', [App\Http\Controllers\Admin\SiteSettingController::class, 'updateBanner'])->name('site-settings.update-banner');

    // Banners
    Route::resource('banners', App\Http\Controllers\Admin\BannerController::class);

    // Banner Settings
    Route::get('/banner-settings', [App\Http\Controllers\Admin\SiteSettingController::class, 'bannerIndex'])->name('banner-settings.index');
    Route::put('/banner-settings', [App\Http\Controllers\Admin\SiteSettingController::class, 'updateBanner'])->name('banner-settings.update');

    // Menu Manager
    Route::get('menus/{menu}/delete', [App\Http\Controllers\Admin\MenuController::class, 'delete'])->name('menus.delete');
    Route::resource('menus', App\Http\Controllers\Admin\MenuController::class);

    // Profile
    Route::get('/profile', [App\Http\Controllers\AdminController::class, 'profile'])->name('profile');
    Route::put('/profile', [App\Http\Controllers\AdminController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [App\Http\Controllers\AdminController::class, 'updatePassword'])->name('profile.password.update');
});

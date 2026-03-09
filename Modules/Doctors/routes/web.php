<?php

use Illuminate\Support\Facades\Route;
use Modules\Doctors\Http\Controllers\Frontend\DoctorController;
use Modules\Doctors\Http\Controllers\Frontend\BookingController;
use Modules\Doctors\Http\Controllers\Frontend\SearchController;
use Modules\Doctors\Http\Controllers\Backend\DoctorController as AdminDoctorController;
use Modules\Doctors\Http\Controllers\Backend\SpecialityController as AdminSpecialityController;

/*
|--------------------------------------------------------------------------
| Doctors Frontend Routes
|--------------------------------------------------------------------------
*/

Route::name('doctors.')->group(function () {
    // Search
    Route::get('/doctors', [SearchController::class, 'index'])->name('search');
    Route::redirect('/search', '/doctors', 301);

    // Doctor Profile
    Route::get('/doctor-profile/{id}', [DoctorController::class, 'show'])->name('profile');

    // Booking
    Route::get('/booking/{doctor_id}', [BookingController::class, 'index'])->name('booking');
    Route::post('/booking/{doctor_id}', [BookingController::class, 'bookAppointment'])->name('booking.submit');
    Route::get('/checkout', [BookingController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [BookingController::class, 'processPayment'])->name('booking.payment');
    Route::view('/booking-success', 'doctors::frontend.booking-success')->name('booking.success');

    // Doctor Dashboard & Settings
    Route::middleware(['auth', 'role:doctor'])->group(function () {
        // Profile Settings (always accessible)
        Route::get('/doctor-profile-settings', [\App\Http\Controllers\Doctor\DashboardController::class, 'profileSettings'])->name('profile.settings');
        Route::post('/doctor-profile-settings', [\App\Http\Controllers\Doctor\DashboardController::class, 'updateProfileSettings'])->name('profile.settings.update');

        Route::middleware('doctor.profile.completed')->group(function () {
            Route::get('/doctor-dashboard', [\App\Http\Controllers\Doctor\DashboardController::class, 'index'])->name('dashboard');
            Route::post('/appointment/accept/{id}', [\App\Http\Controllers\Doctor\DashboardController::class, 'acceptAppointment'])->name('appointment.accept');
            Route::post('/appointment/cancel/{id}', [\App\Http\Controllers\Doctor\DashboardController::class, 'cancelAppointment'])->name('appointment.cancel');

            // Change Password
            Route::get('/doctor-change-password', [\App\Http\Controllers\Doctor\DashboardController::class, 'changePassword'])->name('change.password');
            Route::post('/doctor-change-password', [\App\Http\Controllers\Doctor\DashboardController::class, 'updatePassword'])->name('change.password.update');

            // Appointments
            Route::get('/appointments', [\App\Http\Controllers\Doctor\DashboardController::class, 'appointments'])->name('appointments');

            // My Patients
            Route::get('/my-patients', [\App\Http\Controllers\Doctor\DashboardController::class, 'myPatients'])->name('my.patients');

            // Reviews
            Route::get('/reviews', [\App\Http\Controllers\Doctor\DashboardController::class, 'reviews'])->name('reviews');

            // Invoices
            Route::get('/invoices', [\App\Http\Controllers\Doctor\DashboardController::class, 'invoices'])->name('invoices');

            // Social Media
            Route::get('/social-media', [\App\Http\Controllers\Doctor\DashboardController::class, 'socialMedia'])->name('social.media');
            Route::post('/social-media', [\App\Http\Controllers\Doctor\DashboardController::class, 'updateSocialMedia'])->name('social.media.update');

            // Schedule Timings
            Route::get('/schedule-timings', [\App\Http\Controllers\Doctor\ScheduleController::class, 'index'])->name('schedule.timings');
            Route::post('/schedule-timings', [\App\Http\Controllers\Doctor\ScheduleController::class, 'store'])->name('schedule.store');
            Route::delete('/schedule-timings/{id}', [\App\Http\Controllers\Doctor\ScheduleController::class, 'destroy'])->name('schedule.destroy');

            // Calendar
            Route::view('/calendar', 'frontend.calendar')->name('calendar');

            // Invoices & Billing Views
            Route::view('/invoice-view', 'frontend.invoice-view')->name('invoice.view');
            Route::view('/add-billing', 'frontend.add-billing')->name('add.billing');
            Route::view('/edit-billing', 'frontend.edit-billing')->name('edit.billing');

            // Prescriptions
            Route::view('/add-prescription', 'frontend.add-prescription')->name('add.prescription');
            Route::view('/edit-prescription', 'frontend.edit-prescription')->name('edit.prescription');
        });
    });
});

/*
|--------------------------------------------------------------------------
| Doctors Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('doctors.admin.')->group(function () {
    Route::resource('doctors', AdminDoctorController::class);
    Route::resource('specialities', AdminSpecialityController::class);
});

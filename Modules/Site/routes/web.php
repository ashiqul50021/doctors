<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Modules\Site\Http\Controllers\SiteController;

/*
|--------------------------------------------------------------------------
| Core / Site Frontend Routes
|--------------------------------------------------------------------------
*/

// Home
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Search & Booking
Route::get('/booking/{doctor_id}', [App\Http\Controllers\BookingController::class, 'index'])->name('booking');
Route::post('/booking/{doctor_id}', [App\Http\Controllers\BookingController::class, 'bookAppointment'])->name('booking.submit');
Route::get('/checkout', [App\Http\Controllers\BookingController::class, 'checkout'])->name('checkout');
Route::post('/checkout', [App\Http\Controllers\BookingController::class, 'processPayment'])->name('booking.payment');
Route::view('/booking-success', 'frontend.booking-success')->name('booking.success');

// Chat & Calls
Route::view('/chat', 'frontend.chat')->name('chat');
Route::view('/chat-doctor', 'frontend.chat-doctor')->name('chat.doctor');
Route::view('/voice-call', 'frontend.voice-call')->name('voice.call');
Route::view('/video-call', 'frontend.video-call')->name('video.call');

// Auth Routes
Route::get('/patient/login', [App\Http\Controllers\AuthController::class, 'showLoginForm'])->name('patient.login');
Route::post('/patient/login', [App\Http\Controllers\AuthController::class, 'login'])->name('patient.login.submit');
Route::get('/doctor/login', [App\Http\Controllers\AuthController::class, 'showDoctorLoginForm'])->name('doctor.login');
Route::post('/doctor/login', [App\Http\Controllers\AuthController::class, 'doctorLogin'])->name('doctor.login.submit');

// Legacy login aliases (keep for compatibility with existing links/middleware)
Route::get('/login', [App\Http\Controllers\AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login'])->name('login.submit');
Route::get('/register', [App\Http\Controllers\AuthController::class, 'showPatientRegisterForm'])->name('register');
Route::post('/register', [App\Http\Controllers\AuthController::class, 'registerPatient'])->name('register.submit');
Route::get('/doctor-register', [App\Http\Controllers\AuthController::class, 'showDoctorRegisterForm'])->name('doctor.register');
Route::post('/doctor-register', [App\Http\Controllers\AuthController::class, 'registerDoctor'])->name('doctor.register.submit');
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');
Route::post('/heartbeat', function (Request $request) {
    $request->user()->forceFill([
        'last_seen_at' => now(),
    ])->saveQuietly();

    return response()->json(['ok' => true]);
})->middleware('auth')->name('heartbeat');
Route::get('/forgot-password', function () {
    return view('frontend.forgot-password');
})->name('forgot.password');

// Static Pages
Route::view('/components', 'frontend.components')->name('components');
Route::view('/blank-page', 'frontend.blank-page')->name('blank.page');
Route::view('/privacy-policy', 'frontend.privacy-policy')->name('privacy');
Route::view('/terms-condition', 'frontend.term-condition')->name('terms');

// Maintenance & Utility Routes (local + admin only)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/migrate', function () {
        abort_unless(app()->environment('local'), 404);
        \Illuminate\Support\Facades\Artisan::call('migrate');
        return 'Migration run successfully!';
    });

    Route::get('/migrate-fresh', function () {
        abort_unless(app()->environment('local'), 404);
        \Illuminate\Support\Facades\Artisan::call('migrate:fresh --seed');
        return 'Migration Fresh with Seed run successfully!';
    });

    Route::get('/link', function () {
        abort_unless(app()->environment('local'), 404);
        \Illuminate\Support\Facades\Artisan::call('storage:link');
        return 'Storage linked successfully!';
    });

    Route::get('/optimize-clear', function () {
        abort_unless(app()->environment('local'), 404);
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Artisan::call('route:clear');
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        return 'Optimization and Cache Cleared!';
    });

    Route::get('/composer-install', function () {
        abort_unless(app()->environment('local'), 404);
        set_time_limit(0);
        $output = shell_exec('cd ' . base_path() . ' && composer install 2>&1');
        return '<pre>' . $output . '</pre>';
    });

    Route::get('/composer-update', function () {
        abort_unless(app()->environment('local'), 404);
        set_time_limit(0);
        $output = shell_exec('cd ' . base_path() . ' && composer update 2>&1');
        return '<pre>' . $output . '</pre>';
    });
});

// API Routes for AJAX
Route::get('/api/areas/{district}', function (App\Models\District $district) {
    return response()->json($district->areas()->orderBy('name')->get());
})->name('api.areas');
Route::get('/api/doctors/filter', [App\Http\Controllers\HomeController::class, 'filterDoctors'])->name('api.doctors.filter');

/*
|--------------------------------------------------------------------------
| Patient Routes (Handled by Site Module)
|--------------------------------------------------------------------------
*/

// Patient Pages
Route::middleware(['auth', 'role:patient'])->name('patient.')->group(function () {
    Route::view('/patient-dashboard', 'frontend.patient-dashboard')->name('dashboard');
    Route::view('/patient-profile', 'frontend.patient-profile')->name('profile');
    Route::view('/profile-settings', 'frontend.profile-settings')->name('profile.settings');
    Route::view('/change-password', 'frontend.change-password')->name('change.password');
    Route::view('/favourites', 'frontend.favourites')->name('favourites');
});

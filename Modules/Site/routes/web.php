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

// Booking routes are handled by Modules/Doctors/routes/web.php

// Chat & Calls
Route::middleware(['auth'])->group(function () {
    Route::get('/chat', [\App\Http\Controllers\ChatController::class, 'index'])->name('chat');
    Route::get('/chat-doctor', [\App\Http\Controllers\ChatController::class, 'index'])->name('chat.doctor');
    Route::post('/chat/send', [\App\Http\Controllers\ChatController::class, 'sendMessage'])->name('chat.send');
});
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

// Google Calendar OAuth
Route::get('/google/callback', [App\Http\Controllers\GoogleAuthController::class, 'callback'])->name('google.callback');
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/google/connect', [App\Http\Controllers\GoogleAuthController::class, 'connect'])->name('google.connect');
    Route::get('/google/status', [App\Http\Controllers\GoogleAuthController::class, 'status'])->name('google.status');
});

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
    Route::get('/patient-dashboard', [\App\Http\Controllers\Patient\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/prescription/{id}', [\App\Http\Controllers\Doctor\PrescriptionController::class, 'show'])->name('prescription.view');
    Route::view('/patient-profile', 'frontend.patient-profile')->name('profile');
    Route::get('/profile-settings', [\App\Http\Controllers\Patient\DashboardController::class, 'profileSettings'])->name('profile.settings');
    Route::post('/profile-settings', [\App\Http\Controllers\Patient\DashboardController::class, 'updateProfileSettings'])->name('profile.settings.update');
    Route::get('/change-password', [\App\Http\Controllers\Patient\DashboardController::class, 'changePassword'])->name('change.password');
    Route::post('/change-password', [\App\Http\Controllers\Patient\DashboardController::class, 'updatePassword'])->name('change.password.update');
    Route::get('/favourites', [\App\Http\Controllers\Patient\DashboardController::class, 'favourites'])->name('favourites');
    Route::post('/favourite/toggle/{doctor_id}', [\App\Http\Controllers\Patient\DashboardController::class, 'toggleFavourite'])->name('favourite.toggle');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/invoice-view/{id}', [\App\Http\Controllers\Patient\DashboardController::class, 'viewInvoice'])->name('invoice.view');
});

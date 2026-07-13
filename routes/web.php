<?php

/*
|--------------------------------------------------------------------------
| Application Core Routes
|--------------------------------------------------------------------------
|
| The primary application routes have been modularized.
| Core web, auth, API, and static page routes are now found in 
| Modules/Site/routes/web.php
|
| Patient profile routes are inside __DIR__ . '/patient.php'
| Doctor backend dashboard routes are inside __DIR__ . '/doctor.php'
| Admin panel routing logic is handled in __DIR__ . '/admin.php'
|
*/

// Admin Routes
require __DIR__ . '/admin.php';

use App\Http\Controllers\SslCommerzPaymentController;

// SSLCommerz Routes
Route::post('/sslcommerz/success', [SslCommerzPaymentController::class, 'success']);
Route::post('/sslcommerz/fail', [SslCommerzPaymentController::class, 'fail']);
Route::post('/sslcommerz/cancel', [SslCommerzPaymentController::class, 'cancel']);
Route::post('/sslcommerz/ipn', [SslCommerzPaymentController::class, 'ipn']);

// Doctor & Agent Direct Profile Fallback Route (http://doctor.test/username)
use Modules\Doctors\Models\Doctor;
use Modules\Doctors\Http\Controllers\Frontend\DoctorController;
use Modules\Agents\Models\Agent;
use Modules\Agents\Http\Controllers\Frontend\AgentProfileController;

Route::fallback(function () {
    $slug = strtolower(request()->path());

    // If slug has slashes, it's not a single level slug (e.g. doctorname/appointments)
    if (str_contains($slug, '/')) {
        abort(404);
    }

    // 1. Check if slug belongs to a doctor
    $doctor = Doctor::where('slug', $slug)->first();
    if ($doctor) {
        return app(DoctorController::class)->show($doctor->slug);
    }

    // 2. Check if slug belongs to an agent
    $agent = Agent::where('slug', $slug)->first();
    if ($agent) {
        return app(AgentProfileController::class)->show($agent->slug);
    }

    abort(404);
});

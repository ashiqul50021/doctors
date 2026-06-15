<?php

use Illuminate\Support\Facades\Route;
use Modules\Agents\Http\Controllers\Frontend\AgentAuthController;
use Modules\Agents\Http\Controllers\Frontend\AgentDashboardController;
use Modules\Agents\Http\Controllers\Frontend\AgentBookingController;
use Modules\Agents\Http\Controllers\Frontend\AgentProductController;
use Modules\Agents\Http\Controllers\Frontend\AgentCourseController;
use Modules\Agents\Http\Controllers\Backend\AdminAgentController;

/*
|--------------------------------------------------------------------------
| Agent Auth Routes (Guest)
|--------------------------------------------------------------------------
*/
Route::middleware(['web', 'guest'])->prefix('agent')->name('agent.')->group(function () {
    Route::get('/login', [AgentAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AgentAuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [AgentAuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AgentAuthController::class, 'register'])->name('register.submit');
});

/*
|--------------------------------------------------------------------------
| Agent Dashboard & Protected Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['web', 'auth', 'role:agent'])->prefix('agent')->name('agent.')->group(function () {
    Route::get('/dashboard', [AgentDashboardController::class, 'index'])->name('dashboard');
    Route::get('/wallet', [AgentDashboardController::class, 'wallet'])->name('wallet');
    Route::post('/payout-request', [AgentDashboardController::class, 'payoutRequest'])->name('payout.request');
    Route::post('/profile-image/upload', [AgentDashboardController::class, 'uploadProfileImage'])->name('profile-image.upload');

    // Appointment Booking on behalf of patients
    Route::get('/book-appointment', [AgentBookingController::class, 'index'])->name('book-appointment');
    Route::get('/booking/{doctor_id}', [AgentBookingController::class, 'booking'])->name('booking');
    Route::post('/booking/{doctor_id}/submit', [AgentBookingController::class, 'submit'])->name('booking.submit');

    // Product ordering on behalf of customers (direct order)
    Route::get('/products', [AgentProductController::class, 'index'])->name('products');
    Route::post('/cart/add', [AgentProductController::class, 'addToCart'])->name('cart.add');
    Route::get('/cart', [AgentProductController::class, 'cart'])->name('cart');
    Route::post('/cart/update', [AgentProductController::class, 'updateCart'])->name('cart.update');
    Route::post('/cart/remove', [AgentProductController::class, 'removeFromCart'])->name('cart.remove');
    Route::get('/checkout', [AgentProductController::class, 'checkout'])->name('checkout');
    Route::post('/place-order', [AgentProductController::class, 'placeOrder'])->name('order.place');

    // Course selling/referring
    Route::get('/courses', [AgentCourseController::class, 'index'])->name('courses');
});

/*
|--------------------------------------------------------------------------
| Agent Admin Panel Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['web', 'auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('agents', AdminAgentController::class)->except(['show']);
    Route::get('/agent-payouts', [AdminAgentController::class, 'payoutsIndex'])->name('agents.payouts');
    Route::post('/agent-payouts/{id}/approve', [AdminAgentController::class, 'payoutsApprove'])->name('agents.payouts.approve');
    Route::post('/agent-payouts/{id}/reject', [AdminAgentController::class, 'payoutsReject'])->name('agents.payouts.reject');
});

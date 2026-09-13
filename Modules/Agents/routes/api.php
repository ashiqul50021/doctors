<?php

use Illuminate\Support\Facades\Route;
use Modules\Agents\Http\Controllers\Api\AgentAuthController;
use Modules\Agents\Http\Controllers\Api\AgentDashboardController;
use Modules\Agents\Http\Controllers\Api\AgentBookingApiController;
use Modules\Agents\Http\Controllers\Api\AgentProductApiController;

Route::prefix('v1/agent')->group(function () {
    // Guest Auth & Settings Routes
    Route::get('/settings', [AgentAuthController::class, 'settings']);
    Route::post('/login', [AgentAuthController::class, 'login']);

    // Protected Agent Routes (Sanctum)
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/profile', [AgentAuthController::class, 'profile']);
        Route::post('/logout', [AgentAuthController::class, 'logout']);
        Route::post('/fcm-token', [AgentAuthController::class, 'updateFcmToken']);

        // Dashboard & Wallet
        Route::get('/dashboard', [AgentDashboardController::class, 'index']);
        Route::get('/wallet/transactions', [AgentDashboardController::class, 'wallet']);
        Route::post('/wallet/payout-request', [AgentDashboardController::class, 'payoutRequest']);

        // Doctor Bookings
        Route::get('/doctors', [AgentBookingApiController::class, 'doctors']);
        Route::get('/doctors/{id}/slots', [AgentBookingApiController::class, 'slots']);
        Route::post('/booking/submit', [AgentBookingApiController::class, 'submitBooking']);
        Route::get('/bookings', [AgentBookingApiController::class, 'bookings']);

        // Products & Ordering
        Route::get('/products', [AgentProductApiController::class, 'products']);
        Route::post('/orders/place', [AgentProductApiController::class, 'placeOrder']);
        Route::get('/orders', [AgentProductApiController::class, 'orders']);
    });
});


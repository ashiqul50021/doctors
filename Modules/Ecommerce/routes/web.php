<?php

use Illuminate\Support\Facades\Route;
use Modules\Ecommerce\Http\Controllers\Frontend\ProductController;
use Modules\Ecommerce\Http\Controllers\Backend\ProductController as AdminProductController;
use Modules\Ecommerce\Http\Controllers\Backend\ProductCategoryController as AdminProductCategoryController;

/*
|--------------------------------------------------------------------------
| Ecommerce Frontend Routes
|--------------------------------------------------------------------------
*/

Route::prefix('products')->name('ecommerce.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('products');
    Route::post('/{product}/reviews', [ProductController::class, 'storeReview'])
        ->name('products.reviews.store');
    Route::get('/{id}', [ProductController::class, 'show'])->name('products.show');
});

Route::get('/shop/{slug}', [\Modules\Ecommerce\Http\Controllers\Frontend\ShopController::class, 'show'])->name('ecommerce.shop.show');

Route::name('ecommerce.')->group(function () {
    Route::post('/cart/add', [ProductController::class, 'addToCart'])->name('cart.add');
    Route::get('/cart', [ProductController::class, 'cart'])->name('cart');
    Route::post('/cart/remove', [ProductController::class, 'removeFromCart'])->name('cart.remove');
    Route::post('/cart/update', [ProductController::class, 'updateCart'])->name('cart.update');
    Route::post('/cart/coupon', [ProductController::class, 'applyCoupon'])->name('cart.coupon');
    Route::get('/product-checkout', [ProductController::class, 'checkout'])->name('checkout');
    Route::post('/place-order', [ProductController::class, 'placeOrder'])->name('order.place');
    Route::get('/order-success', [ProductController::class, 'orderSuccess'])->name('order.success');
});

// API route for AJAX
Route::get('/api/products/filter', [ProductController::class, 'filter'])->name('ecommerce.api.products.filter');

/*
|--------------------------------------------------------------------------
| Ecommerce Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('ecommerce.admin.')->group(function () {
    Route::patch('products/{product}/toggle-status', [AdminProductController::class, 'toggleStatus'])->name('products.toggle-status');
    Route::resource('products', AdminProductController::class)->except(['show']);
    Route::resource('product-categories', AdminProductCategoryController::class)->except(['show']);
    Route::get('product-reviews/search-products', [\Modules\Ecommerce\Http\Controllers\Backend\ProductReviewController::class, 'searchProducts'])->name('product-reviews.search-products');
    Route::resource('product-reviews', \Modules\Ecommerce\Http\Controllers\Backend\ProductReviewController::class)->except(['show']);
    Route::post('product-reviews/{review}/approve', [\Modules\Ecommerce\Http\Controllers\Backend\ProductReviewController::class, 'approve'])->name('product-reviews.approve');
    
    // Seller Management Routes
    Route::get('sellers', [\Modules\Ecommerce\Http\Controllers\Backend\SellerManagementController::class, 'index'])->name('sellers.index');
    Route::get('sellers/create', [\Modules\Ecommerce\Http\Controllers\Backend\SellerManagementController::class, 'create'])->name('sellers.create');
    Route::post('sellers', [\Modules\Ecommerce\Http\Controllers\Backend\SellerManagementController::class, 'store'])->name('sellers.store');
    Route::get('sellers/{id}', [\Modules\Ecommerce\Http\Controllers\Backend\SellerManagementController::class, 'show'])->name('sellers.show');
    Route::patch('sellers/{id}/status', [\Modules\Ecommerce\Http\Controllers\Backend\SellerManagementController::class, 'updateStatus'])->name('sellers.update-status');

    // Seller Payout Routes (Admin)
    Route::get('seller-payouts', [\Modules\Ecommerce\Http\Controllers\Backend\SellerPayoutController::class, 'index'])->name('seller-payouts.index');
    Route::patch('seller-payouts/{id}/status', [\Modules\Ecommerce\Http\Controllers\Backend\SellerPayoutController::class, 'updateStatus'])->name('seller-payouts.update-status');
});

/*
|--------------------------------------------------------------------------
| Ecommerce Seller Panel Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', \Modules\Ecommerce\Http\Middleware\SellerMiddleware::class])
    ->prefix('seller')
    ->name('ecommerce.seller.')
    ->group(function () {
        Route::get('/dashboard', [\Modules\Ecommerce\Http\Controllers\Seller\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/payouts', [\Modules\Ecommerce\Http\Controllers\Seller\PayoutController::class, 'index'])->name('payouts.index');
        Route::post('/payouts', [\Modules\Ecommerce\Http\Controllers\Seller\PayoutController::class, 'store'])->name('payouts.store');

        // Profile Settings
        Route::get('/profile', [\Modules\Ecommerce\Http\Controllers\Seller\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [\Modules\Ecommerce\Http\Controllers\Seller\ProfileController::class, 'update'])->name('profile.update');

        // Products Management
        Route::patch('products/{product}/toggle-status', [\Modules\Ecommerce\Http\Controllers\Seller\ProductController::class, 'toggleStatus'])->name('products.toggle-status');
        Route::resource('products', \Modules\Ecommerce\Http\Controllers\Seller\ProductController::class);

        // Orders Management
        Route::get('/orders', [\Modules\Ecommerce\Http\Controllers\Seller\OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{id}', [\Modules\Ecommerce\Http\Controllers\Seller\OrderController::class, 'show'])->name('orders.show');
    });


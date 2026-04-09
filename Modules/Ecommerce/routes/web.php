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
        ->middleware('auth')
        ->name('products.reviews.store');
    Route::get('/{id}', [ProductController::class, 'show'])->name('products.show');
});

Route::name('ecommerce.')->group(function () {
    Route::post('/cart/add', [ProductController::class, 'addToCart'])->name('cart.add');
    Route::get('/cart', [ProductController::class, 'cart'])->name('cart');
    Route::post('/cart/remove', [ProductController::class, 'removeFromCart'])->name('cart.remove');
    Route::post('/cart/update', [ProductController::class, 'updateCart'])->name('cart.update');
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
    Route::resource('products', AdminProductController::class)->except(['show']);
    Route::resource('product-categories', AdminProductCategoryController::class)->except(['show']);
});

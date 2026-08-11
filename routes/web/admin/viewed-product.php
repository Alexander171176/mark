<?php

use App\Http\Controllers\Admin\Market\MarketRecentlyViewedProduct\MarketRecentlyViewedProductController;
use Illuminate\Support\Facades\Route;

/** Пользователи с историей просмотренных товаров */
Route::get(
    '/market/recently-viewed-products',
    [MarketRecentlyViewedProductController::class, 'index']
)->name('marketRecentlyViewedProducts.index');

/** История просмотренных товаров конкретного пользователя */
Route::get(
    '/users/{user}/market/recently-viewed-products',
    [MarketRecentlyViewedProductController::class, 'show']
)->name('users.marketRecentlyViewedProducts.show');

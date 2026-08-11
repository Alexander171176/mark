<?php

// Товары маркетплейса

use Illuminate\Support\Facades\Route;

$siteLayout = config('site_settings.siteLayout', 'Default');

$publicProductController =
    "App\\Http\\Controllers\\Public\\{$siteLayout}\\Market\\MarketProduct\\MarketProductController";

/** Каталог товаров */
Route::get('/catalog/products', [$publicProductController, 'index'])
    ->name('public.marketProducts.index');

/** Получить недавно просмотренные товары */
Route::post('/catalog/products/recently-viewed', [$publicProductController, 'recentlyViewed'])
    ->name('public.marketProducts.recentlyViewed');

/** Объединить гостевую историю с историей пользователя */
Route::post('/catalog/products/recently-viewed/merge', [$publicProductController, 'mergeRecentlyViewed'])
    ->middleware('auth')
    ->name('public.marketProducts.recentlyViewed.merge');

/** Очистить историю просмотренных товаров */
Route::delete('/catalog/products/recently-viewed', [$publicProductController, 'clearRecentlyViewed'])
    ->middleware('auth')
    ->name('public.marketProducts.recentlyViewed.clear');

/** Страница конкретного товара */
Route::get('/catalog/products/{url}', [$publicProductController, 'show'])
    ->name('public.marketProducts.show');

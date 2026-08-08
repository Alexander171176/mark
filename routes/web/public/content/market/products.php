<?php

// Товары маркетплейса

use Illuminate\Support\Facades\Route;

$siteLayout = config('site_settings.siteLayout', 'Default');

$publicProductController =
    "App\\Http\\Controllers\\Public\\{$siteLayout}\\Market\\MarketProduct\\MarketProductController";

Route::get('/catalog/products', [$publicProductController, 'index'])
    ->name('public.marketProducts.index');

Route::get('/catalog/products/{url}', [$publicProductController, 'show'])
    ->name('public.marketProducts.show');

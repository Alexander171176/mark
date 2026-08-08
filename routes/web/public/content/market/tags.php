<?php

// Теги товаров маркетплейса

use Illuminate\Support\Facades\Route;

$siteLayout = config('site_settings.siteLayout', 'Default');

$publicTagController =
    "App\\Http\\Controllers\\Public\\{$siteLayout}\\Market\\MarketTag\\MarketTagController";

Route::get('/catalog/tags/{url}', [$publicTagController, 'show'])
    ->name('public.marketTags.show');

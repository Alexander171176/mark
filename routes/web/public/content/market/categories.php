<?php

// Категории товаров маркетплейса

use Illuminate\Support\Facades\Route;

$siteLayout = config('site_settings.siteLayout', 'Default');

$publicCategoryController =
    "App\\Http\\Controllers\\Public\\{$siteLayout}\\Market\\MarketCategory\\MarketCategoryController";

Route::get('/catalog/categories', [$publicCategoryController, 'index'])
    ->name('public.marketCategories.index');

Route::get('/catalog/menu/categories', [$publicCategoryController, 'menuCategories'])
    ->name('public.marketCategories.menu');

Route::get('/catalog/categories/{url}', [$publicCategoryController, 'show'])
    ->name('public.marketCategories.show');

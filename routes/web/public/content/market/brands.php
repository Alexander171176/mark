<?php

// Бренды товаров маркетплейса

use App\Http\Controllers\Public\PublicTemplateDispatcherController;
use Illuminate\Support\Facades\Route;

Route::get('/catalog/brands', [PublicTemplateDispatcherController::class, 'dispatch'])
    ->defaults('_templateController', 'Market\\MarketBrand\\MarketBrandController')
    ->defaults('_templateAction', 'index')
    ->name('public.marketBrands.index');

Route::get('/catalog/brands/{url}', [PublicTemplateDispatcherController::class, 'dispatch'])
    ->defaults('_templateController', 'Market\\MarketBrand\\MarketBrandController')
    ->defaults('_templateAction', 'show')
    ->name('public.marketBrands.show');

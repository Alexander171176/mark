<?php

// Категории товаров маркетплейса

use App\Http\Controllers\Public\PublicTemplateDispatcherController;
use Illuminate\Support\Facades\Route;

Route::get('/catalog/categories', [PublicTemplateDispatcherController::class, 'dispatch'])
    ->defaults('_templateController', 'Market\\MarketCategory\\MarketCategoryController')
    ->defaults('_templateAction', 'index')
    ->name('public.marketCategories.index');

Route::get('/catalog/menu/categories', [PublicTemplateDispatcherController::class, 'dispatch'])
    ->defaults('_templateController', 'Market\\MarketCategory\\MarketCategoryController')
    ->defaults('_templateAction', 'menuCategories')
    ->name('public.marketCategories.menu');

Route::get('/catalog/categories/{url}', [PublicTemplateDispatcherController::class, 'dispatch'])
    ->defaults('_templateController', 'Market\\MarketCategory\\MarketCategoryController')
    ->defaults('_templateAction', 'show')
    ->name('public.marketCategories.show');

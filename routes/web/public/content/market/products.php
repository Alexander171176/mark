<?php

// Товары маркетплейса

use App\Http\Controllers\Public\PublicTemplateDispatcherController;
use Illuminate\Support\Facades\Route;

/** Каталог товаров */
Route::get('/catalog/products', [PublicTemplateDispatcherController::class, 'dispatch'])
    ->defaults('_templateController', 'Market\\MarketProduct\\MarketProductController')
    ->defaults('_templateAction', 'index')
    ->name('public.marketProducts.index');

/** Получить недавно просмотренные товары */
Route::post('/catalog/products/recently-viewed', [PublicTemplateDispatcherController::class, 'dispatch'])
    ->defaults('_templateController', 'Market\\MarketProduct\\MarketProductController')
    ->defaults('_templateAction', 'recentlyViewed')
    ->name('public.marketProducts.recentlyViewed');

/** Объединить гостевую историю с историей пользователя */
Route::post('/catalog/products/recently-viewed/merge', [PublicTemplateDispatcherController::class, 'dispatch'])
    ->defaults('_templateController', 'Market\\MarketProduct\\MarketProductController')
    ->defaults('_templateAction', 'mergeRecentlyViewed')
    ->middleware('auth')
    ->name('public.marketProducts.recentlyViewed.merge');

/** Очистить историю просмотренных товаров */
Route::delete('/catalog/products/recently-viewed', [PublicTemplateDispatcherController::class, 'dispatch'])
    ->defaults('_templateController', 'Market\\MarketProduct\\MarketProductController')
    ->defaults('_templateAction', 'clearRecentlyViewed')
    ->middleware('auth')
    ->name('public.marketProducts.recentlyViewed.clear');

/** Страница конкретного товара */
Route::get('/catalog/products/{url}', [PublicTemplateDispatcherController::class, 'dispatch'])
    ->defaults('_templateController', 'Market\\MarketProduct\\MarketProductController')
    ->defaults('_templateAction', 'show')
    ->name('public.marketProducts.show');

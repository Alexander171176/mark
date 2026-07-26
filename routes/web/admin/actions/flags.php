<?php

use App\Http\Controllers\Admin\Market\MarketProduct\MarketProductController;
use App\Http\Controllers\Admin\Market\MarketProductBundle\MarketProductBundleController;
use App\Http\Controllers\Admin\School\SchoolCourse\SchoolCourseController;
use Illuminate\Support\Facades\Route;

// Курсы - новинки, хиты, распродажа
Route::put('/school-courses/{schoolCourse}/is-new',
    [SchoolCourseController::class, 'updateIsNew'])
    ->whereNumber('schoolCourse')
    ->name('schoolCourses.updateIsNew');

Route::put('/school-courses/{schoolCourse}/is-hit',
    [SchoolCourseController::class, 'updateIsHit'])
    ->whereNumber('schoolCourse')
    ->name('schoolCourses.updateIsHit');

Route::put('/school-courses/{schoolCourse}/is-sale',
    [SchoolCourseController::class, 'updateIsSale'])
    ->whereNumber('schoolCourse')
    ->name('schoolCourses.updateIsSale');

// Товары - новинки, хиты, распродажа
Route::put('/market-products/{marketProduct}/is-new',
    [MarketProductController::class, 'updateIsNew'])
    ->whereNumber('marketProduct')
    ->name('marketProducts.updateIsNew');

Route::put('/market-products/{marketProduct}/is-hit',
    [MarketProductController::class, 'updateIsHit'])
    ->whereNumber('marketProduct')
    ->name('marketProducts.updateIsHit');

Route::put('/market-products/{marketProduct}/is-sale',
    [MarketProductController::class, 'updateIsSale'])
    ->whereNumber('marketProduct')
    ->name('marketProducts.updateIsSale');

// Комплекты товаров - новинки, хиты, распродажа
Route::put('/market-product-bundles/{marketProductBundle}/is-new',
    [MarketProductBundleController::class, 'updateIsNew'])
    ->whereNumber('marketProductBundle')
    ->name('marketProductBundles.updateIsNew');

Route::put('/market-product-bundles/{marketProductBundle}/is-hit',
    [MarketProductBundleController::class, 'updateIsHit'])
    ->whereNumber('marketProductBundle')
    ->name('marketProductBundles.updateIsHit');

Route::put('/market-product-bundles/{marketProductBundle}/is-sale',
    [MarketProductBundleController::class, 'updateIsSale'])
    ->whereNumber('marketProductBundle')
    ->name('marketProductBundles.updateIsSale');

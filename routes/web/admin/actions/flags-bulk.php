<?php

use App\Http\Controllers\Admin\Market\MarketProduct\MarketProductController;
use App\Http\Controllers\Admin\School\SchoolCourse\SchoolCourseController;
use Illuminate\Support\Facades\Route;

// Курсы - новинки, хиты, распродажа - массовые позиции
Route::put('/school-courses/bulk-is-new',
    [SchoolCourseController::class, 'bulkUpdateIsNew'])
    ->name('schoolCourses.bulkUpdateIsNew');

Route::put('/school-courses/bulk-is-hit',
    [SchoolCourseController::class, 'bulkUpdateIsHit'])
    ->name('schoolCourses.bulkUpdateIsHit');

Route::put('/school-courses/bulk-is-sale',
    [SchoolCourseController::class, 'bulkUpdateIsSale'])
    ->name('schoolCourses.bulkUpdateIsSale');

// Товары - новинки, хиты, распродажа - массовые позиции
Route::put('/market-products/bulk-is-new',
    [MarketProductController::class, 'bulkUpdateIsNew'])
    ->name('marketProducts.bulkUpdateIsNew');

Route::put('/market-products/bulk-is-hit',
    [MarketProductController::class, 'bulkUpdateIsHit'])
    ->name('marketProducts.bulkUpdateIsHit');

Route::put('/market-products/bulk-is-sale',
    [MarketProductController::class, 'bulkUpdateIsSale'])
    ->name('marketProducts.bulkUpdateIsSale');

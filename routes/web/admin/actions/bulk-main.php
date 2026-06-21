<?php

// Переключение активности в главном массово

use App\Http\Controllers\Admin\Blog\BlogArticle\BlogArticleController;
use App\Http\Controllers\Admin\Blog\BlogBanner\BlogBannerController;
use App\Http\Controllers\Admin\Blog\BlogVideo\BlogVideoController;
use App\Http\Controllers\Admin\Market\MarketCompany\MarketCompanyController;
use App\Http\Controllers\Admin\Market\MarketShop\MarketShopController;
use App\Http\Controllers\Admin\School\SchoolCourse\SchoolCourseController;
use Illuminate\Support\Facades\Route;

Route::put('/blog-articles/bulk-main',
    [BlogArticleController::class, 'bulkUpdateMain'])
    ->name('blogArticles.bulkUpdateMain');

Route::put('/blog-banners/bulk-main',
    [BlogBannerController::class, 'bulkUpdateMain'])
    ->name('blogBanners.bulkUpdateMain');

Route::put('/blog-videos/bulk-main',
    [BlogVideoController::class, 'bulkUpdateMain'])
    ->name('blogVideos.bulkUpdateMain');

Route::put('/school-courses/bulk-main',
    [SchoolCourseController::class, 'bulkUpdateMain'])
    ->name('schoolCourses.bulkUpdateMain');

Route::put('/market-companies/bulk-main',
    [MarketCompanyController::class, 'bulkUpdateMain'])
    ->name('marketCompanies.bulkUpdateMain');

Route::put('/market-shops/bulk-main',
    [MarketShopController::class, 'bulkUpdateMain'])
    ->name('marketShops.bulkUpdateMain');

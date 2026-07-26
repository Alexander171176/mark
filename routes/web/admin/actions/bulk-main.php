<?php

// Переключение активности в главном массово

use App\Http\Controllers\Admin\Blog\BlogArticle\BlogArticleController;
use App\Http\Controllers\Admin\Blog\BlogBanner\BlogBannerController;
use App\Http\Controllers\Admin\Blog\BlogVideo\BlogVideoController;
use App\Http\Controllers\Admin\Market\MarketBrand\MarketBrandController;
use App\Http\Controllers\Admin\Market\MarketCompany\MarketCompanyController;
use App\Http\Controllers\Admin\Market\MarketProduct\MarketProductController;
use App\Http\Controllers\Admin\Market\MarketProductBundle\MarketProductBundleController;
use App\Http\Controllers\Admin\Market\MarketShop\MarketShopController;
use App\Http\Controllers\Admin\School\SchoolCourse\SchoolCourseController;
use Illuminate\Support\Facades\Route;

// блог
Route::put('/blog-articles/bulk-main',
    [BlogArticleController::class, 'bulkUpdateMain'])
    ->name('blogArticles.bulkUpdateMain');

Route::put('/blog-banners/bulk-main',
    [BlogBannerController::class, 'bulkUpdateMain'])
    ->name('blogBanners.bulkUpdateMain');

Route::put('/blog-videos/bulk-main',
    [BlogVideoController::class, 'bulkUpdateMain'])
    ->name('blogVideos.bulkUpdateMain');

// школа
Route::put('/school-courses/bulk-main',
    [SchoolCourseController::class, 'bulkUpdateMain'])
    ->name('schoolCourses.bulkUpdateMain');

// маркет
Route::put('/market-companies/bulk-main',
    [MarketCompanyController::class, 'bulkUpdateMain'])
    ->name('marketCompanies.bulkUpdateMain');

Route::put('/market-shops/bulk-main',
    [MarketShopController::class, 'bulkUpdateMain'])
    ->name('marketShops.bulkUpdateMain');

Route::put('/market-products/bulk-main',
    [MarketProductController::class, 'bulkUpdateMain'])
    ->name('marketProducts.bulkUpdateMain');

Route::put('/market-product-bundles/bulk-main',
    [MarketProductBundleController::class, 'bulkUpdateMain'])
    ->name('marketProductBundles.bulkUpdateMain');

Route::put('/market-brands/bulk-main',
    [MarketBrandController::class, 'bulkUpdateMain'])
    ->name('marketBrands.bulkUpdateMain');

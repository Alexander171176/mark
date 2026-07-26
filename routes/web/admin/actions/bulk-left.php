<?php

// Переключение активности в левой колонке массово

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
Route::put('/blog-articles/bulk-left',
    [BlogArticleController::class, 'bulkUpdateLeft'])
    ->name('blogArticles.bulkUpdateLeft');

Route::put('/blog-banners/bulk-left',
    [BlogBannerController::class, 'bulkUpdateLeft'])
    ->name('blogBanners.bulkUpdateLeft');

Route::put('/blog-videos/bulk-left',
    [BlogVideoController::class, 'bulkUpdateLeft'])
    ->name('blogVideos.bulkUpdateLeft');

// школа
Route::put('/school-courses/bulk-left',
    [SchoolCourseController::class, 'bulkUpdateLeft'])
    ->name('schoolCourses.bulkUpdateLeft');

// маркет
Route::put('/market-companies/bulk-left',
    [MarketCompanyController::class, 'bulkUpdateLeft'])
    ->name('marketCompanies.bulkUpdateLeft');

Route::put('/market-shops/bulk-left',
    [MarketShopController::class, 'bulkUpdateLeft'])
    ->name('marketShops.bulkUpdateLeft');

Route::put('/market-products/bulk-left',
    [MarketProductController::class, 'bulkUpdateLeft'])
    ->name('marketProducts.bulkUpdateLeft');

Route::put('/market-product-bundles/bulk-left',
    [MarketProductBundleController::class, 'bulkUpdateLeft'])
    ->name('marketProductBundles.bulkUpdateLeft');

Route::put('/market-brands/bulk-left',
    [MarketBrandController::class, 'bulkUpdateLeft'])
    ->name('marketBrands.bulkUpdateLeft');

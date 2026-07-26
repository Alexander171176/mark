<?php

// Переключение активности в правой колонке массово

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
Route::put('/blog-articles/bulk-right',
    [BlogArticleController::class, 'bulkUpdateRight'])
    ->name('blogArticles.bulkUpdateRight');

Route::put('/blog-banners/bulk-right',
    [BlogBannerController::class, 'bulkUpdateRight'])
    ->name('blogBanners.bulkUpdateRight');

Route::put('/blog-videos/bulk-right',
    [BlogVideoController::class, 'bulkUpdateRight'])
    ->name('blogVideos.bulkUpdateRight');

// школа
Route::put('/school-courses/bulk-right',
    [SchoolCourseController::class, 'bulkUpdateRight'])
    ->name('schoolCourses.bulkUpdateRight');

// маркет
Route::put('/market-companies/bulk-right',
    [MarketCompanyController::class, 'bulkUpdateRight'])
    ->name('marketCompanies.bulkUpdateRight');

Route::put('/market-shops/bulk-right',
    [MarketShopController::class, 'bulkUpdateRight'])
    ->name('marketShops.bulkUpdateRight');

Route::put('/market-products/bulk-right',
    [MarketProductController::class, 'bulkUpdateRight'])
    ->name('marketProducts.bulkUpdateRight');

Route::put('/market-product-bundles/bulk-right',
    [MarketProductBundleController::class, 'bulkUpdateRight'])
    ->name('marketProductBundles.bulkUpdateRight');

Route::put('/market-brands/bulk-right',
    [MarketBrandController::class, 'bulkUpdateRight'])
    ->name('marketBrands.bulkUpdateRight');

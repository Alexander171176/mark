<?php

// Переключение активности в правой колонке

use App\Http\Controllers\Admin\Blog\BlogArticle\BlogArticleController;
use App\Http\Controllers\Admin\Blog\BlogBanner\BlogBannerController;
use App\Http\Controllers\Admin\Blog\BlogVideo\BlogVideoController;
use App\Http\Controllers\Admin\Market\MarketBrand\MarketBrandController;
use App\Http\Controllers\Admin\Market\MarketCompany\MarketCompanyController;
use App\Http\Controllers\Admin\Market\MarketProduct\MarketProductController;
use App\Http\Controllers\Admin\Market\MarketProductBundle\MarketProductBundleController;
use App\Http\Controllers\Admin\Market\MarketShop\MarketShopController;
use App\Http\Controllers\Admin\School\SchoolAssignment\SchoolAssignmentController;
use App\Http\Controllers\Admin\School\SchoolCourse\SchoolCourseController;
use App\Http\Controllers\Admin\School\SchoolQuiz\SchoolQuizController;
use Illuminate\Support\Facades\Route;

// блог
Route::put('/blog-articles/{blogArticle}/right',
    [BlogArticleController::class, 'updateRight'])
    ->name('blogArticles.updateRight');

Route::put('/blog-banners/{blogBanner}/right',
    [BlogBannerController::class, 'updateRight'])
    ->name('blogBanners.updateRight');

Route::put('/blog-videos/{blogVideo}/right',
    [BlogVideoController::class, 'updateRight'])
    ->name('blogVideos.updateRight');

// школа
Route::put('/school-courses/{schoolCourse}/right',
    [SchoolCourseController::class, 'updateRight'])
    ->whereNumber('schoolCourse')
    ->name('schoolCourses.updateRight');

Route::put('/school-assignments/{schoolAssignment}/right',
    [SchoolAssignmentController::class, 'updateRight'])
    ->whereNumber('schoolAssignment')
    ->name('schoolAssignments.updateRight');

Route::put('/school-quizzes/{schoolQuiz}/right',
    [SchoolQuizController::class, 'updateRight'])
    ->whereNumber('schoolQuiz')
    ->name('schoolQuizzes.updateRight');

// маркет
Route::put('/market-companies/{marketCompany}/right',
    [MarketCompanyController::class, 'updateRight'])
    ->whereNumber('marketCompany')
    ->name('marketCompanies.updateRight');

Route::put('/market-shops/{marketShop}/right',
    [MarketShopController::class, 'updateRight'])
    ->whereNumber('marketShop')
    ->name('marketShops.updateRight');

Route::put('/market-products/{marketProduct}/right',
    [MarketProductController::class, 'updateRight'])
    ->whereNumber('marketProduct')
    ->name('marketProducts.updateRight');

Route::put('/market-product-bundles/{marketProductBundle}/right',
    [MarketProductBundleController::class, 'updateRight'])
    ->whereNumber('marketProductBundle')
    ->name('marketProductBundles.updateRight');

Route::put('/market-brands/{marketBrand}/right',
    [MarketBrandController::class, 'updateRight'])
    ->whereNumber('marketBrand')
    ->name('marketBrands.updateRight');

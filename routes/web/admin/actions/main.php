<?php

// Переключение активности в главном

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
Route::put('/blog-articles/{blogArticle}/main',
    [BlogArticleController::class, 'updateMain'])
    ->name('blogArticles.updateMain');

Route::put('/blog-banners/{blogBanner}/main',
    [BlogBannerController::class, 'updateMain'])
    ->name('blogBanners.updateMain');

Route::put('/blog-videos/{blogVideo}/main',
    [BlogVideoController::class, 'updateMain'])
    ->name('blogVideos.updateMain');

// школа
Route::put('/school-courses/{schoolCourse}/main',
    [SchoolCourseController::class, 'updateMain'])
    ->whereNumber('schoolCourse')
    ->name('schoolCourses.updateMain');

Route::put('/school-assignments/{schoolAssignment}/main',
    [SchoolAssignmentController::class, 'updateMain'])
    ->whereNumber('schoolAssignment')
    ->name('schoolAssignments.updateMain');

Route::put('/school-quizzes/{schoolQuiz}/main',
    [SchoolQuizController::class, 'updateMain'])
    ->whereNumber('schoolQuiz')
    ->name('schoolQuizzes.updateMain');

// маркет
Route::put('/market-companies/{marketCompany}/main',
    [MarketCompanyController::class, 'updateMain'])
    ->whereNumber('marketCompany')
    ->name('marketCompanies.updateMain');

Route::put('/market-shops/{marketShop}/main',
    [MarketShopController::class, 'updateMain'])
    ->whereNumber('marketShop')
    ->name('marketShops.updateMain');

Route::put('/market-products/{marketProduct}/main',
    [MarketProductController::class, 'updateMain'])
    ->whereNumber('marketProduct')
    ->name('marketProducts.updateMain');

Route::put('/market-product-bundles/{marketProductBundle}/main',
    [MarketProductBundleController::class, 'updateMain'])
    ->whereNumber('marketProductBundle')
    ->name('marketProductBundles.updateMain');

Route::put('/market-brands/{marketBrand}/main',
    [MarketBrandController::class, 'updateMain'])
    ->whereNumber('marketBrand')
    ->name('marketBrands.updateMain');

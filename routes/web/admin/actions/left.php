<?php

// Переключение активности в левой колонке

use App\Http\Controllers\Admin\Blog\BlogArticle\BlogArticleController;
use App\Http\Controllers\Admin\Blog\BlogBanner\BlogBannerController;
use App\Http\Controllers\Admin\Blog\BlogVideo\BlogVideoController;
use App\Http\Controllers\Admin\Market\MarketCompany\MarketCompanyController;
use App\Http\Controllers\Admin\Market\MarketShop\MarketShopController;
use App\Http\Controllers\Admin\School\SchoolAssignment\SchoolAssignmentController;
use App\Http\Controllers\Admin\School\SchoolCourse\SchoolCourseController;
use App\Http\Controllers\Admin\School\SchoolQuiz\SchoolQuizController;
use Illuminate\Support\Facades\Route;

// блог
Route::put('/blog-articles/{blogArticle}/left',
    [BlogArticleController::class, 'updateLeft'])
    ->name('blogArticles.updateLeft');

Route::put('/blog-banners/{blogBanner}/left',
    [BlogBannerController::class, 'updateLeft'])
    ->name('blogBanners.updateLeft');

Route::put('/blog-videos/{blogVideo}/left',
    [BlogVideoController::class, 'updateLeft'])
    ->name('blogVideos.updateLeft');

// школа
Route::put('/school-courses/{schoolCourse}/left',
    [SchoolCourseController::class, 'updateLeft'])
    ->whereNumber('schoolCourse')
    ->name('schoolCourses.updateLeft');

Route::put('/school-assignments/{schoolAssignment}/left',
    [SchoolAssignmentController::class, 'updateLeft'])
    ->whereNumber('schoolAssignment')
    ->name('schoolAssignments.updateLeft');

Route::put('/school-quizzes/{schoolQuiz}/left',
    [SchoolQuizController::class, 'updateLeft'])
    ->whereNumber('schoolQuiz')
    ->name('schoolQuizzes.updateLeft');

// маркет
Route::put('/market-companies/{marketCompany}/left',
    [MarketCompanyController::class, 'updateLeft'])
    ->whereNumber('marketCompany')
    ->name('marketCompanies.updateLeft');

Route::put('/market-shops/{marketShop}/left',
    [MarketShopController::class, 'updateLeft'])
    ->whereNumber('marketShop')
    ->name('marketShops.updateLeft');

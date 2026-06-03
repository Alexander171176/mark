<?php

// Переключение активности в правой колонке массово

use App\Http\Controllers\Admin\Blog\BlogArticle\BlogArticleController;
use App\Http\Controllers\Admin\Blog\BlogBanner\BlogBannerController;
use App\Http\Controllers\Admin\Blog\BlogVideo\BlogVideoController;
use App\Http\Controllers\Admin\School\SchoolCourse\SchoolCourseController;
use Illuminate\Support\Facades\Route;

Route::put('/blog-articles/bulk-right',
    [BlogArticleController::class, 'bulkUpdateRight'])
    ->name('blogArticles.bulkUpdateRight');

Route::put('/blog-banners/bulk-right',
    [BlogBannerController::class, 'bulkUpdateRight'])
    ->name('blogBanners.bulkUpdateRight');

Route::put('/blog-videos/bulk-right',
    [BlogVideoController::class, 'bulkUpdateRight'])
    ->name('blogVideos.bulkUpdateRight');

Route::put('/school-courses/bulk-right',
    [SchoolCourseController::class, 'bulkUpdateRight'])
    ->name('schoolCourses.bulkUpdateRight');

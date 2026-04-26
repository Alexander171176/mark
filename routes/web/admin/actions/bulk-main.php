<?php

// Переключение активности в главном массово

use App\Http\Controllers\Admin\Blog\BlogArticle\BlogArticleController;
use App\Http\Controllers\Admin\Blog\BlogBanner\BlogBannerController;
use App\Http\Controllers\Admin\Blog\BlogVideo\BlogVideoController;
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

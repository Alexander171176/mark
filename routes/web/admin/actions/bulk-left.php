<?php

// Переключение активности в левой колонке массово

use App\Http\Controllers\Admin\Blog\BlogArticle\BlogArticleController;
use App\Http\Controllers\Admin\Blog\BlogBanner\BlogBannerController;
use App\Http\Controllers\Admin\Blog\BlogVideo\BlogVideoController;
use Illuminate\Support\Facades\Route;

Route::put('/blog-articles/bulk-left',
    [BlogArticleController::class, 'bulkUpdateLeft'])
    ->name('blogArticles.bulkUpdateLeft');

Route::put('/blog-banners/bulk-left',
    [BlogBannerController::class, 'bulkUpdateLeft'])
    ->name('blogBanners.bulkUpdateLeft');

Route::put('/blog-videos/bulk-left',
    [BlogVideoController::class, 'bulkUpdateLeft'])
    ->name('blogVideos.bulkUpdateLeft');

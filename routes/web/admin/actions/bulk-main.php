<?php

// Переключение активности в главном массово

use App\Http\Controllers\Admin\Blog\Article\ArticleController;
use App\Http\Controllers\Admin\Blog\Banner\BannerController;
use App\Http\Controllers\Admin\Blog\Video\VideoController;
use Illuminate\Support\Facades\Route;

Route::put('/articles/bulk-main', [ArticleController::class, 'bulkUpdateMain'])
    ->name('articles.bulkUpdateMain');

Route::put('/banners/bulk-main', [BannerController::class, 'bulkUpdateMain'])
    ->name('banners.bulkUpdateMain');

Route::put('/videos/bulk-main', [VideoController::class, 'bulkUpdateMain'])
    ->name('videos.bulkUpdateMain');

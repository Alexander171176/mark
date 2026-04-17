<?php

// Переключение активности в левой колонке массово

use App\Http\Controllers\Admin\Blog\Article\ArticleController;
use App\Http\Controllers\Admin\Blog\Banner\BannerController;
use App\Http\Controllers\Admin\Blog\Video\VideoController;
use Illuminate\Support\Facades\Route;

Route::put('/articles/bulk-left', [ArticleController::class, 'bulkUpdateLeft'])
    ->name('articles.bulkUpdateLeft');

Route::put('/banners/bulk-left', [BannerController::class, 'bulkUpdateLeft'])
    ->name('banners.bulkUpdateLeft');

Route::put('/videos/bulk-left', [VideoController::class, 'bulkUpdateLeft'])
    ->name('videos.bulkUpdateLeft');

<?php

// Переключение активности в правой колонке массово

use App\Http\Controllers\Admin\Blog\Article\ArticleController;
use App\Http\Controllers\Admin\Blog\Banner\BannerController;
use App\Http\Controllers\Admin\Blog\Video\VideoController;
use Illuminate\Support\Facades\Route;

Route::put('/articles/bulk-right', [ArticleController::class, 'bulkUpdateRight'])
    ->name('articles.bulkUpdateRight');

Route::put('/banners/bulk-right', [BannerController::class, 'bulkUpdateRight'])
    ->name('banners.bulkUpdateRight');

Route::put('/videos/bulk-right', [VideoController::class, 'bulkUpdateRight'])
    ->name('videos.bulkUpdateRight');

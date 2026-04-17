<?php

// Одобрение рубрики
use App\Http\Controllers\Admin\Blog\Article\ArticleController;
use App\Http\Controllers\Admin\Blog\Banner\BannerController;
use App\Http\Controllers\Admin\Blog\Rubric\RubricController;
use App\Http\Controllers\Admin\Blog\Tag\TagController;
use App\Http\Controllers\Admin\Blog\Video\VideoController;
use Illuminate\Support\Facades\Route;

Route::put('/rubrics/{rubric}/approve', [RubricController::class, 'approve'])
    ->name('rubrics.approve');

Route::put('/articles/{article}/approve', [ArticleController::class, 'approve'])
    ->name('articles.approve');

Route::put('/tags/{tag}/approve', [TagController::class, 'approve'])
    ->name('tags.approve');

Route::put('/banners/{banner}/approve', [BannerController::class, 'approve'])
    ->name('banners.approve');

Route::put('/videos/{video}/approve', [VideoController::class, 'approve'])
    ->name('videos.approve');

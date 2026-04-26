<?php

// Переключение активности в правой колонке

use App\Http\Controllers\Admin\Blog\BlogArticle\BlogArticleController;
use App\Http\Controllers\Admin\Blog\BlogBanner\BlogBannerController;
use App\Http\Controllers\Admin\Blog\BlogVideo\BlogVideoController;
use App\Http\Controllers\Admin\School\Assignment\AssignmentController;
use App\Http\Controllers\Admin\School\Quiz\QuizController;
use Illuminate\Support\Facades\Route;

Route::put('/blog-articles/{blogArticle}/right',
    [BlogArticleController::class, 'updateRight'])
    ->name('blogArticles.updateRight');

Route::put('/blog-banners/{blogBanner}/right',
    [BlogBannerController::class, 'updateRight'])
    ->name('blogBanners.updateRight');

Route::put('/blog-videos/{blogVideo}/right',
    [BlogVideoController::class, 'updateRight'])
    ->name('blogVideos.updateRight');

Route::put('/assignments/{assignment}/right', [AssignmentController::class, 'updateRight'])
    ->name('assignments.updateRight');

Route::put('/quizzes/{quiz}/right', [QuizController::class, 'updateRight'])
    ->name('quizzes.updateRight');

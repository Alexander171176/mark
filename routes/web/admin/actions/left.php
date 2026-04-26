<?php

// Переключение активности в левой колонке

use App\Http\Controllers\Admin\Blog\BlogArticle\BlogArticleController;
use App\Http\Controllers\Admin\Blog\BlogBanner\BlogBannerController;
use App\Http\Controllers\Admin\Blog\BlogVideo\BlogVideoController;
use App\Http\Controllers\Admin\School\Assignment\AssignmentController;
use App\Http\Controllers\Admin\School\Quiz\QuizController;
use Illuminate\Support\Facades\Route;

Route::put('/blog-articles/{blogArticle}/left',
    [BlogArticleController::class, 'updateLeft'])
    ->name('blogArticles.updateLeft');

Route::put('/blog-banners/{blogBanner}/left',
    [BlogBannerController::class, 'updateLeft'])
    ->name('blogBanners.updateLeft');

Route::put('/blog-videos/{blogVideo}/left',
    [BlogVideoController::class, 'updateLeft'])
    ->name('blogVideos.updateLeft');

Route::put('/assignments/{assignment}/left', [AssignmentController::class, 'updateLeft'])
    ->name('assignments.updateLeft');

Route::put('/quizzes/{quiz}/left', [QuizController::class, 'updateLeft'])
    ->name('quizzes.updateLeft');

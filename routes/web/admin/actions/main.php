<?php

// Переключение активности в главном

use App\Http\Controllers\Admin\Blog\BlogArticle\BlogArticleController;
use App\Http\Controllers\Admin\Blog\BlogBanner\BlogBannerController;
use App\Http\Controllers\Admin\Blog\BlogVideo\BlogVideoController;
use App\Http\Controllers\Admin\School\Assignment\AssignmentController;
use App\Http\Controllers\Admin\School\Quiz\QuizController;
use Illuminate\Support\Facades\Route;

Route::put('/blog-articles/{blogArticle}/main',
    [BlogArticleController::class, 'updateMain'])
    ->name('blogArticles.updateMain');

Route::put('/blog-banners/{blogBanner}/main',
    [BlogBannerController::class, 'updateMain'])
    ->name('blogBanners.updateMain');

Route::put('/blog-videos/{blogVideo}/main',
    [BlogVideoController::class, 'updateMain'])
    ->name('blogVideos.updateMain');

Route::put('/assignments/{assignment}/main', [AssignmentController::class, 'updateMain'])
    ->name('assignments.updateMain');

Route::put('/quizzes/{quiz}/main', [QuizController::class, 'updateMain'])
    ->name('quizzes.updateMain');

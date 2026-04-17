<?php

// Переключение активности в левой колонке

use App\Http\Controllers\Admin\Blog\Article\ArticleController;
use App\Http\Controllers\Admin\Blog\Banner\BannerController;
use App\Http\Controllers\Admin\Blog\Video\VideoController;
use App\Http\Controllers\Admin\School\Assignment\AssignmentController;
use App\Http\Controllers\Admin\School\Quiz\QuizController;
use Illuminate\Support\Facades\Route;

Route::put('/articles/{article}/left', [ArticleController::class, 'updateLeft'])
    ->name('articles.updateLeft');

Route::put('/banners/{banner}/left', [BannerController::class, 'updateLeft'])
    ->name('banners.updateLeft');

Route::put('/videos/{video}/left', [VideoController::class, 'updateLeft'])
    ->name('videos.updateLeft');

Route::put('/assignments/{assignment}/left', [AssignmentController::class, 'updateLeft'])
    ->name('assignments.updateLeft');

Route::put('/quizzes/{quiz}/left', [QuizController::class, 'updateLeft'])
    ->name('quizzes.updateLeft');

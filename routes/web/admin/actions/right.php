<?php

// Переключение активности в правой колонке

use App\Http\Controllers\Admin\Blog\Article\ArticleController;
use App\Http\Controllers\Admin\Blog\Banner\BannerController;
use App\Http\Controllers\Admin\Blog\Video\VideoController;
use App\Http\Controllers\Admin\School\Assignment\AssignmentController;
use App\Http\Controllers\Admin\School\Quiz\QuizController;
use Illuminate\Support\Facades\Route;

Route::put('/articles/{article}/right', [ArticleController::class, 'updateRight'])
    ->name('articles.updateRight');

Route::put('/banners/{banner}/right', [BannerController::class, 'updateRight'])
    ->name('banners.updateRight');

Route::put('/videos/{video}/right', [VideoController::class, 'updateRight'])
    ->name('videos.updateRight');

Route::put('/assignments/{assignment}/right', [AssignmentController::class, 'updateRight'])
    ->name('assignments.updateRight');

Route::put('/quizzes/{quiz}/right', [QuizController::class, 'updateRight'])
    ->name('quizzes.updateRight');

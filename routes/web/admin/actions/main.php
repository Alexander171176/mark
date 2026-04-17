<?php

// Переключение активности в главном

use App\Http\Controllers\Admin\Blog\Article\ArticleController;
use App\Http\Controllers\Admin\Blog\Banner\BannerController;
use App\Http\Controllers\Admin\Blog\Video\VideoController;
use App\Http\Controllers\Admin\School\Assignment\AssignmentController;
use App\Http\Controllers\Admin\School\Quiz\QuizController;
use Illuminate\Support\Facades\Route;

Route::put('/articles/{article}/main', [ArticleController::class, 'updateMain'])
    ->name('articles.updateMain');

Route::put('/banners/{banner}/main', [BannerController::class, 'updateMain'])
    ->name('banners.updateMain');

Route::put('/videos/{video}/main', [VideoController::class, 'updateMain'])
    ->name('videos.updateMain');

Route::put('/assignments/{assignment}/main', [AssignmentController::class, 'updateMain'])
    ->name('assignments.updateMain');

Route::put('/quizzes/{quiz}/main', [QuizController::class, 'updateMain'])
    ->name('quizzes.updateMain');

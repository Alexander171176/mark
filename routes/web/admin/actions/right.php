<?php

// Переключение активности в правой колонке

use App\Http\Controllers\Admin\Blog\BlogArticle\BlogArticleController;
use App\Http\Controllers\Admin\Blog\BlogBanner\BlogBannerController;
use App\Http\Controllers\Admin\Blog\BlogVideo\BlogVideoController;
use App\Http\Controllers\Admin\School\SchoolAssignment\SchoolAssignmentController;
use App\Http\Controllers\Admin\School\SchoolCourse\SchoolCourseController;
use App\Http\Controllers\Admin\School\SchoolQuiz\SchoolQuizController;
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

Route::put('/school-courses/{schoolCourse}/right',
    [SchoolCourseController::class, 'updateRight'])
    ->whereNumber('schoolCourse')
    ->name('schoolCourses.updateRight');

Route::put('/school-assignments/{schoolAssignment}/right',
    [SchoolAssignmentController::class, 'updateRight'])
    ->whereNumber('schoolAssignment')
    ->name('schoolAssignments.updateRight');

Route::put('/school-quizzes/{schoolQuiz}/right',
    [SchoolQuizController::class, 'updateRight'])
    ->whereNumber('schoolQuiz')
    ->name('schoolQuizzes.updateRight');

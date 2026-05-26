<?php

// Переключение активности в главном

use App\Http\Controllers\Admin\Blog\BlogArticle\BlogArticleController;
use App\Http\Controllers\Admin\Blog\BlogBanner\BlogBannerController;
use App\Http\Controllers\Admin\Blog\BlogVideo\BlogVideoController;
use App\Http\Controllers\Admin\School\Assignment\SchoolAssignmentController;
use App\Http\Controllers\Admin\School\Course\SchoolCourseController;
use App\Http\Controllers\Admin\School\Quiz\SchoolQuizController;
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

Route::put('/school-courses/{schoolCourse}/main',
    [SchoolCourseController::class, 'updateMain'])
    ->whereNumber('schoolCourse')
    ->name('schoolCourses.updateMain');

Route::put('/school-assignments/{schoolAssignment}/main',
    [SchoolAssignmentController::class, 'updateMain'])
    ->whereNumber('schoolAssignment')
    ->name('schoolAssignments.updateMain');

Route::put('/school-quizzes/{schoolQuiz}/main',
    [SchoolQuizController::class, 'updateMain'])
    ->whereNumber('schoolQuiz')
    ->name('schoolQuizzes.updateMain');

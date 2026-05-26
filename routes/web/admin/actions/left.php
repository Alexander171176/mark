<?php

// Переключение активности в левой колонке

use App\Http\Controllers\Admin\Blog\BlogArticle\BlogArticleController;
use App\Http\Controllers\Admin\Blog\BlogBanner\BlogBannerController;
use App\Http\Controllers\Admin\Blog\BlogVideo\BlogVideoController;
use App\Http\Controllers\Admin\School\Assignment\SchoolAssignmentController;
use App\Http\Controllers\Admin\School\Course\SchoolCourseController;
use App\Http\Controllers\Admin\School\Quiz\SchoolQuizController;
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

Route::put('/school-courses/{schoolCourse}/left',
    [SchoolCourseController::class, 'updateLeft'])
    ->whereNumber('schoolCourse')
    ->name('schoolCourses.updateLeft');

Route::put('/school-assignments/{schoolAssignment}/left',
    [SchoolAssignmentController::class, 'updateLeft'])
    ->whereNumber('schoolAssignment')
    ->name('schoolAssignments.updateLeft');

Route::put('/school-quizzes/{schoolQuiz}/left',
    [SchoolQuizController::class, 'updateLeft'])
    ->whereNumber('schoolQuiz')
    ->name('schoolQuizzes.updateLeft');

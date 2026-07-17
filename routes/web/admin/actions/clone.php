<?php

use App\Http\Controllers\Admin\Blog\BlogArticle\BlogArticleController;
use App\Http\Controllers\Admin\Blog\BlogRubric\BlogRubricController;
use App\Http\Controllers\Admin\Market\MarketProduct\MarketProductController;
use App\Http\Controllers\Admin\School\SchoolAssignment\SchoolAssignmentController;
use App\Http\Controllers\Admin\School\SchoolCourseSchedule\SchoolCourseScheduleController;
use App\Http\Controllers\Admin\School\SchoolLesson\SchoolLessonController;
use App\Http\Controllers\Admin\School\SchoolOrder\SchoolOrderController;
use App\Http\Controllers\Admin\School\SchoolQuiz\SchoolQuizController;
use Illuminate\Support\Facades\Route;

// блог
Route::post('/blog-rubrics/{blogRubric}/clone',
    [BlogRubricController::class, 'clone'])
    ->name('blogRubrics.clone');

Route::post('/blog-articles/{blogArticle}/clone',
    [BlogArticleController::class, 'clone'])
    ->name('blogArticles.clone');

// школа
Route::post('/school-lessons/{schoolLesson}/clone',
    [SchoolLessonController::class, 'clone'])
    ->whereNumber('schoolLesson')
    ->name('schoolLessons.clone');

Route::post('/school-assignments/{schoolAssignment}/clone',
    [SchoolAssignmentController::class, 'clone'])
    ->whereNumber('schoolAssignment')
    ->name('schoolAssignments.clone');

Route::post('/school-course-schedules/{schoolCourseSchedule}/clone',
    [SchoolCourseScheduleController::class, 'clone'])
    ->whereNumber('schoolCourseSchedule')
    ->name('schoolCourseSchedules.clone');

Route::post('/school-orders/{schoolOrder}/clone',
    [SchoolOrderController::class, 'clone'])
    ->whereNumber('schoolOrder')
    ->name('schoolOrders.clone');

Route::post('/school-quizzes/{schoolQuiz}/clone',
    [SchoolQuizController::class, 'clone'])
    ->whereNumber('schoolQuiz')
    ->name('schoolQuizzes.clone');

// магазин
Route::post('/market-products/{marketProduct}/clone',
    [MarketProductController::class, 'clone'])
    ->name('marketProducts.clone');

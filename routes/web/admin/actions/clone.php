<?php

// Клонирование (Используем имена моделей для параметров RMB)

use App\Http\Controllers\Admin\Blog\Article\ArticleController;
use App\Http\Controllers\Admin\Blog\Rubric\RubricController;
use App\Http\Controllers\Admin\Finance\Order\OrderController;
use App\Http\Controllers\Admin\School\Assignment\AssignmentController;
use App\Http\Controllers\Admin\School\CourseSchedule\CourseScheduleController;
use App\Http\Controllers\Admin\School\Lesson\LessonController;
use App\Http\Controllers\Admin\School\QuizQuestion\QuizQuestionController;
use Illuminate\Support\Facades\Route;

Route::post('/rubrics/{rubric}/clone',
    [RubricController::class, 'clone'])->name('rubrics.clone');

Route::post('/articles/{article}/clone',
    [ArticleController::class, 'clone'])->name('articles.clone');

Route::post('/lessons/{lesson}/clone',
    [LessonController::class, 'clone'])->name('lessons.clone');

Route::post('/assignments/{assignment}/clone',
    [AssignmentController::class, 'clone'])->name('assignments.clone');

Route::post('/course-schedules/{courseSchedule}/clone',
    [CourseScheduleController::class, 'clone'])
    ->name('courseSchedules.clone');

Route::post('/quiz-questions/{quizQuestion}/clone',
    [QuizQuestionController::class, 'clone'])
    ->name('quizQuestions.clone');

Route::post('/orders/{order}/clone',
    [OrderController::class, 'clone'])->name('orders.clone');

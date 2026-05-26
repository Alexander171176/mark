<?php

use App\Http\Controllers\Admin\Blog\BlogArticle\BlogArticleController;
use App\Http\Controllers\Admin\Blog\BlogRubric\BlogRubricController;
use App\Http\Controllers\Admin\School\Assignment\SchoolAssignmentController;
use App\Http\Controllers\Admin\School\CourseSchedule\SchoolCourseScheduleController;
use App\Http\Controllers\Admin\School\Lesson\SchoolLessonController;
use App\Http\Controllers\Admin\School\Order\SchoolOrderController;
use App\Http\Controllers\Admin\School\Quiz\SchoolQuizController;
use Illuminate\Support\Facades\Route;

Route::post('/blog-rubrics/{blogRubric}/clone',
    [BlogRubricController::class, 'clone'])
    ->name('blogRubrics.clone');

Route::post('/blog-articles/{blogArticle}/clone',
    [BlogArticleController::class, 'clone'])
    ->name('blogArticles.clone');

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

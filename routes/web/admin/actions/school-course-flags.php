<?php

use App\Http\Controllers\Admin\School\SchoolCourse\SchoolCourseController;
use Illuminate\Support\Facades\Route;

// Онлайн школа - новинки, хиты, распродажа
Route::put('/school-courses/{schoolCourse}/is-new',
    [SchoolCourseController::class, 'updateIsNew'])
    ->whereNumber('schoolCourse')
    ->name('schoolCourses.updateIsNew');

Route::put('/school-courses/{schoolCourse}/is-hit',
    [SchoolCourseController::class, 'updateIsHit'])
    ->whereNumber('schoolCourse')
    ->name('schoolCourses.updateIsHit');

Route::put('/school-courses/{schoolCourse}/is-sale',
    [SchoolCourseController::class, 'updateIsSale'])
    ->whereNumber('schoolCourse')
    ->name('schoolCourses.updateIsSale');

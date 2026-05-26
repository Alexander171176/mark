<?php

use App\Http\Controllers\Admin\School\Course\SchoolCourseController;
use Illuminate\Support\Facades\Route;

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

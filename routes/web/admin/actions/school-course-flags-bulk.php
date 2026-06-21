<?php

use App\Http\Controllers\Admin\School\SchoolCourse\SchoolCourseController;
use Illuminate\Support\Facades\Route;

// Онлайн школа - новинки, хиты, распродажа - массовые позиции
Route::put('/school-courses/bulk-is-new',
    [SchoolCourseController::class, 'bulkUpdateIsNew'])
    ->name('schoolCourses.bulkUpdateIsNew');

Route::put('/school-courses/bulk-is-hit',
    [SchoolCourseController::class, 'bulkUpdateIsHit'])
    ->name('schoolCourses.bulkUpdateIsHit');

Route::put('/school-courses/bulk-is-sale',
    [SchoolCourseController::class, 'bulkUpdateIsSale'])
    ->name('schoolCourses.bulkUpdateIsSale');

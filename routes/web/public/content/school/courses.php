<?php
// Курсы обучения

use Illuminate\Support\Facades\Route;

$siteLayout = config('site_settings.siteLayout', 'Default');

$publicCourseController =
    "App\\Http\\Controllers\\Public\\{$siteLayout}\\School\\SchoolCourse\\SchoolCourseController";

Route::get('/school/courses', [$publicCourseController, 'index'])
    ->name('public.schoolCourses.index');

Route::get('/school/courses/{slug}', [$publicCourseController, 'show'])
    ->name('public.schoolCourses.show');

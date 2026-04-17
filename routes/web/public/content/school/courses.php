<?php
// Инструкторы обучения

use Illuminate\Support\Facades\Route;

$siteLayout = config('site_settings.siteLayout', 'Default');

$publicCourseController = "App\\Http\\Controllers\\Public\\{$siteLayout}\\School\\CourseController";

Route::get('/school/courses', [$publicCourseController, 'index'])
    ->name('public.courses.index');

Route::get('/school/courses/{slug}', [$publicCourseController, 'show'])
    ->name('public.courses.show');

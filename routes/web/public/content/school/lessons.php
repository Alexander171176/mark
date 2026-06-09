<?php
// Уроки школы

use Illuminate\Support\Facades\Route;

$siteLayout = config('site_settings.siteLayout', 'Default');

$publicLessonController =
    "App\\Http\\Controllers\\Public\\{$siteLayout}\\School\\SchoolLesson\\SchoolLessonController";

Route::get('/school/lessons', [$publicLessonController, 'index'])
    ->name('public.schoolLessons.index');

Route::get('/school/lessons/{slug}', [$publicLessonController, 'show'])
    ->name('public.schoolLessons.show');

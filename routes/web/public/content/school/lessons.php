<?php
// Инструкторы обучения

use Illuminate\Support\Facades\Route;

$siteLayout = config('site_settings.siteLayout', 'Default');

$publicLessonController = "App\\Http\\Controllers\\Public\\{$siteLayout}\\School\\LessonController";

Route::get('/school/lessons', [$publicLessonController, 'index'])
    ->name('public.lessons.index');

Route::get('/school/lessons/{slug}', [$publicLessonController, 'show'])
    ->name('public.lessons.show');

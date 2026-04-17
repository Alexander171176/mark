<?php
// Инструкторы обучения

use Illuminate\Support\Facades\Route;

$siteLayout = config('site_settings.siteLayout', 'Default');

$publicInstructorController = "App\\Http\\Controllers\\Public\\{$siteLayout}\\School\\InstructorController";

Route::get('/school/instructors', [$publicInstructorController, 'index'])
    ->name('public.instructors.index');

Route::get('/school/instructors/{slug}', [$publicInstructorController, 'show'])
    ->name('public.instructors.show');

<?php
// Инструкторы школы

use Illuminate\Support\Facades\Route;

$siteLayout = config('site_settings.siteLayout', 'Default');

$publicInstructorController =
    "App\\Http\\Controllers\\Public\\{$siteLayout}\\School\\SchoolInstructor\\SchoolInstructorController";

Route::get('/school/instructors', [$publicInstructorController, 'index'])
    ->name('public.schoolInstructors.index');

Route::get('/school/instructors/{slug}', [$publicInstructorController, 'show'])
    ->name('public.schoolInstructors.show');

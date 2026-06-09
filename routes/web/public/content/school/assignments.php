<?php
// Задания обучения

use Illuminate\Support\Facades\Route;

$siteLayout = config('site_settings.siteLayout', 'Default');

$publicAssignmentController =
    "App\\Http\\Controllers\\Public\\{$siteLayout}\\School\\SchoolAssignment\\SchoolAssignmentController";

Route::get('/school/assignments', [$publicAssignmentController, 'index'])
    ->name('public.schoolAssignments.index');

Route::get('/school/assignments/{slug}', [$publicAssignmentController, 'show'])
    ->name('public.schoolAssignments.show');

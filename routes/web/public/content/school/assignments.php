<?php
// Инструкторы обучения

use Illuminate\Support\Facades\Route;

$siteLayout = config('site_settings.siteLayout', 'Default');

$publicAssignmentController = "App\\Http\\Controllers\\Public\\{$siteLayout}\\School\\AssignmentController";

Route::get('/school/assignments', [$publicAssignmentController, 'index'])
    ->name('public.assignments.index');

Route::get('/school/assignments/{slug}', [$publicAssignmentController, 'show'])
    ->name('public.assignments.show');

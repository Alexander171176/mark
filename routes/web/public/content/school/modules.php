<?php
// Модули школы

use Illuminate\Support\Facades\Route;

$siteLayout = config('site_settings.siteLayout', 'Default');

$publicModuleController =
    "App\\Http\\Controllers\\Public\\{$siteLayout}\\School\\SchoolModule\\SchoolModuleController";

Route::get('/school/modules', [$publicModuleController, 'index'])
    ->name('public.schoolModules.index');

Route::get('/school/modules/{slug}', [$publicModuleController, 'show'])
    ->name('public.schoolModules.show');

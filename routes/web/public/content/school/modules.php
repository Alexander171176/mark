<?php
// Инструкторы обучения

use Illuminate\Support\Facades\Route;

$siteLayout = config('site_settings.siteLayout', 'Default');

$publicModuleController = "App\\Http\\Controllers\\Public\\{$siteLayout}\\School\\ModuleController";

Route::get('/school/modules', [$publicModuleController, 'index'])
    ->name('public.modules.index');

Route::get('/school/modules/{slug}', [$publicModuleController, 'show'])
    ->name('public.modules.show');

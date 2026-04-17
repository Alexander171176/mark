<?php
// Видео из шаблона

use Illuminate\Support\Facades\Route;

$siteLayout = config('site_settings.siteLayout', 'Default');

$publicVideoController = "App\\Http\\Controllers\\Public\\{$siteLayout}\\Blog\\VideoController";

Route::get('/videos', [$publicVideoController, 'index'])
    ->name('public.videos.index');

Route::get('/videos/{url}', [$publicVideoController, 'show'])
    ->name('public.videos.show');

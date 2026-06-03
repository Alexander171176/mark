<?php
// Видео из шаблона

use Illuminate\Support\Facades\Route;

$siteLayout = config('site_settings.siteLayout', 'Default');

$publicVideoController = "App\\Http\\Controllers\\Public\\{$siteLayout}\\Blog\\BlogVideo\\BlogVideoController";

Route::get('/videos', [$publicVideoController, 'index'])
    ->name('public.blogVideos.index');

Route::get('/videos/{url}', [$publicVideoController, 'show'])
    ->name('public.blogVideos.show');

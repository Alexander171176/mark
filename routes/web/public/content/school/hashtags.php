<?php
// Теги Блога из шаблона

use Illuminate\Support\Facades\Route;

$siteLayout = config('site_settings.siteLayout', 'Default');

$publicHashtagController = "App\\Http\\Controllers\\Public\\{$siteLayout}\\School\\HashtagController";

Route::get('/school/hashtags/{slug}', [$publicHashtagController, 'show'])
    ->name('public.hashtags.show');

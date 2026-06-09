<?php
// Хештеги школы

use Illuminate\Support\Facades\Route;

$siteLayout = config('site_settings.siteLayout', 'Default');

$publicHashtagController =
    "App\\Http\\Controllers\\Public\\{$siteLayout}\\School\\SchoolHashtag\\SchoolHashtagController";

Route::get('/school/hashtags/{slug}', [$publicHashtagController, 'show'])
    ->name('public.schoolHashtags.show');

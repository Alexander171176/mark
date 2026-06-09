<?php
// Направления обучения школы

use Illuminate\Support\Facades\Route;

$siteLayout = config('site_settings.siteLayout', 'Default');

$publicTrackController =
    "App\\Http\\Controllers\\Public\\{$siteLayout}\\School\\SchoolTrack\\SchoolTrackController";

Route::get('/school/tracks', [$publicTrackController, 'index'])
    ->name('public.schoolTracks.index');

Route::get('/school/menu/tracks', [$publicTrackController, 'menuTracks'])
    ->name('public.schoolTracks.menu');

Route::get('/school/tracks/{slug}', [$publicTrackController, 'show'])
    ->name('public.schoolTracks.show');

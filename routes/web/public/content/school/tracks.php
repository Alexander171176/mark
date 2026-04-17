<?php
// Рубрики Блога из шаблона
use Illuminate\Support\Facades\Route;

$siteLayout = config('site_settings.siteLayout', 'Default');

$publicTrackController = "App\\Http\\Controllers\\Public\\{$siteLayout}\\School\\TrackController";

Route::get('/school/tracks', [$publicTrackController, 'index'])
    ->name('public.tracks.index');

Route::get('/school/menu/tracks', [$publicTrackController, 'menuTracks'])
    ->name('public.tracks.menu');

Route::get('/school/tracks/{slug}', [$publicTrackController, 'show'])
    ->name('public.tracks.show');

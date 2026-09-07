<?php
// Направления обучения школы

use App\Http\Controllers\Public\PublicTemplateDispatcherController;
use Illuminate\Support\Facades\Route;

Route::get('/school/tracks', [PublicTemplateDispatcherController::class, 'dispatch'])
    ->defaults('_templateController', 'School\\SchoolTrack\\SchoolTrackController')
    ->defaults('_templateAction', 'index')
    ->name('public.schoolTracks.index');

Route::get('/school/menu/tracks', [PublicTemplateDispatcherController::class, 'dispatch'])
    ->defaults('_templateController', 'School\\SchoolTrack\\SchoolTrackController')
    ->defaults('_templateAction', 'menuTracks')
    ->name('public.schoolTracks.menu');

Route::get('/school/tracks/{slug}', [PublicTemplateDispatcherController::class, 'dispatch'])
    ->defaults('_templateController', 'School\\SchoolTrack\\SchoolTrackController')
    ->defaults('_templateAction', 'show')
    ->name('public.schoolTracks.show');

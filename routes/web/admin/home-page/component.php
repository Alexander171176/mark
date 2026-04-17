<?php
// --- COMPONENT ---

use App\Http\Controllers\Admin\Constructor\HomePage\Component\ComponentFeatureController;
use App\Http\Controllers\Admin\Constructor\HomePage\Component\ComponentSectionController;
use App\Http\Controllers\Admin\Constructor\HomePage\Component\ComponentTabController;
use App\Http\Controllers\Admin\Constructor\HomePage\Component\ComponentTileController;
use Illuminate\Support\Facades\Route;

Route::get('/component/sections/{section}/edit', [ComponentSectionController::class, 'edit'])
    ->name('component.sections.edit');

Route::put('/component/sections/{section}', [ComponentSectionController::class, 'update'])
    ->name('component.sections.update');

Route::put('/component/features/{feature}', [ComponentFeatureController::class, 'update'])
    ->name('component.features.update');

Route::put('/component/features/{feature}/activity', [ComponentFeatureController::class, 'updateActivity'])
    ->name('component.features.updateActivity');

Route::put('/component/tabs/{tab}', [ComponentTabController::class, 'update'])
    ->name('component.tabs.update');

Route::put('/component/tabs/{tab}/activity', [ComponentTabController::class, 'updateActivity'])
    ->name('component.tabs.updateActivity');

Route::put('/component/tiles/{tile}', [ComponentTileController::class, 'update'])
    ->name('component.tiles.update');

Route::put('/component/tiles/{tile}/activity', [ComponentTileController::class, 'updateActivity'])
    ->name('component.tiles.updateActivity');

Route::post('/component/tiles/{tile}/upload-light', [ComponentTileController::class, 'uploadLight'])
    ->name('component.tiles.uploadLight');

Route::post('/component/tiles/{tile}/upload-dark', [ComponentTileController::class, 'uploadDark'])
    ->name('component.tiles.uploadDark');

Route::delete('/component/tiles/{tile}/clear-light', [ComponentTileController::class, 'clearLight'])
    ->name('component.tiles.clearLight');

Route::delete('/component/tiles/{tile}/clear-dark', [ComponentTileController::class, 'clearDark'])
    ->name('component.tiles.clearDark');

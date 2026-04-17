<?php
// --- QUALITY ---

use App\Http\Controllers\Admin\Constructor\HomePage\Quality\QualityItemController;
use App\Http\Controllers\Admin\Constructor\HomePage\Quality\QualitySectionController;
use Illuminate\Support\Facades\Route;

Route::get('/quality/sections/{section}/edit', [QualitySectionController::class, 'edit'])
    ->name('quality.sections.edit');

Route::put('/quality/sections/{section}', [QualitySectionController::class, 'update'])
    ->name('quality.sections.update');

Route::put('/quality/items/{item}', [QualityItemController::class, 'update'])
    ->name('quality.items.update');

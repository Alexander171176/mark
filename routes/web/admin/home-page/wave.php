<?php
// --- WAVE ---

use App\Http\Controllers\Admin\Constructor\HomePage\Wave\WaveSectionController;
use App\Http\Controllers\Admin\Constructor\HomePage\Wave\WaveTechController;
use Illuminate\Support\Facades\Route;

Route::get('/wave/sections/{section}/edit', [WaveSectionController::class, 'edit'])
    ->name('wave.sections.edit');

Route::put('/wave/sections/{section}', [WaveSectionController::class, 'update'])
    ->name('wave.sections.update');

Route::put('/wave/teches/{tech}', [WaveTechController::class, 'update'])
    ->name('wave.teches.update');

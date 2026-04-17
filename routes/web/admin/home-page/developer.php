<?php
// --- DEVELOPER ---

use App\Http\Controllers\Admin\Constructor\HomePage\Developer\DeveloperItemController;
use App\Http\Controllers\Admin\Constructor\HomePage\Developer\DeveloperSectionController;
use Illuminate\Support\Facades\Route;

Route::get('/developer/sections/{section}/edit', [DeveloperSectionController::class, 'edit'])
    ->name('developer.sections.edit');

Route::put('/developer/sections/{section}', [DeveloperSectionController::class, 'update'])
    ->name('developer.sections.update');

Route::put('/developer/items/{item}', [DeveloperItemController::class, 'update'])
    ->name('developer.items.update');

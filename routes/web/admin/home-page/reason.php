<?php
// --- REASON ---

use App\Http\Controllers\Admin\Constructor\HomePage\Reason\ReasonItemController;
use App\Http\Controllers\Admin\Constructor\HomePage\Reason\ReasonSectionController;
use Illuminate\Support\Facades\Route;

Route::get('/reason/sections/{section}/edit', [ReasonSectionController::class, 'edit'])
    ->name('reason.sections.edit');

Route::put('/reason/sections/{section}', [ReasonSectionController::class, 'update'])
    ->name('reason.sections.update');

Route::put('/reason/items/{item}', [ReasonItemController::class, 'update'])
    ->name('reason.items.update');

Route::put('/reason/items/{item}/activity', [ReasonItemController::class, 'updateActivity'])
    ->name('reason.items.updateActivity');

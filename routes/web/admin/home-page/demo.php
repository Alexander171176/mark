<?php
// --- DEMO ---

use App\Http\Controllers\Admin\Constructor\HomePage\Demo\DemoGroupController;
use App\Http\Controllers\Admin\Constructor\HomePage\Demo\DemoItemController;
use App\Http\Controllers\Admin\Constructor\HomePage\Demo\DemoSectionController;
use Illuminate\Support\Facades\Route;

Route::get('/demo/sections/{section}/edit', [DemoSectionController::class, 'edit'])
    ->name('demo.sections.edit');

Route::put('/demo/sections/{section}', [DemoSectionController::class, 'update'])
    ->name('demo.sections.update');

Route::put('/demo/groups/{group}', [DemoGroupController::class, 'update'])
    ->name('demo.groups.update');

Route::put('/demo/items/{item}', [DemoItemController::class, 'update'])
    ->name('demo.items.update');

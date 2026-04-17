<?php
// --- FEATURE ---

use App\Http\Controllers\Admin\Constructor\HomePage\Feature\FeatureItemController;
use App\Http\Controllers\Admin\Constructor\HomePage\Feature\FeatureSectionController;
use Illuminate\Support\Facades\Route;

Route::get('/feature/sections/{section}/edit', [FeatureSectionController::class, 'edit'])
    ->name('feature.sections.edit');

Route::put('/feature/sections/{section}', [FeatureSectionController::class, 'update'])
    ->name('feature.sections.update');

Route::put('/feature/items/{item}', [FeatureItemController::class, 'update'])
    ->name('feature.items.update');

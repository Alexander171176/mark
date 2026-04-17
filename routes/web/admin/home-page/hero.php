<?php
// --- HERO  ---

use App\Http\Controllers\Admin\Constructor\HomePage\Hero\HeroIconController;
use App\Http\Controllers\Admin\Constructor\HomePage\Hero\HeroScreenshotController;
use App\Http\Controllers\Admin\Constructor\HomePage\Hero\HeroSectionController;
use Illuminate\Support\Facades\Route;

Route::get('/hero/sections/{section}/edit', [HeroSectionController::class, 'edit'])
    ->name('hero.sections.edit');

Route::put('/hero/sections/{section}', [HeroSectionController::class, 'update'])
    ->name('hero.sections.update');

Route::put('/hero/icons/{icon}', [HeroIconController::class, 'update'])
    ->name('hero.icons.update');

Route::put('/hero/screenshots/{screenshot}', [HeroScreenshotController::class, 'update'])
    ->name('hero.screenshots.update');

<?php
// --- QUICKSTART ---

use App\Http\Controllers\Admin\Constructor\HomePage\Quickstart\QuickstartSectionController;
use Illuminate\Support\Facades\Route;

Route::get('/quickstart/sections/{section}/edit', [QuickstartSectionController::class, 'edit'])
    ->name('quickstart.sections.edit');

Route::put('/quickstart/sections/{section}', [QuickstartSectionController::class, 'update'])
    ->name('quickstart.sections.update');

<?php
// --- HOME PAGE actions ---

use App\Http\Controllers\Admin\Constructor\HomePage\HomePageController;
use Illuminate\Support\Facades\Route;

Route::put('/activity', [HomePageController::class, 'updateActivity'])
    ->name('activity.update');

Route::put('/sort', [HomePageController::class, 'updateSort'])
    ->name('sort.update');

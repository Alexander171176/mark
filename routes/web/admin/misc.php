<?php

// --- Маршрут просмотр локалей и другие ---

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/localization', fn () => Inertia::render('Admin/System/Localization/Index'))
    ->name('localization.index');

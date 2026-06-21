<?php

// --- Маршрут просмотр локалей ---

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/localization', fn () => Inertia::render('Admin/System/Localization/Index'))
    ->name('localization.index');

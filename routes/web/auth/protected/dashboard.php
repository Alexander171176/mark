<?php
// Пользовательский дашборд

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/dashboard', fn () => Inertia::render('Dashboard'))
    ->name('dashboard');

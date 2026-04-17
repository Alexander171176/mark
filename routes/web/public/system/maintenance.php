<?php
// Страница технические работы

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/maintenance', function () {
    return Inertia::render('Maintenance');
})->name('maintenance');

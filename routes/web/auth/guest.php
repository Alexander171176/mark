<?php
// --- Логин, Регистрация, Выход ---

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;

// --- Логин ---
Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->middleware(['guest'])
    ->name('login');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware(['guest'])
    ->name('login.store');

// --- Выход ---
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware(['auth'])
    ->name('logout');

// --- Регистрация ---
Route::get('/register', [RegisteredUserController::class, 'create'])
    ->middleware(['guest'])
    ->name('register');

Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware(['guest'])
    ->name('register.store');

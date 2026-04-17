<?php
// Зарегистрированный пользователь

use Illuminate\Support\Facades\Route;
use Laravel\Jetstream\Http\Controllers\Inertia\CurrentUserController;
use Laravel\Jetstream\Http\Controllers\Inertia\OtherBrowserSessionsController;
use Laravel\Jetstream\Http\Controllers\Inertia\ProfilePhotoController;

// Удаление пользователя
Route::delete('/user', [CurrentUserController::class, 'destroy'])
    ->name('current-user.destroy');

// Выход с других браузерных сессий
Route::delete('/user/other-browser-sessions', [OtherBrowserSessionsController::class, 'destroy'])
    ->name('other-browser-sessions.destroy');

// Удаление фото профиля
Route::delete('/user/profile-photo', [ProfilePhotoController::class, 'destroy'])
    ->name('current-user-photo.destroy');

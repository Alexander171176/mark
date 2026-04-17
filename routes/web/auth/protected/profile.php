<?php
// Профиль пользователя

use Illuminate\Support\Facades\Route;
use Laravel\Jetstream\Http\Controllers\Inertia\UserProfileController;

Route::get('/profile', [UserProfileController::class, 'show'])
    ->name('profile.show');

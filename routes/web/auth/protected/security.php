<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\ConfirmablePasswordController;
use Laravel\Fortify\Http\Controllers\ConfirmedPasswordStatusController;
use Laravel\Fortify\Http\Controllers\PasswordController;
use Laravel\Fortify\Http\Controllers\ProfileInformationController;

// Обновление информации профиля
Route::put('/user/profile-information', [ProfileInformationController::class, 'update'])
    ->name('user-profile-information.update');

// Обновление пароля пользователя
Route::put('/user/password', [PasswordController::class, 'update'])
    ->name('user-password.update');

// Подтверждение пароля
Route::get('/user/confirm-password', [ConfirmablePasswordController::class, 'show'])
    ->name('password.confirm');

Route::post('/user/confirm-password', [ConfirmablePasswordController::class, 'store'])
    ->name('password.confirm.store');

Route::get('/user/confirmed-password-status', [ConfirmedPasswordStatusController::class, 'show'])
    ->name('password.confirmation');

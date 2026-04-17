<?php
// Двухфакторная аутентификация (2FA)

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\ConfirmedTwoFactorAuthenticationController;
use Laravel\Fortify\Http\Controllers\RecoveryCodeController;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticationController;
use Laravel\Fortify\Http\Controllers\TwoFactorQrCodeController;
use Laravel\Fortify\Http\Controllers\TwoFactorSecretKeyController;

// Включение 2FA
Route::post('/user/two-factor-authentication', [TwoFactorAuthenticationController::class, 'store'])
    ->name('two-factor.enable');

// Подтверждение включения 2FA
Route::post('/user/confirmed-two-factor-authentication', [ConfirmedTwoFactorAuthenticationController::class, 'store'])
    ->name('two-factor.confirm');

// Отключение 2FA
Route::delete('/user/two-factor-authentication', [TwoFactorAuthenticationController::class, 'destroy'])
    ->name('two-factor.disable');

// Получение QR-кода
Route::get('/user/two-factor-qr-code', [TwoFactorQrCodeController::class, 'show'])
    ->name('two-factor.qr-code');

// Получение секретного ключа
Route::get('/user/two-factor-secret-key', [TwoFactorSecretKeyController::class, 'show'])
    ->name('two-factor.secret-key');

// Получение и генерация recovery-кодов
Route::get('/user/two-factor-recovery-codes', [RecoveryCodeController::class, 'index'])
    ->name('two-factor.recovery-codes');

Route::post('/user/two-factor-recovery-codes', [RecoveryCodeController::class, 'store']);

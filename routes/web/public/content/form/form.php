<?php

use App\Http\Controllers\Public\Default\Form\FormCaptchaController;
use App\Http\Controllers\Public\Default\Form\FormProtectionController;
use App\Http\Controllers\Public\Default\Form\FormSubmissionController;
use Illuminate\Support\Facades\Route;

/**
 * Серверный токен защиты формы.
 */
Route::get(
    '/forms/{formCode}/protection',
    [FormProtectionController::class, 'show']
)
    ->middleware('throttle:20,1')
    ->name('forms.protection');

/**
 * Получение CAPTCHA.
 */
Route::get(
    '/forms/{formCode}/captcha',
    [FormCaptchaController::class, 'show']
)
    ->middleware('throttle:20,1')
    ->name('forms.captcha');

/**
 * Отправка публичной формы.
 */
Route::post(
    '/forms/{formCode}/submit',
    [FormSubmissionController::class, 'store']
)->name('forms.submit');

<?php
// Политика конфиденциальности и условия использования

use Illuminate\Support\Facades\Route;
use Laravel\Jetstream\Http\Controllers\Inertia\PrivacyPolicyController;
use Laravel\Jetstream\Http\Controllers\Inertia\TermsOfServiceController;

// Условия использования
Route::get('/terms-of-service', [TermsOfServiceController::class, 'show'])
    ->name('terms.show');

// Политика конфиденциальности
Route::get('/privacy-policy', [PrivacyPolicyController::class, 'show'])
    ->name('policy.show');

<?php

use App\Http\Controllers\Public\Privacy\PrivacyController;
use Illuminate\Support\Facades\Route;

// Страница соглашения на обработку данных
Route::get('/privacy', [PrivacyController::class, 'index'])
    ->name('privacy.index');

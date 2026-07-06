<?php
// Страница Политика конфиденциальности.

use App\Http\Controllers\Public\Privacy\PrivacyController;
use Illuminate\Support\Facades\Route;

Route::get('/privacy', [PrivacyController::class, 'index'])
    ->name('privacy.index');

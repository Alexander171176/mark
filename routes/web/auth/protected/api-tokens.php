<?php
// API токены

use Illuminate\Support\Facades\Route;
use Laravel\Jetstream\Http\Controllers\Inertia\ApiTokenController;

// Страница со списком токенов
Route::get('/api-tokens', [ApiTokenController::class, 'index'])->name('api-tokens.index');
// Создание нового токена
Route::post('/api-tokens', [ApiTokenController::class, 'store'])->name('api-tokens.store');
// Обновление прав токена
Route::put('/api-tokens/{tokenId}', [ApiTokenController::class, 'update'])->name('api-tokens.update');
// Удаление токена
Route::delete('/api-tokens/{tokenId}', [ApiTokenController::class, 'destroy'])->name('api-tokens.destroy');

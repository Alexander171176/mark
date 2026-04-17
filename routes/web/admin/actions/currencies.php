<?php

use App\Http\Controllers\Admin\Finance\Currency\CurrencyController;
use Illuminate\Support\Facades\Route;

// Назначить валюту базовой
Route::put('/currencies/{currency}/default',
    [CurrencyController::class, 'setDefault'])
    ->name('currencies.setDefault');

// Ручной инлайн “снимок” курса относительно текущей базовой (is_default)
Route::post('/currencies/{currency}/rate',
    [CurrencyController::class, 'updateRate'])
    ->name('currencies.updateRate');

// Обновить курсы с CBR для выбранной базы (currency = база)
Route::post('/currencies/{currency}/refresh-rates',
    [CurrencyController::class, 'refreshRates'])
    ->name('currencies.refreshRates');

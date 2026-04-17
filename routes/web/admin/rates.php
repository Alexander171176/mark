<?php

// --- Currency rates (история/редактирование на уровне базовой валюты) ---

use App\Http\Controllers\Admin\Finance\Currency\CurrencyRateController;
use Illuminate\Support\Facades\Route;

Route::prefix('currencies/{currency}')
    ->name('currencies.')
    ->group(function () {

        Route::get('/rates', [CurrencyRateController::class, 'index'])
            ->name('rates.index');

        Route::post('/rates/refresh', [CurrencyRateController::class, 'refresh'])
            ->name('rates.refresh');

        Route::post('/rates', [CurrencyRateController::class, 'store'])
            ->name('rates.store');

        // update = create history snapshot (мы это заложили в контроллер)
        Route::put('/rates/{rate}', [CurrencyRateController::class, 'update'])
            ->name('rates.update');

        Route::delete('/rates/{rate}', [CurrencyRateController::class, 'destroy'])
            ->name('rates.destroy');

        Route::post('/rates/bulk', [CurrencyRateController::class, 'bulkUpsert'])
            ->name('rates.bulk');
    });

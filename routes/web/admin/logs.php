<?php

// --- Маршруты для показа, очистки логов и скачивания логов ---

use App\Http\Controllers\Admin\System\Log\LogController;
use Illuminate\Support\Facades\Route;

Route::prefix('logs')
    ->name('logs.')
    ->controller(LogController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');         // Страница логов
        Route::delete('/', 'clear')->name('clear');      // Очистить логи
        Route::get('/download', 'download')->name('download'); // Скачать логи
    });

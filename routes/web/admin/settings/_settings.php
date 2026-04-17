<?php

// --- Настройки отображения в админке ---

use Illuminate\Support\Facades\Route;

Route::prefix('settings')->name('settings.')->group(function () {

    // --- создание снапшотов настроек системы ---
    require __DIR__ . '/snapshots.php';

    // Количество на странице
    require __DIR__ . '/count.php';

    // Тип сортировки
    require __DIR__ . '/sort.php';

});

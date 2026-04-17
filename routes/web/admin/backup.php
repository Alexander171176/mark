<?php

// --- Маршруты для страницы архивации и восстановления БД ---

use App\Http\Controllers\Admin\System\DatabaseBackupController;
use Illuminate\Support\Facades\Route;

Route::prefix('backup')
    ->name('backup.')
    ->controller(DatabaseBackupController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index'); // Страница архивации и восстановления
        Route::get('/list', 'list')->name('list');              // Показать все бэкапы
        Route::post('/create', 'create')->name('create');       // Создать бэкап
        Route::post('/restore', 'restore')->name('restore');    // Восстановить из бэкапа
        Route::get('/download/{filename}', 'download')->name('download'); // Скачать бэкап
        Route::delete('/delete', 'delete')->name('delete');     // Удалить бэкап
    });

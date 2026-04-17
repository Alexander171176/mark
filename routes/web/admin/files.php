<?php

// --- Маршруты для страницы архивации и восстановления сайта из архива ---

use App\Http\Controllers\Admin\System\FileBackupController;
use Illuminate\Support\Facades\Route;

Route::prefix('files')->name('files.')->group(function () {
    Route::get('/', [FileBackupController::class, 'index'])->name('index');
    Route::post('/create', [FileBackupController::class, 'create'])->name('create');
    Route::post('/restore', [FileBackupController::class, 'restore'])->name('restore');
    Route::delete('/delete', [FileBackupController::class, 'delete'])->name('delete');
    Route::get('/list', [FileBackupController::class, 'list'])->name('list');
    Route::get('/download/{file}', [FileBackupController::class, 'download'])->name('download');
});

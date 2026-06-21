<?php

// Резервное копирование и восстановление БД
use App\Http\Controllers\Admin\System\DatabaseBackupController;
use Illuminate\Support\Facades\Route;

Route::prefix('backup')
    ->name('backup.')
    ->group(function () {
        Route::get('/', [DatabaseBackupController::class, 'index'])->name('index');
        Route::post('/create', [DatabaseBackupController::class, 'create'])->name('create');

        Route::post('/restore/start', [DatabaseBackupController::class, 'restoreStart'])->name('restore.start');
        Route::get('/restore/status/{job}', [DatabaseBackupController::class, 'restoreStatus'])->name('restore.status');

        Route::delete('/delete', [DatabaseBackupController::class, 'delete'])->name('delete');
        Route::get('/list', [DatabaseBackupController::class, 'list'])->name('list');
        Route::get('/download/{filename}', [DatabaseBackupController::class, 'download'])
            ->where('filename', '.*')
            ->name('download');
    });

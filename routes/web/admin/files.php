<?php

use App\Http\Controllers\Admin\System\FileBackupController;
use Illuminate\Support\Facades\Route;

Route::prefix('files')
    ->name('files.')
    ->group(function () {
        Route::get('/', [FileBackupController::class, 'index'])->name('index');
        Route::post('/start', [FileBackupController::class, 'start'])->name('start');
        Route::post('/process', [FileBackupController::class, 'process'])->name('process');
        Route::get('/status/{job}', [FileBackupController::class, 'status'])->name('status');
        Route::delete('/delete', [FileBackupController::class, 'delete'])->name('delete');
        Route::get('/list', [FileBackupController::class, 'list'])->name('list');
        Route::get('/download/{file}', [FileBackupController::class, 'download'])
            ->where('file', '.*')
            ->name('download');
    });

<?php

// --- Системные маршруты для отладки ---

use App\Http\Controllers\Admin\System\ComposerController;
use App\Http\Controllers\Admin\System\EnvController;
use App\Http\Controllers\Admin\System\PackageController;
use App\Http\Controllers\Admin\System\PhpInfoController;
use App\Http\Controllers\Admin\System\RobotController;
use Illuminate\Support\Facades\Route;

Route::get('/phpinfo', [PhpInfoController::class, 'index'])->name('phpinfo.index');
Route::get('/composer', [ComposerController::class, 'index'])->name('composer.index');
Route::get('/package', [PackageController::class, 'index'])->name('package.index');
Route::get('/env', [EnvController::class, 'index'])->name('env.index');
Route::get('/robot', [RobotController::class, 'index'])->name('robot.index');
Route::put('/robot', [RobotController::class, 'update'])->name('robot.update');

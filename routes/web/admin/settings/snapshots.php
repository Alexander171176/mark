<?php

use App\Http\Controllers\Admin\System\Snapshot\SettingsSnapshotController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->prefix('/snapshots')->group(function () {
    Route::post('/build-public', [SettingsSnapshotController::class, 'buildPublic']);
    Route::post('/build-admin',  [SettingsSnapshotController::class, 'buildAdmin']);
});

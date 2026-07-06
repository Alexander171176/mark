<?php
// маршруты аналитики импорт и очистка лога в БД

use App\Http\Controllers\Admin\Analytics\AnalyticsCleanup\AnalyticsCleanupController;
use App\Http\Controllers\Admin\Analytics\AnalyticsImport\AnalyticsImportController;
use Illuminate\Support\Facades\Route;

Route::post('/analytics/import', AnalyticsImportController::class)
    ->name('analytics.import');

Route::delete('/analytics/cleanup', [AnalyticsCleanupController::class, 'destroy'])
    ->name('analytics.cleanup.destroy');

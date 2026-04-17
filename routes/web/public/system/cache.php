<?php
// Очистка кэша
use App\Http\Controllers\Admin\System\SystemController;
use Illuminate\Support\Facades\Route;

Route::post('/admin/cache/clear', [SystemController::class, 'clearCache'])
    ->middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])
    ->name('cache.clear');

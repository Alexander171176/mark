<?php

// Обновление только value настройки

use App\Http\Controllers\Admin\System\Setting\SettingController;
use Illuminate\Support\Facades\Route;

Route::put('/settings/{setting}/value',
    [SettingController::class, 'updateValue'])->name('settings.updateValue');

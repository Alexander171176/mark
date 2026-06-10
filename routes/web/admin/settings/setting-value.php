<?php

use App\Http\Controllers\Admin\System\Setting\SettingController;
use Illuminate\Support\Facades\Route;

Route::put(
    '/setting-value',
    [SettingController::class, 'updateSettingValue']
)->name('updateSettingValue');

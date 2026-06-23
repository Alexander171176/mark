<?php

use App\Http\Controllers\Admin\System\ImagePreset\ImagePresetController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Image Presets
|--------------------------------------------------------------------------
|
| Пресеты обработки изображений.
| Создание и удаление отключены.
| Доступны только список и редактирование.
|
*/

Route::get(
    '/image-presets',
    [ImagePresetController::class, 'index']
)->name('imagePresets.index');

Route::get(
    '/image-presets/{imagePreset}/edit',
    [ImagePresetController::class, 'edit']
)
    ->whereNumber('imagePreset')
    ->name('imagePresets.edit');

Route::put(
    '/image-presets/{imagePreset}',
    [ImagePresetController::class, 'update']
)
    ->whereNumber('imagePreset')
    ->name('imagePresets.update');

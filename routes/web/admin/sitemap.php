<?php

// --- Маршруты для страницы генерации карты в xml ---

use App\Http\Controllers\Admin\System\SitemapController;
use Illuminate\Support\Facades\Route;

Route::prefix('sitemap')
    ->name('sitemap.')
    ->controller(SitemapController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index'); // Генерация и просмотр sitemap.xml
        Route::post('/', 'generate')->name('generate');// кнопка «Сгенерировать»
        Route::get('/file', 'download')->name('download');// скачать
    });

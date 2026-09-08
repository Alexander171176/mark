<?php

// --- Маршруты для страницы генерации карты в xml ---

use App\Http\Controllers\Admin\System\SitemapController;
use Illuminate\Support\Facades\Route;

Route::prefix('sitemap')
    ->name('sitemap.')
    ->controller(SitemapController::class)
    ->group(function () {
        // Генерация и просмотр sitemap.xml
        Route::get('/', 'index')
            ->name('index');

        // Кнопка «Сгенерировать»
        Route::post('/', 'generate')
            ->name('generate');

        // Получить содержимое выбранного sitemap-файла
        Route::get('/content', 'content')
            ->name('content');

        // Скачать выбранный sitemap-файл
        Route::get('/file', 'download')
            ->name('download');
    });

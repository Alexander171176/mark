<?php

// --- Маршруты Панели Администратора ---

use Illuminate\Support\Facades\Route;

// --- Маршруты Панели Администратора ---
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])
    ->prefix('admin')->name('admin.')
    ->group(function () {

        // --- Главная страница админки ---
        require __DIR__ . '/index.php';

        // --- Маршруты для страницы генерации карты в xml ---
        require __DIR__ . '/sitemap.php';

        // --- Маршруты для страницы архивации и восстановления сайта из архива ---
        require __DIR__ . '/files.php';

        // --- Маршруты для страницы архивации и восстановления БД ---
        require __DIR__ . '/backup.php';

        // --- Маршруты для показа, очистки логов и скачивания логов ---
        require __DIR__ . '/logs.php';

        // --- Системные маршруты для отладки ---
        require __DIR__ . '/system.php';

        // --- Настройки отображения в админке ---
        require __DIR__ . '/settings/_settings.php';

        // --- Основные CRUD Ресурсы ---
        require __DIR__ . '/resources.php';

        // --- Маршруты удаления связей ManyToMany ---
        require __DIR__ . '/deletes.php';

        // --- Currency rates (история/редактирование на уровне базовой валюты) ---
        require __DIR__ . '/rates.php';

        // --- Маршруты для дополнительных действий ---
        require __DIR__ . '/actions/_actions.php';

        // --- Маршруты для редактирования главной страницы ---
        require __DIR__ . '/home-page/_home-page.php';

        // --- Маршрут просмотр локалей и другие ---
        require __DIR__ . '/misc.php';
    });

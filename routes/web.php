<?php

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use UniSharp\LaravelFilemanager\Lfm;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::group([
    'prefix' => LaravelLocalization::setLocale(),        // /ru или /en
    'middleware' => [
        'localeSessionRedirect',     // перенаправляет из / на /ru или /en
        'localizationRedirect',      // сохраняет префикс в URL при смене языка
        'localeViewPath',            // подтягивает view из ресурсов по языку
        'web',                       // (необязательно, т.к. web.php уже под web-middleware)
    ]
], function () {

    // --- Глобальные настройки и публичные маршруты ---
    require __DIR__ . '/web/public/_public.php';

    // --- Аутентификация Jetstream / Fortify ---
    require __DIR__ . '/web/auth/_auth.php';

    // --- Стандартные маршруты Jetstream (protected) ---
    require __DIR__ . '/web/auth/protected/_protected.php';

    // --- Маршруты Панели Администратора ---
    require __DIR__ . '/web/admin/_admin.php';

    // --- Остальные маршруты (Filemanager, Redis test) ---
    Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web', 'auth']], function () {
        Lfm::routes();
    });

});

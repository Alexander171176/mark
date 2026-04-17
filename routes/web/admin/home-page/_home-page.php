<?php

// --- Маршруты для редактирования главной страницы ---

use Illuminate\Support\Facades\Route;

Route::prefix('home-page')->as('home-page.')->group(function () {

    // Страница конструктора (Index.vue)
    require __DIR__ . '/index.php';

    // --- HERO  ---
    require __DIR__ . '/hero.php';

    // --- WAVE ---
    require __DIR__ . '/wave.php';

    // --- FEATURE ---
    require __DIR__ . '/feature.php';

    // --- DEVELOPER ---
    require __DIR__ . '/developer.php';

    // --- QUICKSTART ---
    require __DIR__ . '/quickstart.php';

    // --- DEMO ---
    require __DIR__ . '/demo.php';

    // --- QUALITY ---
    require __DIR__ . '/quality.php';

    // --- COMPONENT ---
    require __DIR__ . '/component.php';

    // --- REASON ---
    require __DIR__ . '/reason.php';

    // Общие actions главной страницы
    require __DIR__ . '/actions.php';

});

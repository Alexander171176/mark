<?php

// --- Маршруты для дополнительных действий ---

use Illuminate\Support\Facades\Route;

Route::prefix('actions')->name('actions.')->group(function () { // Группируем доп. действия

    // Обновление только value настройки
    require __DIR__ . '/setting-value.php';

    // Клонирование
    require __DIR__ . '/clone.php';

    // Переключение активности
    require __DIR__ . '/activity.php';

    // Переключение активности (массовое)
    require __DIR__ . '/bulk-activity.php';

    // Переключение Left/Main/Right
    require __DIR__ . '/left.php';
    require __DIR__ . '/main.php';
    require __DIR__ . '/right.php';

    // Переключение Left/Main/Right массовое
    require __DIR__ . '/bulk-left.php';
    require __DIR__ . '/bulk-main.php';
    require __DIR__ . '/bulk-right.php';

    // Обновление сортировки (bulk / drag-and-drop)
    require __DIR__ . '/bulk-sort.php';

    // Обновление сортировки (1 запись)
    require __DIR__ . '/sort.php';

    // Одобрение комментария
    require __DIR__ . '/comment-approve.php';

    // Одобрение рубрики
    require __DIR__ . '/approve.php';

    // --- Cohort Enrollments: статусы и заметки ---
    require __DIR__ . '/cohort-enrollment.php';

    // массовое обновление/удаление статусов прохождений викторин
    require __DIR__ . '/quiz-attempts-bulk-status.php';

    // --- Currencies actions ---
    require __DIR__ . '/currencies.php';

    // Массовое удаление
    require __DIR__ . '/bulk-delete.php';
});

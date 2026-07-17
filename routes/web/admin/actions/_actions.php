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

    // Переключение показывать в главном меню
    require __DIR__ . '/menu.php';

    // Переключение страниц
    require __DIR__ . '/page.php';

    // Переключение Left/Main/Right
    require __DIR__ . '/left.php';
    require __DIR__ . '/main.php';
    require __DIR__ . '/right.php';

    // Переключение Left/Main/Right массовое
    require __DIR__ . '/bulk-left.php';
    require __DIR__ . '/bulk-main.php';
    require __DIR__ . '/bulk-right.php';

    // Переключение флагов курсов New/Hit/Sale
    require __DIR__ . '/flags.php';

    // Переключение флагов курсов New/Hit/Sale массовое
    require __DIR__ . '/flags-bulk.php';

    // Обновление сортировки (bulk / drag-and-drop)
    require __DIR__ . '/bulk-sort.php';

    // Обновление сортировки (1 запись)
    // require __DIR__ . '/sort.php';

    // Одобрение комментария
    require __DIR__ . '/comment-approve.php';

    // Одобрение рубрики
    require __DIR__ . '/approve.php';

    // --- Cohort Enrollments: статусы и заметки ---
    require __DIR__ . '/cohort-enrollment.php';

    // массовое обновление/удаление статусов прохождений викторин
    require __DIR__ . '/quiz-attempts-bulk-status.php';

    // массовое обновление/удаление статусов прохождений викторин
    require __DIR__ . '/school-quiz-attempt-items-bulk-correct.php';

    // --- Currencies actions ---
    require __DIR__ . '/currencies.php';

    // Массовое удаление
    require __DIR__ . '/bulk-destroy.php';
});

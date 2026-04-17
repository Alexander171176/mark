<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])
    ->group(function () {


        require __DIR__ . '/dashboard.php'; // Пользовательский дашборд
        require __DIR__ . '/profile.php'; // Профиль пользователя

        require __DIR__ . '/api-tokens.php'; // API токены
        require __DIR__ . '/user.php'; // Зарегистрированный пользователь

        require __DIR__ . '/security.php'; // Обновление информации профиля, подтверждение пароля пользователя
        require __DIR__ . '/two-factor.php'; // Двухфакторная аутентификация (2FA)

        require __DIR__ . '/teams.php'; // Работа с командами (Teams)
        require __DIR__ . '/legal.php'; // Политика конфиденциальности и условия использования
    });

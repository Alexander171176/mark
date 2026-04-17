<?php

// --- Аутентификация Jetstream/Fortify (guest + часть auth) ---

// Гостевые/публичные auth-маршруты
require __DIR__ . '/guest.php';
require __DIR__ . '/password.php';

// Email verification (часть под auth — но логически держим в auth ветке)
require __DIR__ . '/email-verification.php';

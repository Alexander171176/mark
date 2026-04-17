<?php

use App\Http\Middleware\CheckDowntime;
use Illuminate\Support\Facades\Route;

Route::middleware([CheckDowntime::class])->group(function () {

    // --- System: cache / maintenance / fallback ---
    require __DIR__ . '/system/_system.php';

    // --- Public pages (home, etc.) ---
    require __DIR__ . '/pages/_pages.php';

    // --- Content entities (rubrics/articles/tags/videos) ---
    require __DIR__ . '/content/_content.php';

    // --- Interactions (likes etc.) ---
    require __DIR__ . '/interactions/_interactions.php';
});

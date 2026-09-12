<?php

// Главная страница публичной части

use App\Context\Location\LocationContext;
use App\Http\Controllers\Public\PublicTemplateDispatcherController;
use Illuminate\Support\Facades\Route;

/**
 * Вход без Location.
 *
 * Перенаправляет на главную страницу
 * текущей определённой Location.
 */
Route::get('/', function (
    LocationContext $context
) {
    $location = $context->current();

    if (!$location) {
        abort(404);
    }

    return redirect()->route('home');
})->name('home.gateway');

/**
 * Главная страница текущей Location.
 */
Route::get('/{location}', [PublicTemplateDispatcherController::class, 'dispatch'])
    ->where('location', '[A-Za-z0-9\-]+')
    ->defaults('_templateController', 'HomeController')
    ->defaults('_templateAction', 'index')
    ->name('home');

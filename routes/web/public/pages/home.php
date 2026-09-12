<?php

// Главная страница публичной части

use App\Http\Controllers\Public\PublicTemplateDispatcherController;
use Illuminate\Support\Facades\Route;

/**
 * Главная страница публичной части.
 */
Route::get('/', [PublicTemplateDispatcherController::class, 'dispatch'])
    ->defaults('_templateController', 'HomeController')
    ->defaults('_templateAction', 'index')
    ->name('home');

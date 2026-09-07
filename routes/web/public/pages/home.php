<?php
// Главная страница публичной части

use App\Http\Controllers\Public\PublicTemplateDispatcherController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicTemplateDispatcherController::class, 'dispatch'])
    ->defaults('_templateController', 'HomeController')
    ->defaults('_templateAction', 'index')
    ->name('home');

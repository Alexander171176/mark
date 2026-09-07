<?php
// Модули школы

use App\Http\Controllers\Public\PublicTemplateDispatcherController;
use Illuminate\Support\Facades\Route;

Route::get('/school/modules', [PublicTemplateDispatcherController::class, 'dispatch'])
    ->defaults('_templateController', 'School\\SchoolModule\\SchoolModuleController')
    ->defaults('_templateAction', 'index')
    ->name('public.schoolModules.index');

Route::get('/school/modules/{slug}', [PublicTemplateDispatcherController::class, 'dispatch'])
    ->defaults('_templateController', 'School\\SchoolModule\\SchoolModuleController')
    ->defaults('_templateAction', 'show')
    ->name('public.schoolModules.show');

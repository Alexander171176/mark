<?php
// Рубрики Блога

use App\Http\Controllers\Public\PublicTemplateDispatcherController;
use Illuminate\Support\Facades\Route;

Route::get('/blog/rubrics', [PublicTemplateDispatcherController::class, 'dispatch'])
    ->defaults('_templateController', 'Blog\\BlogRubric\\BlogRubricController')
    ->defaults('_templateAction', 'index')
    ->name('public.blogRubrics.index');

Route::get('/blog/menu/rubrics', [PublicTemplateDispatcherController::class, 'dispatch'])
    ->defaults('_templateController', 'Blog\\BlogRubric\\BlogRubricController')
    ->defaults('_templateAction', 'menuRubrics')
    ->name('public.blogRubrics.menu');

Route::get('/blog/rubrics/{url}', [PublicTemplateDispatcherController::class, 'dispatch'])
    ->defaults('_templateController', 'Blog\\BlogRubric\\BlogRubricController')
    ->defaults('_templateAction', 'show')
    ->name('public.blogRubrics.show');

<?php
// Видео Блога

use App\Http\Controllers\Public\PublicTemplateDispatcherController;
use Illuminate\Support\Facades\Route;

Route::get('/videos', [PublicTemplateDispatcherController::class, 'dispatch'])
    ->defaults('_templateController', 'Blog\\BlogVideo\\BlogVideoController')
    ->defaults('_templateAction', 'index')
    ->name('public.blogVideos.index');

Route::get('/videos/{url}', [PublicTemplateDispatcherController::class, 'dispatch'])
    ->defaults('_templateController', 'Blog\\BlogVideo\\BlogVideoController')
    ->defaults('_templateAction', 'show')
    ->name('public.blogVideos.show');

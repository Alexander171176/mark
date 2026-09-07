<?php
// Теги Блога

use App\Http\Controllers\Public\PublicTemplateDispatcherController;
use Illuminate\Support\Facades\Route;

Route::get('/blog/tags/{slug}', [PublicTemplateDispatcherController::class, 'dispatch'])
    ->defaults('_templateController', 'Blog\\BlogTag\\BlogTagController')
    ->defaults('_templateAction', 'show')
    ->name('public.blogTags.show');

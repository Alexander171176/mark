<?php
// Посты Блога

use App\Http\Controllers\Public\PublicTemplateDispatcherController;
use Illuminate\Support\Facades\Route;

Route::get('/blog/articles', [PublicTemplateDispatcherController::class, 'dispatch'])
    ->defaults('_templateController', 'Blog\\BlogArticle\\BlogArticleController')
    ->defaults('_templateAction', 'index')
    ->name('public.blogArticles.index');

Route::get('/blog/articles/{url}', [PublicTemplateDispatcherController::class, 'dispatch'])
    ->defaults('_templateController', 'Blog\\BlogArticle\\BlogArticleController')
    ->defaults('_templateAction', 'show')
    ->name('public.blogArticles.show');

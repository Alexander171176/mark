<?php
// Рубрики Блога из шаблона
use Illuminate\Support\Facades\Route;

$siteLayout = config('site_settings.siteLayout', 'Default');

$publicRubricController = "App\\Http\\Controllers\\Public\\{$siteLayout}\\Blog\\BlogRubric\\BlogRubricController";

Route::get('/blog/rubrics', [$publicRubricController, 'index'])
    ->name('public.blogRubrics.index');

Route::get('/blog/menu/rubrics', [$publicRubricController, 'menuRubrics'])
    ->name('public.blogRubrics.menu');

Route::get('/blog/rubrics/{url}', [$publicRubricController, 'show'])
    ->name('public.blogRubrics.show');

<?php
// Теги Блога из шаблона

use Illuminate\Support\Facades\Route;

$siteLayout = config('site_settings.siteLayout', 'Default');

$publicTagController = "App\\Http\\Controllers\\Public\\{$siteLayout}\\Blog\\TagController";

Route::get('/blog/tags/{url}', [$publicTagController, 'show'])
    ->name('public.tags.show');

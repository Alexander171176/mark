<?php
// Главная страница из шаблона

use Illuminate\Support\Facades\Route;

$siteLayout = config('site_settings.siteLayout', 'Default');

$publicHomeController = "App\\Http\\Controllers\\Public\\{$siteLayout}\\HomeController";

Route::get('/', [$publicHomeController, 'index'])
    ->name('home');

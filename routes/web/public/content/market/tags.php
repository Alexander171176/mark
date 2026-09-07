<?php

// Теги товаров маркетплейса

use App\Http\Controllers\Public\PublicTemplateDispatcherController;
use Illuminate\Support\Facades\Route;

Route::get('/catalog/tags/{url}', [PublicTemplateDispatcherController::class, 'dispatch'])
    ->defaults('_templateController', 'Market\\MarketTag\\MarketTagController')
    ->defaults('_templateAction', 'show')
    ->name('public.marketTags.show');

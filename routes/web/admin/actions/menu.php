<?php

use App\Http\Controllers\Admin\Market\MarketCategory\MarketCategoryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Market Categories
|--------------------------------------------------------------------------
*/

Route::put('/market-categories/{marketCategory}/in-menu',
    [MarketCategoryController::class, 'updateInMenu'])
    ->whereNumber('marketCategory')
    ->name('marketCategories.updateInMenu');


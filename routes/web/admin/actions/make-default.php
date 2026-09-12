<?php

use App\Http\Controllers\Admin\Market\MarketProductVariant\MarketProductVariantController;
use App\Http\Controllers\Admin\System\Location\LocationController;
use Illuminate\Support\Facades\Route;

Route::put('/market-product-variants/{marketProductVariant}/make-default',
    [MarketProductVariantController::class, 'makeDefault'])
    ->whereNumber('marketProductVariant')
    ->name('marketProductVariants.makeDefault');

Route::put('/locations/{location}/make-default',
    [LocationController::class, 'makeDefault'])
    ->whereNumber('location')
    ->name('locations.makeDefault');

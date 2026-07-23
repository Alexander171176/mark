<?php

use App\Http\Controllers\Admin\Market\MarketProductVariant\MarketProductVariantController;
use Illuminate\Support\Facades\Route;

Route::put('/market-product-variants/{marketProductVariant}/make-default',
    [MarketProductVariantController::class, 'makeDefault'])
    ->whereNumber('marketProductVariant')
    ->name('marketProductVariants.makeDefault');

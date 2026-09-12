<?php

use App\Context\Location\LocationContext;
use Illuminate\Support\Facades\Route;

/**
 * Проверка LocationContext.
 */
Route::get('/location/context', function (
    LocationContext $context
) {
    return response()->json([
        'location' => $context->current(),
        'source' => $context->source(),
    ]);
})->name('location.context');

/**
 * Проверка LocationContext из URL.
 */
Route::get('/{location}/location/context', function (
    LocationContext $context
) {
    return response()->json([
        'location' => $context->current(),
        'source' => $context->source(),
    ]);
})
    ->where('location', '[A-Za-z0-9\-]+')
    ->name('location.route.context');

/**
 * Временный тест генерации URL
 * с Location по умолчанию.
 */
Route::get('/location/url-test/{location}', function () {
    return response()->json([
        'url' => route('location.url-test'),
    ]);
})
    ->where('location', '[A-Za-z0-9\-]+')
    ->name('location.url-test');

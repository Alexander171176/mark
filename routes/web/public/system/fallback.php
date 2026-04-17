<?php
// Обработка 404 и режима обслуживания

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::fallback(function (Request $request) {
    if (config('site_settings.downtimeSite', 'false') === 'true'
        && !$request->is('admin/*')
        && !$request->is(app()->getLocale() . '/admin*')) {
        return Inertia::render('Maintenance');
    }

    return Inertia::render('NotFound')
        ->toResponse($request)
        ->setStatusCode(404);
});

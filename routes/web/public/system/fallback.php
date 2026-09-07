<?php
// Обработка 404 и режима обслуживания

use App\Services\SiteSettings\PublicSettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::fallback(function (Request $request) {
    $publicSettings = app(PublicSettingsService::class);

    if ($publicSettings->string('downtimeSite', 'false') === 'true'
        && !$request->is('admin/*')
        && !$request->is(app()->getLocale() . '/admin*')) {
        return Inertia::render('Maintenance');
    }

    return Inertia::render('NotFound')
        ->toResponse($request)
        ->setStatusCode(404);
});

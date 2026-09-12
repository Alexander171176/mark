<?php

use App\Http\Controllers\Public\PublicTemplateDispatcherController;
use App\Http\Middleware\Public\ResolveLocation;
use Illuminate\Support\Facades\Route;

Route::middleware([
    ResolveLocation::class,
])->group(function () {

    Route::get('/{location}/{slug?}', [PublicTemplateDispatcherController::class, 'dispatch'])
        ->where([
            'location' => '[A-Za-z0-9\-]+',
            'slug' => '.*',
        ])
        ->defaults('_templateController', 'Cms\\CmsPagePublicController')
        ->defaults('_templateAction', 'show')
        ->name('public.cmsPages.show');
});

<?php

use App\Http\Controllers\Public\PublicTemplateDispatcherController;
use Illuminate\Support\Facades\Route;

Route::get('/{slug?}', [PublicTemplateDispatcherController::class, 'dispatch'])
    ->where([
        'slug' => '.*',
    ])
    ->defaults('_templateController', 'Cms\\CmsPagePublicController')
    ->defaults('_templateAction', 'show')
    ->name('public.cmsPages.show');

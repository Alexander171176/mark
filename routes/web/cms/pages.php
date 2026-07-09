<?php


use App\Http\Controllers\Public\Default\Cms\CmsPagePublicController;
use Illuminate\Support\Facades\Route;

Route::get('/{slug?}', [CmsPagePublicController::class, 'show'])
    ->where('slug', '.*')
    ->name('public.cmsPages.show');

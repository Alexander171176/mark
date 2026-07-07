<?php

use App\Http\Controllers\Admin\Cms\CmsPage\CmsPageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| CMS Pages
|--------------------------------------------------------------------------
*/

Route::put('/cms-pages/{cmsPage}/in-menu',
    [CmsPageController::class, 'updateInMenu'])
    ->whereNumber('cmsPage')
    ->name('cmsPages.updateInMenu');

Route::put('/cms-pages/{cmsPage}/in-footer',
    [CmsPageController::class, 'updateInFooter'])
    ->whereNumber('cmsPage')
    ->name('cmsPages.updateInFooter');

Route::put('/cms-pages/{cmsPage}/show-content',
    [CmsPageController::class, 'updateShowContent'])
    ->whereNumber('cmsPage')
    ->name('cmsPages.updateShowContent');

Route::put('/cms-pages/{cmsPage}/show-seo',
    [CmsPageController::class, 'updateShowSeo'])
    ->whereNumber('cmsPage')
    ->name('cmsPages.updateShowSeo');

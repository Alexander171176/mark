<?php

// Одобрение администратором
use App\Http\Controllers\Admin\Blog\BlogArticle\BlogArticleController;
use App\Http\Controllers\Admin\Blog\BlogBanner\BlogBannerController;
use App\Http\Controllers\Admin\Blog\BlogRubric\BlogRubricController;
use App\Http\Controllers\Admin\Blog\BlogTag\BlogTagController;
use App\Http\Controllers\Admin\Blog\BlogVideo\BlogVideoController;
use App\Http\Controllers\Admin\Market\MarketAttributeGroup\MarketAttributeGroupController;
use App\Http\Controllers\Admin\Market\MarketBrand\MarketBrandController;
use App\Http\Controllers\Admin\Market\MarketCategory\MarketCategoryController;
use App\Http\Controllers\Admin\Market\MarketCompany\MarketCompanyController;
use App\Http\Controllers\Admin\Market\MarketShop\MarketShopController;
use App\Http\Controllers\Admin\Market\MarketTag\MarketTagController;
use Illuminate\Support\Facades\Route;

Route::put('/blog-rubrics/{blogRubric}/approve',
    [BlogRubricController::class, 'approve'])
    ->name('blogRubrics.approve');

Route::put('/blog-articles/{blogArticle}/approve',
    [BlogArticleController::class, 'approve'])
    ->name('blogArticles.approve');

Route::put('/blog-tags/{blogTag}/approve',
    [BlogTagController::class, 'approve'])
    ->name('blogTags.approve');

Route::put('/blog-banners/{blogBanner}/approve',
    [BlogBannerController::class, 'approve'])
    ->name('blogBanners.approve');

Route::put('/blog-videos/{blogVideo}/approve',
    [BlogVideoController::class, 'approve'])
    ->name('blogVideos.approve');

Route::put('/market-companies/{marketCompany}/approve',
    [MarketCompanyController::class, 'approve'])
    ->whereNumber('marketCompany')
    ->name('marketCompanies.approve');

Route::put('/market-shops/{marketShop}/approve',
    [MarketShopController::class, 'approve'])
    ->whereNumber('marketShop')
    ->name('marketShops.approve');

Route::put('/market-categories/{marketCategory}/approve',
    [MarketCategoryController::class, 'approve'])
    ->whereNumber('marketCategory')
    ->name('marketCategories.approve');

Route::put('/market-brands/{marketBrand}/approve',
    [MarketBrandController::class, 'approve'])
    ->whereNumber('marketBrand')
    ->name('marketBrands.approve');

Route::put('/market-tags/{marketTag}/approve',
    [MarketTagController::class, 'approve'])
    ->whereNumber('marketTag')
    ->name('marketTags.approve');

Route::put('/market-attribute-groups/{marketAttributeGroup}/approve',
    [MarketAttributeGroupController::class, 'approve'])
    ->whereNumber('marketAttributeGroup')
    ->name('marketAttributeGroups.approve');

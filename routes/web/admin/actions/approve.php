<?php

// Одобрение администратором
use App\Http\Controllers\Admin\Blog\BlogArticle\BlogArticleController;
use App\Http\Controllers\Admin\Blog\BlogBanner\BlogBannerController;
use App\Http\Controllers\Admin\Blog\BlogRubric\BlogRubricController;
use App\Http\Controllers\Admin\Blog\BlogTag\BlogTagController;
use App\Http\Controllers\Admin\Blog\BlogVideo\BlogVideoController;
use App\Http\Controllers\Admin\Market\MarketCompany\MarketCompanyController;
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

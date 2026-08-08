<?php

// Лайки пользователей

use Illuminate\Support\Facades\Route;

$siteLayout = config('site_settings.siteLayout', 'Default');

$publicArticleController =
    "App\\Http\\Controllers\\Public\\{$siteLayout}\\Blog\\BlogArticle\\BlogArticleController";
$publicVideoController   =
    "App\\Http\\Controllers\\Public\\{$siteLayout}\\Blog\\BlogVideo\\BlogVideoController";
$publicTrackController   =
    "App\\Http\\Controllers\\Public\\{$siteLayout}\\School\\SchoolTrack\\SchoolTrackController";
$publicCourseController  =
    "App\\Http\\Controllers\\Public\\{$siteLayout}\\School\\SchoolCourse\\SchoolCourseController";
$publicModuleController  =
    "App\\Http\\Controllers\\Public\\{$siteLayout}\\School\\SchoolModule\\SchoolModuleController";
$publicLessonController  =
    "App\\Http\\Controllers\\Public\\{$siteLayout}\\School\\SchoolLesson\\SchoolLessonController";
$publicProductController =
    "App\\Http\\Controllers\\Public\\{$siteLayout}\\Market\\MarketProduct\\MarketProductController";

Route::post('/blog-articles/{id}/like', [$publicArticleController, 'like'])
    ->name('public.blogArticles.like');

Route::post('/blog-videos/{id}/like', [$publicVideoController, 'like'])
    ->name('public.blogVideos.like');

Route::post('/school-tracks/{id}/like', [$publicTrackController, 'like'])
    ->name('public.schoolTracks.like');

Route::post('/school-courses/{id}/like', [$publicCourseController, 'like'])
    ->name('public.schoolCourses.like');

Route::post('/school-modules/{id}/like', [$publicModuleController, 'like'])
    ->name('public.schoolModules.like');

Route::post('/school-lessons/{id}/like', [$publicLessonController, 'like'])
    ->name('public.schoolLessons.like');

Route::post('/catalog/products/{id}/like', [$publicProductController, 'like'])
    ->name('public.marketProducts.like');

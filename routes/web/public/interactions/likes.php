<?php

// Лайки пользователей

use Illuminate\Support\Facades\Route;

$siteLayout = config('site_settings.siteLayout', 'Default');

$publicArticleController = "App\\Http\\Controllers\\Public\\{$siteLayout}\\Blog\\BlogArticle\\BlogArticleController";
$publicVideoController   = "App\\Http\\Controllers\\Public\\{$siteLayout}\\Blog\\BlogVideo\\BlogVideoController";
$publicTrackController   = "App\\Http\\Controllers\\Public\\{$siteLayout}\\School\\TrackController";
$publicCourseController  = "App\\Http\\Controllers\\Public\\{$siteLayout}\\School\\CourseController";
$publicModuleController  = "App\\Http\\Controllers\\Public\\{$siteLayout}\\School\\ModuleController";
$publicLessonController  = "App\\Http\\Controllers\\Public\\{$siteLayout}\\School\\LessonController";

Route::post('/blog-articles/{id}/like', [$publicArticleController, 'like'])
    ->name('public.blogArticles.like');

Route::post('/blog-videos/{id}/like', [$publicVideoController, 'like'])
    ->name('public.blogVideos.like');

Route::post('/tracks/{track}/like', [$publicTrackController, 'like'])
    ->name('tracks.like');

Route::post('/courses/{course}/like', [$publicCourseController, 'like'])
    ->name('courses.like');

Route::post('/modules/{module}/like', [$publicModuleController, 'like'])
    ->name('modules.like');

Route::post('/lessons/{lesson}/like', [$publicLessonController, 'like'])
    ->name('lessons.like');

<?php

// Лайки пользователей

use App\Http\Controllers\Public\PublicTemplateDispatcherController;
use Illuminate\Support\Facades\Route;

Route::post('/blog-articles/{id}/like', [PublicTemplateDispatcherController::class, 'dispatch'])
    ->defaults('_templateController', 'Blog\\BlogArticle\\BlogArticleController')
    ->defaults('_templateAction', 'like')
    ->name('public.blogArticles.like');

Route::post('/blog-videos/{id}/like', [PublicTemplateDispatcherController::class, 'dispatch'])
    ->defaults('_templateController', 'Blog\\BlogVideo\\BlogVideoController')
    ->defaults('_templateAction', 'like')
    ->name('public.blogVideos.like');

Route::post('/school-tracks/{id}/like', [PublicTemplateDispatcherController::class, 'dispatch'])
    ->defaults('_templateController', 'School\\SchoolTrack\\SchoolTrackController')
    ->defaults('_templateAction', 'like')
    ->name('public.schoolTracks.like');

Route::post('/school-courses/{id}/like', [PublicTemplateDispatcherController::class, 'dispatch'])
    ->defaults('_templateController', 'School\\SchoolCourse\\SchoolCourseController')
    ->defaults('_templateAction', 'like')
    ->name('public.schoolCourses.like');

Route::post('/school-modules/{id}/like', [PublicTemplateDispatcherController::class, 'dispatch'])
    ->defaults('_templateController', 'School\\SchoolModule\\SchoolModuleController')
    ->defaults('_templateAction', 'like')
    ->name('public.schoolModules.like');

Route::post('/school-lessons/{id}/like', [PublicTemplateDispatcherController::class, 'dispatch'])
    ->defaults('_templateController', 'School\\SchoolLesson\\SchoolLessonController')
    ->defaults('_templateAction', 'like')
    ->name('public.schoolLessons.like');

Route::post('/catalog/products/{id}/like', [PublicTemplateDispatcherController::class, 'dispatch'])
    ->defaults('_templateController', 'Market\\MarketProduct\\MarketProductController')
    ->defaults('_templateAction', 'like')
    ->name('public.marketProducts.like');

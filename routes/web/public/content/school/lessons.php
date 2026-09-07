<?php
// Уроки школы

use App\Http\Controllers\Public\PublicTemplateDispatcherController;
use Illuminate\Support\Facades\Route;

Route::get('/school/lessons', [PublicTemplateDispatcherController::class, 'dispatch'])
    ->defaults('_templateController', 'School\\SchoolLesson\\SchoolLessonController')
    ->defaults('_templateAction', 'index')
    ->name('public.schoolLessons.index');

Route::get('/school/lessons/{slug}', [PublicTemplateDispatcherController::class, 'dispatch'])
    ->defaults('_templateController', 'School\\SchoolLesson\\SchoolLessonController')
    ->defaults('_templateAction', 'show')
    ->name('public.schoolLessons.show');

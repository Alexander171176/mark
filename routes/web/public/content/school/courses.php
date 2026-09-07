<?php
// Курсы обучения

use App\Http\Controllers\Public\PublicTemplateDispatcherController;
use Illuminate\Support\Facades\Route;

Route::get('/school/courses', [PublicTemplateDispatcherController::class, 'dispatch'])
    ->defaults('_templateController', 'School\\SchoolCourse\\SchoolCourseController')
    ->defaults('_templateAction', 'index')
    ->name('public.schoolCourses.index');

Route::get('/school/courses/{slug}', [PublicTemplateDispatcherController::class, 'dispatch'])
    ->defaults('_templateController', 'School\\SchoolCourse\\SchoolCourseController')
    ->defaults('_templateAction', 'show')
    ->name('public.schoolCourses.show');

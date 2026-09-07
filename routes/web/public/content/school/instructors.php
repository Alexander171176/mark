<?php
// Инструкторы школы

use App\Http\Controllers\Public\PublicTemplateDispatcherController;
use Illuminate\Support\Facades\Route;

Route::get('/school/instructors', [PublicTemplateDispatcherController::class, 'dispatch'])
    ->defaults('_templateController', 'School\\SchoolInstructor\\SchoolInstructorController')
    ->defaults('_templateAction', 'index')
    ->name('public.schoolInstructors.index');

Route::get('/school/instructors/{slug}', [PublicTemplateDispatcherController::class, 'dispatch'])
    ->defaults('_templateController', 'School\\SchoolInstructor\\SchoolInstructorController')
    ->defaults('_templateAction', 'show')
    ->name('public.schoolInstructors.show');

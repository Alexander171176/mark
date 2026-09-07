<?php
// Задания обучения

use App\Http\Controllers\Public\PublicTemplateDispatcherController;
use Illuminate\Support\Facades\Route;

Route::get('/school/assignments', [PublicTemplateDispatcherController::class, 'dispatch'])
    ->defaults('_templateController', 'School\\SchoolAssignment\\SchoolAssignmentController')
    ->defaults('_templateAction', 'index')
    ->name('public.schoolAssignments.index');

Route::get('/school/assignments/{slug}', [PublicTemplateDispatcherController::class, 'dispatch'])
    ->defaults('_templateController', 'School\\SchoolAssignment\\SchoolAssignmentController')
    ->defaults('_templateAction', 'show')
    ->name('public.schoolAssignments.show');

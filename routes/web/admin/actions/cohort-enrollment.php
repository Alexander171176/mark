<?php

use App\Http\Controllers\Admin\School\CohortEnrollment\CohortEnrollmentController;
use Illuminate\Support\Facades\Route;

// Обновление статуса одной записи
Route::put('/cohort-enrollments/{cohortEnrollment}/status',
    [CohortEnrollmentController::class, 'updateStatus'])
    ->name('cohortEnrollments.updateStatus');

// Отдельное обновление заметок администратора
Route::put('/cohort-enrollments/{cohortEnrollment}/notes',
    [CohortEnrollmentController::class, 'updateNotes'])
    ->name('cohortEnrollments.updateNotes');

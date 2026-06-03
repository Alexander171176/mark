<?php

use App\Http\Controllers\Admin\School\SchoolCohortEnrollment\SchoolCohortEnrollmentController;
use Illuminate\Support\Facades\Route;

// Обновление статуса одной записи
Route::put('/cohort-enrollments/{schoolCohortEnrollment}/status',
    [SchoolCohortEnrollmentController::class, 'updateStatus'])
    ->name('schoolCohortEnrollments.updateStatus');

// Отдельное обновление заметок администратора
Route::put('/cohort-enrollments/{schoolCohortEnrollment}/notes',
    [SchoolCohortEnrollmentController::class, 'updateNotes'])
    ->name('schoolCohortEnrollments.updateNotes');

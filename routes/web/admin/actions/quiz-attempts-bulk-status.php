<?php

// массовое обновление/удаление статусов прохождений викторин

use App\Http\Controllers\Admin\School\SchoolQuizAttempt\SchoolQuizAttemptController;
use Illuminate\Support\Facades\Route;

Route::put('/school-quiz-attempts/bulk-status',
    [SchoolQuizAttemptController::class, 'bulkUpdateStatus'])
    ->name('schoolQuizAttempts.bulkUpdateStatus');

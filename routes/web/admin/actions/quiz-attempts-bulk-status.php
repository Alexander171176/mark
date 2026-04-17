<?php

// массовое обновление/удаление статусов прохождений викторин

use App\Http\Controllers\Admin\School\QuizAttempt\QuizAttemptController;
use Illuminate\Support\Facades\Route;

Route::put('/quiz-attempts/bulk-status',
    [QuizAttemptController::class, 'bulkUpdateStatus'])
    ->name('quizAttempts.bulkUpdateStatus');

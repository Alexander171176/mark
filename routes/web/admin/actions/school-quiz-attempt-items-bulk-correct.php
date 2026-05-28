<?php

use App\Http\Controllers\Admin\School\QuizAttemptItem\SchoolQuizAttemptItemController;
use Illuminate\Support\Facades\Route;

Route::put('/school-quiz-attempt-items/bulk-correct',
    [SchoolQuizAttemptItemController::class, 'bulkUpdateCorrect'])
    ->name('schoolQuizAttemptItems.bulkUpdateCorrect');

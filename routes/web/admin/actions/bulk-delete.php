<?php

// Массовое удаление


use App\Http\Controllers\Admin\Blog\Article\ArticleController;
use App\Http\Controllers\Admin\Blog\Banner\BannerController;
use App\Http\Controllers\Admin\Blog\Comment\CommentController;
use App\Http\Controllers\Admin\Blog\Rubric\RubricController;
use App\Http\Controllers\Admin\Blog\Tag\TagController;
use App\Http\Controllers\Admin\Blog\Video\VideoController;
use App\Http\Controllers\Admin\Finance\BundlePrice\BundlePriceController;
use App\Http\Controllers\Admin\Finance\CoursePrice\CoursePriceController;
use App\Http\Controllers\Admin\Finance\Currency\CurrencyController;
use App\Http\Controllers\Admin\School\Assignment\AssignmentController;
use App\Http\Controllers\Admin\School\Hashtag\HashtagController;
use App\Http\Controllers\Admin\School\Quiz\QuizController;
use App\Http\Controllers\Admin\School\QuizAnswer\QuizAnswerController;
use App\Http\Controllers\Admin\School\QuizAttempt\QuizAttemptController;
use App\Http\Controllers\Admin\School\QuizAttemptItem\QuizAttemptItemController;
use App\Http\Controllers\Admin\School\QuizQuestion\QuizQuestionController;
use Illuminate\Support\Facades\Route;

Route::delete('/rubrics/bulk-delete', [RubricController::class, 'bulkDestroy'])
    ->name('rubrics.bulkDestroy');

Route::delete('/articles/bulk-delete', [ArticleController::class, 'bulkDestroy'])
    ->name('articles.bulkDestroy');

Route::delete('/tags/bulk-delete', [TagController::class, 'bulkDestroy'])
    ->name('tags.bulkDestroy');

Route::delete('/banners/bulk-delete', [BannerController::class, 'bulkDestroy'])
    ->name('banners.bulkDestroy');

Route::delete('/videos/bulk-delete', [VideoController::class, 'bulkDestroy'])
    ->name('videos.bulkDestroy');

Route::delete('/comments/bulk-delete', [CommentController::class, 'bulkDestroy'])
    ->name('comments.bulkDestroy');

Route::delete('/hashtags/bulk-delete', [HashtagController::class, 'bulkDestroy'])
    ->name('hashtags.bulkDestroy');

Route::delete('/assignments/bulk-delete', [AssignmentController::class, 'bulkDestroy'])
    ->name('assignments.bulkDestroy');

Route::delete('/quizzes/bulk-delete', [QuizController::class, 'bulkDestroy'])
    ->name('quizzes.bulkDestroy');

Route::delete('/quiz-questions/bulk-delete', [QuizQuestionController::class, 'bulkDestroy'])
    ->name('quizQuestions.bulkDestroy');

Route::delete('/quiz-answers/bulk-delete', [QuizAnswerController::class, 'bulkDestroy'])
    ->name('quizAnswers.bulkDestroy');

Route::delete('/quiz-attempts/bulk-delete', [QuizAttemptController::class, 'bulkDestroy'])
    ->name('quizAttempts.bulkDestroy');

Route::delete('/quiz-attempts/bulk-delete', [QuizAttemptController::class, 'bulkDestroy'])
    ->name('quizAttempts.bulkDestroy');

Route::delete('/quiz-attempt-items/bulk-delete', [QuizAttemptItemController::class, 'bulkDestroy'])
    ->name('quizAttemptItems.bulkDestroy');

Route::delete('/currencies/bulk-delete', [CurrencyController::class, 'bulkDestroy'])
    ->name('currencies.bulkDestroy');

Route::delete('course-prices/bulk-destroy', [CoursePriceController::class, 'bulkDestroy'])
    ->name('coursePrices.bulkDestroy');

Route::delete('bundle-prices/bulk-destroy', [BundlePriceController::class, 'bulkDestroy'])
    ->name('bundlePrices.bulkDestroy');

<?php

// Массовое удаление


use App\Http\Controllers\Admin\Blog\BlogArticle\BlogArticleController;
use App\Http\Controllers\Admin\Blog\BlogBanner\BlogBannerController;
use App\Http\Controllers\Admin\Blog\BlogRubric\BlogRubricController;
use App\Http\Controllers\Admin\Blog\BlogTag\BlogTagController;
use App\Http\Controllers\Admin\Blog\BlogVideo\BlogVideoController;
use App\Http\Controllers\Admin\Blog\Comment\CommentController;
use App\Http\Controllers\Admin\Finance\BundlePrice\BundlePriceController;
use App\Http\Controllers\Admin\Finance\CoursePrice\CoursePriceController;
use App\Http\Controllers\Admin\Finance\Currency\CurrencyController;
use App\Http\Controllers\Admin\Market\MarketCompany\MarketCompanyController;
use App\Http\Controllers\Admin\Market\MarketStorefront\MarketStorefrontController;
use App\Http\Controllers\Admin\School\Assignment\AssignmentController;
use App\Http\Controllers\Admin\School\Hashtag\HashtagController;
use App\Http\Controllers\Admin\School\Quiz\QuizController;
use App\Http\Controllers\Admin\School\QuizAnswer\QuizAnswerController;
use App\Http\Controllers\Admin\School\QuizAttempt\QuizAttemptController;
use App\Http\Controllers\Admin\School\QuizAttemptItem\QuizAttemptItemController;
use App\Http\Controllers\Admin\School\QuizQuestion\QuizQuestionController;
use Illuminate\Support\Facades\Route;

Route::delete('/blog-rubrics/bulk-delete',
    [BlogRubricController::class, 'bulkDestroy'])
    ->name('blogRubrics.bulkDestroy');

Route::delete('/blog-articles/bulk-delete',
    [BlogArticleController::class, 'bulkDestroy'])
    ->name('blogArticles.bulkDestroy');

Route::delete('/blog-tags/bulk-delete',
    [BlogTagController::class, 'bulkDestroy'])
    ->name('blogTags.bulkDestroy');

Route::delete('/blog-banners/bulk-delete',
    [BlogBannerController::class, 'bulkDestroy'])
    ->name('blogBanners.bulkDestroy');

Route::delete('/blog-videos/bulk-delete',
    [BlogVideoController::class, 'bulkDestroy'])
    ->name('blogVideos.bulkDestroy');

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

Route::delete('/quiz-attempt-items/bulk-delete', [QuizAttemptItemController::class, 'bulkDestroy'])
    ->name('quizAttemptItems.bulkDestroy');

Route::delete('/currencies/bulk-delete', [CurrencyController::class, 'bulkDestroy'])
    ->name('currencies.bulkDestroy');

Route::delete('course-prices/bulk-destroy', [CoursePriceController::class, 'bulkDestroy'])
    ->name('coursePrices.bulkDestroy');

Route::delete('bundle-prices/bulk-destroy', [BundlePriceController::class, 'bulkDestroy'])
    ->name('bundlePrices.bulkDestroy');

Route::delete('market-companies/bulk-destroy', [MarketCompanyController::class, 'bulkDestroy'])
    ->name('marketCompanies.bulkDestroy');

Route::delete('market-storefronts/bulk-destroy', [MarketStorefrontController::class, 'bulkDestroy'])
    ->name('marketStorefronts.bulkDestroy');

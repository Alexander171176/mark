<?php

// Массовое удаление


use App\Http\Controllers\Admin\Blog\BlogArticle\BlogArticleController;
use App\Http\Controllers\Admin\Blog\BlogBanner\BlogBannerController;
use App\Http\Controllers\Admin\Blog\BlogRubric\BlogRubricController;
use App\Http\Controllers\Admin\Blog\BlogTag\BlogTagController;
use App\Http\Controllers\Admin\Blog\BlogVideo\BlogVideoController;
use App\Http\Controllers\Admin\Blog\Comment\CommentController;
use App\Http\Controllers\Admin\Finance\Currency\CurrencyController;
use App\Http\Controllers\Admin\Market\MarketCompany\MarketCompanyController;
use App\Http\Controllers\Admin\Market\MarketStorefront\MarketStorefrontController;
use App\Http\Controllers\Admin\School\Assignment\SchoolAssignmentController;
use App\Http\Controllers\Admin\School\BundlePrice\BundlePriceController;
use App\Http\Controllers\Admin\School\CoursePrice\CoursePriceController;
use App\Http\Controllers\Admin\School\Hashtag\SchoolHashtagController;
use App\Http\Controllers\Admin\School\Quiz\SchoolQuizController;
use App\Http\Controllers\Admin\School\QuizAnswer\SchoolQuizAnswerController;
use App\Http\Controllers\Admin\School\QuizAttempt\SchoolQuizAttemptController;
use App\Http\Controllers\Admin\School\QuizAttemptItem\SchoolQuizAttemptItemController;
use App\Http\Controllers\Admin\School\QuizQuestion\SchoolQuizQuestionController;
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

Route::delete('/comments/bulk-delete',
    [CommentController::class, 'bulkDestroy'])
    ->name('comments.bulkDestroy');

Route::delete('/school-hashtags/bulk-delete',
    [SchoolHashtagController::class, 'bulkDestroy'])
    ->name('schoolHashtags.bulkDestroy');

Route::delete('/school-assignments/bulk-delete',
    [SchoolAssignmentController::class, 'bulkDestroy'])
    ->name('schoolAssignments.bulkDestroy');

Route::delete('/school-quizzes/bulk-delete',
    [SchoolQuizController::class, 'bulkDestroy'])
    ->name('schoolQuizzes.bulkDestroy');

Route::delete('/school-quiz-questions/bulk-delete',
    [SchoolQuizQuestionController::class, 'bulkDestroy'])
    ->name('schoolQuizQuestions.bulkDestroy');

Route::delete('/school-quiz-answers/bulk-delete',
    [SchoolQuizAnswerController::class, 'bulkDestroy'])
    ->name('schoolQuizAnswers.bulkDestroy');

Route::delete('/school-quiz-attempts/bulk-delete',
    [SchoolQuizAttemptController::class, 'bulkDestroy'])
    ->name('schoolQuizAttempts.bulkDestroy');

Route::delete('/school-quiz-attempt-items/bulk-delete',
    [SchoolQuizAttemptItemController::class, 'bulkDestroy'])
    ->name('schoolQuizAttemptItems.bulkDestroy');

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

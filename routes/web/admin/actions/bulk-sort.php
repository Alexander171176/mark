<?php

// Обновление сортировки для Drag and Drop

use App\Http\Controllers\Admin\Blog\Article\ArticleController;
use App\Http\Controllers\Admin\Blog\Banner\BannerController;
use App\Http\Controllers\Admin\Blog\Rubric\RubricController;
use App\Http\Controllers\Admin\Blog\Tag\TagController;
use App\Http\Controllers\Admin\Blog\Video\VideoController;
use App\Http\Controllers\Admin\Finance\BundlePrice\BundlePriceController;
use App\Http\Controllers\Admin\Finance\CoursePrice\CoursePriceController;
use App\Http\Controllers\Admin\Finance\Currency\CurrencyController;
use App\Http\Controllers\Admin\Finance\SubscriptionPlan\SubscriptionPlanController;
use App\Http\Controllers\Admin\Market\MarketCompany\MarketCompanyController;
use App\Http\Controllers\Admin\School\Assignment\AssignmentController;
use App\Http\Controllers\Admin\School\Bundle\BundleController;
use App\Http\Controllers\Admin\School\Course\CourseController;
use App\Http\Controllers\Admin\School\CourseSchedule\CourseScheduleController;
use App\Http\Controllers\Admin\School\Hashtag\HashtagController;
use App\Http\Controllers\Admin\School\InstructorProfile\InstructorProfileController;
use App\Http\Controllers\Admin\School\LearningCategory\LearningCategoryController;
use App\Http\Controllers\Admin\School\Lesson\LessonController;
use App\Http\Controllers\Admin\School\Module\ModuleController;
use App\Http\Controllers\Admin\School\Quiz\QuizController;
use App\Http\Controllers\Admin\School\QuizAnswer\QuizAnswerController;
use App\Http\Controllers\Admin\School\QuizQuestion\QuizQuestionController;
use App\Http\Controllers\Admin\System\Parameter\ParameterController;
use Illuminate\Support\Facades\Route;

Route::put('/instructors/update-sort-bulk',
    [InstructorProfileController::class, 'updateSortBulk'])
    ->name('instructors.updateSortBulk');

Route::put('/learning-categories/update-sort-bulk',
    [LearningCategoryController::class, 'updateSortBulk'])
    ->name('learningCategories.updateSortBulk');

Route::put('/hashtags/update-sort-bulk',
    [HashtagController::class, 'updateSortBulk'])
    ->name('hashtags.updateSortBulk');

Route::put('/courses/update-sort-bulk',
    [CourseController::class, 'updateSortBulk'])
    ->name('courses.updateSortBulk');

Route::put('/modules/update-sort-bulk',
    [ModuleController::class, 'updateSortBulk'])
    ->name('modules.updateSortBulk');

Route::put('/lessons/update-sort-bulk',
    [LessonController::class, 'updateSortBulk'])
    ->name('lessons.updateSortBulk');

Route::put('/assignments/update-sort-bulk',
    [AssignmentController::class, 'updateSortBulk'])
    ->name('assignments.updateSortBulk');

Route::put('/course-schedules/update-sort-bulk',
    [CourseScheduleController::class, 'updateSortBulk'])
    ->name('courseSchedules.updateSortBulk');

Route::put('/quizzes/update-sort-bulk',
    [QuizController::class, 'updateSortBulk'])
    ->name('quizzes.updateSortBulk');

Route::put('/quiz-questions/update-sort-bulk',
    [QuizQuestionController::class, 'updateSortBulk'])
    ->name('quizQuestions.updateSortBulk');

Route::put('/quiz-answers/update-sort-bulk',
    [QuizAnswerController::class, 'updateSortBulk'])
    ->name('quizAnswers.updateSortBulk');

Route::put('/bundles/update-sort-bulk',
    [BundleController::class, 'updateSortBulk'])
    ->name('bundles.updateSortBulk');

Route::put('/rubrics/update-sort-bulk',
    [RubricController::class, 'updateSortBulk'])
    ->name('rubrics.updateSortBulk');

Route::put('/articles/update-sort-bulk',
    [ArticleController::class, 'updateSortBulk'])
    ->name('articles.updateSortBulk');

Route::put('/tags/update-sort-bulk',
    [TagController::class, 'updateSortBulk'])
    ->name('tags.updateSortBulk');

Route::put('/banners/update-sort-bulk',
    [BannerController::class, 'updateSortBulk'])
    ->name('banners.updateSortBulk');

Route::put('/videos/update-sort-bulk',
    [VideoController::class, 'updateSortBulk'])
    ->name('videos.updateSortBulk');

Route::put('/settings/update-sort-bulk',
    [ParameterController::class, 'updateSortBulk'])
    ->name('settings.updateSortBulk');

Route::put('/currencies/update-sort-bulk',
    [CurrencyController::class, 'updateSortBulk'])
    ->name('currencies.updateSortBulk');

Route::put('course-prices/update-sort-bulk',
    [CoursePriceController::class, 'updateSortBulk'])
    ->name('coursePrices.updateSortBulk');

Route::put('bundle-prices/update-sort-bulk',
    [BundlePriceController::class, 'updateSortBulk'])
    ->name('bundlePrices.updateSortBulk');

Route::put('subscription-plans/update-sort-bulk',
    [SubscriptionPlanController::class, 'updateSortBulk'])
    ->name('subscriptionPlans.updateSortBulk');

Route::put('market-companies/update-sort-bulk',
    [MarketCompanyController::class, 'updateSortBulk'])
    ->name('marketCompanies.updateSortBulk');

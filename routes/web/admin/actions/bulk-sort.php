<?php

// Обновление сортировки для Drag and Drop

use App\Http\Controllers\Admin\Blog\BlogArticle\BlogArticleController;
use App\Http\Controllers\Admin\Blog\BlogBanner\BlogBannerController;
use App\Http\Controllers\Admin\Blog\BlogRubric\BlogRubricController;
use App\Http\Controllers\Admin\Blog\BlogTag\BlogTagController;
use App\Http\Controllers\Admin\Blog\BlogVideo\BlogVideoController;
use App\Http\Controllers\Admin\Finance\Currency\CurrencyController;
use App\Http\Controllers\Admin\Market\MarketCompany\MarketCompanyController;
use App\Http\Controllers\Admin\Market\MarketStorefront\MarketStorefrontController;
use App\Http\Controllers\Admin\School\Assignment\SchoolAssignmentController;
use App\Http\Controllers\Admin\School\Bundle\SchoolBundleController;
use App\Http\Controllers\Admin\School\Course\SchoolCourseController;
use App\Http\Controllers\Admin\School\CourseSchedule\SchoolCourseScheduleController;
use App\Http\Controllers\Admin\School\Hashtag\SchoolHashtagController;
use App\Http\Controllers\Admin\School\InstructorProfile\SchoolInstructorProfileController;
use App\Http\Controllers\Admin\School\Lesson\SchoolLessonController;
use App\Http\Controllers\Admin\School\Module\SchoolModuleController;
use App\Http\Controllers\Admin\School\Price\SchoolBundlePriceController;
use App\Http\Controllers\Admin\School\Price\SchoolCoursePriceController;
use App\Http\Controllers\Admin\School\Quiz\SchoolQuizController;
use App\Http\Controllers\Admin\School\QuizAnswer\SchoolQuizAnswerController;
use App\Http\Controllers\Admin\School\QuizQuestion\SchoolQuizQuestionController;
use App\Http\Controllers\Admin\School\SubscriptionPlan\SubscriptionPlanController;
use App\Http\Controllers\Admin\School\Track\SchoolTrackController;
use App\Http\Controllers\Admin\System\Parameter\ParameterController;
use Illuminate\Support\Facades\Route;

Route::put('/school-instructor-profiles/update-sort-bulk',
    [SchoolInstructorProfileController::class, 'updateSortBulk'])
    ->name('schoolInstructorProfiles.updateSortBulk');

Route::put('/school-tracks/update-sort-bulk',
    [SchoolTrackController::class, 'updateSortBulk'])
    ->name('schoolTracks.updateSortBulk');

Route::put('/school-hashtags/update-sort-bulk',
    [SchoolHashtagController::class, 'updateSortBulk'])
    ->name('schoolHashtags.updateSortBulk');

Route::put('/school-courses/update-sort-bulk',
    [SchoolCourseController::class, 'updateSortBulk'])
    ->name('schoolCourses.updateSortBulk');

Route::put('/school-modules/update-sort-bulk',
    [SchoolModuleController::class, 'updateSortBulk'])
    ->name('schoolModules.updateSortBulk');

Route::put('/school-lessons/update-sort-bulk',
    [SchoolLessonController::class, 'updateSortBulk'])
    ->name('schoolLessons.updateSortBulk');

Route::put('/school-assignments/update-sort-bulk',
    [SchoolAssignmentController::class, 'updateSortBulk'])
    ->name('schoolAssignments.updateSortBulk');

Route::put('/school-course-schedules/update-sort-bulk',
    [SchoolCourseScheduleController::class, 'updateSortBulk'])
    ->name('schoolCourseSchedules.updateSortBulk');

Route::put('/school-quizzes/update-sort-bulk',
    [SchoolQuizController::class, 'updateSortBulk'])
    ->name('schoolQuizzes.updateSortBulk');

Route::put('/school-quiz-questions/update-sort-bulk',
    [SchoolQuizQuestionController::class, 'updateSortBulk'])
    ->name('schoolQuizQuestions.updateSortBulk');

Route::put('/school-quiz-answers/update-sort-bulk',
    [SchoolQuizAnswerController::class, 'updateSortBulk'])
    ->name('schoolQuizAnswers.updateSortBulk');

Route::put('/school-bundles/update-sort-bulk',
    [SchoolBundleController::class, 'updateSortBulk'])
    ->name('schoolBundles.updateSortBulk');

Route::put('/blog-rubrics/update-sort-bulk',
    [BlogRubricController::class, 'updateSortBulk'])
    ->name('blogRubrics.updateSortBulk');

Route::put('/blog-articles/update-sort-bulk',
    [BlogArticleController::class, 'updateSortBulk'])
    ->name('blogArticles.updateSortBulk');

Route::put('/blog-tags/update-sort-bulk',
    [BlogTagController::class, 'updateSortBulk'])
    ->name('blogTags.updateSortBulk');

Route::put('/blog-banners/update-sort-bulk',
    [BlogBannerController::class, 'updateSortBulk'])
    ->name('blogBanners.updateSortBulk');

Route::put('/blog-videos/update-sort-bulk',
    [BlogVideoController::class, 'updateSortBulk'])
    ->name('blogVideos.updateSortBulk');

Route::put('/settings/update-sort-bulk',
    [ParameterController::class, 'updateSortBulk'])
    ->name('settings.updateSortBulk');

Route::put('/currencies/update-sort-bulk',
    [CurrencyController::class, 'updateSortBulk'])
    ->name('currencies.updateSortBulk');

Route::put('/school-course-prices/update-sort-bulk',
    [SchoolCoursePriceController::class, 'updateSortBulk'])
    ->name('schoolCoursePrices.updateSortBulk');

Route::put('/school-bundle-prices/update-sort-bulk',
    [SchoolBundlePriceController::class, 'updateSortBulk'])
    ->name('schoolBundlePrices.updateSortBulk');

Route::put('subscription-plans/update-sort-bulk',
    [SubscriptionPlanController::class, 'updateSortBulk'])
    ->name('subscriptionPlans.updateSortBulk');

Route::put('market-companies/update-sort-bulk',
    [MarketCompanyController::class, 'updateSortBulk'])
    ->name('marketCompanies.updateSortBulk');

Route::put('market-storefronts/update-sort-bulk',
    [MarketStorefrontController::class, 'updateSortBulk'])
    ->name('marketStorefronts.updateSortBulk');

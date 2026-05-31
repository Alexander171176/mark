<?php

// Переключение активности массово

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

Route::put('/school-instructor-profiles/bulk-activity',
    [SchoolInstructorProfileController::class, 'bulkUpdateActivity'])
    ->name('schoolInstructorProfiles.bulkUpdateActivity');

Route::put('/school-tracks/bulk-activity',
    [SchoolTrackController::class, 'bulkUpdateActivity'])
    ->name('schoolTracks.bulkUpdateActivity');

Route::put('/school-hashtags/bulk-activity',
    [SchoolHashtagController::class, 'bulkUpdateActivity'])
    ->name('schoolHashtags.bulkUpdateActivity');

Route::put('/school-courses/bulk-activity',
    [SchoolCourseController::class, 'bulkUpdateActivity'])
    ->name('schoolCourses.bulkUpdateActivity');

Route::put('/school-modules/bulk-activity',
    [SchoolModuleController::class, 'bulkUpdateActivity'])
    ->name('schoolModules.bulkUpdateActivity');

Route::put('/school-lessons/bulk-activity',
    [SchoolLessonController::class, 'bulkUpdateActivity'])
    ->name('schoolLessons.bulkUpdateActivity');

Route::put('/school-assignments/bulk-activity',
    [SchoolAssignmentController::class, 'bulkUpdateActivity'])
    ->name('schoolAssignments.bulkUpdateActivity');

Route::put('/school-course-schedules/bulk-activity',
    [SchoolCourseScheduleController::class, 'bulkUpdateActivity'])
    ->name('schoolCourseSchedules.bulkUpdateActivity');

Route::put('/school-quizzes/bulk-activity',
    [SchoolQuizController::class, 'bulkUpdateActivity'])
    ->name('schoolQuizzes.bulkUpdateActivity');

Route::put('/school-quiz-questions/bulk-activity',
    [SchoolQuizQuestionController::class, 'bulkUpdateActivity'])
    ->name('schoolQuizQuestions.bulkUpdateActivity');

Route::put('/school-quiz-answers/bulk-activity',
    [SchoolQuizAnswerController::class, 'bulkUpdateActivity'])
    ->name('schoolQuizAnswers.bulkUpdateActivity');

Route::put('/school-bundles/bulk-activity',
    [SchoolBundleController::class, 'bulkUpdateActivity'])
    ->name('schoolBundles.bulkUpdateActivity');

Route::put('/blog-rubrics/bulk-activity',
    [BlogRubricController::class, 'bulkUpdateActivity'])
    ->name('blogRubrics.bulkUpdateActivity');

Route::put('/blog-articles/bulk-activity',
    [BlogArticleController::class, 'bulkUpdateActivity'])
    ->name('blogArticles.bulkUpdateActivity');

Route::put('/blog-tags/bulk-activity',
    [BlogTagController::class, 'bulkUpdateActivity'])
    ->name('blogTags.bulkUpdateActivity');

Route::put('/blog-banners/bulk-activity',
    [BlogBannerController::class, 'bulkUpdateActivity'])
    ->name('blogBanners.bulkUpdateActivity');

Route::put('/blog-videos/bulk-activity',
    [BlogVideoController::class, 'bulkUpdateActivity'])
    ->name('blogVideos.bulkUpdateActivity');

Route::put('/settings/bulk-activity',
    [ParameterController::class, 'bulkUpdateActivity'])
    ->name('settings.bulkUpdateActivity');

Route::put('/comments/bulk-activity',
    [CommentController::class, 'bulkUpdateActivity'])
    ->name('comments.bulkUpdateActivity');

Route::put('/currencies/bulk-activity',
    [CurrencyController::class, 'bulkUpdateActivity'])
    ->name('currencies.bulkUpdateActivity');

Route::put('/school-course-prices/bulk-activity',
    [SchoolCoursePriceController::class, 'bulkUpdateActivity'])
    ->name('schoolCoursePrices.bulkUpdateActivity');

Route::put('/school-bundle-prices/bulk-activity',
    [SchoolBundlePriceController::class, 'bulkUpdateActivity'])
    ->name('schoolBundlePrices.bulkUpdateActivity');

Route::put('subscription-plans/bulk-update-activity',
    [SubscriptionPlanController::class, 'bulkUpdateActivity'])
    ->name('subscriptionPlans.bulkUpdateActivity');

Route::put('market-companies/bulk-update-activity',
    [MarketCompanyController::class, 'bulkUpdateActivity'])
    ->name('marketCompanies.bulkUpdateActivity');

Route::put('market-storefronts/bulk-update-activity',
    [MarketStorefrontController::class, 'bulkUpdateActivity'])
    ->name('marketStorefronts.bulkUpdateActivity');

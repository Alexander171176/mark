<?php

// Переключение активности (Используем имена моделей для параметров RMB)

use App\Http\Controllers\Admin\Blog\BlogArticle\BlogArticleController;
use App\Http\Controllers\Admin\Blog\BlogBanner\BlogBannerController;
use App\Http\Controllers\Admin\Blog\BlogRubric\BlogRubricController;
use App\Http\Controllers\Admin\Blog\BlogTag\BlogTagController;
use App\Http\Controllers\Admin\Blog\BlogVideo\BlogVideoController;
use App\Http\Controllers\Admin\Blog\Comment\CommentController;
use App\Http\Controllers\Admin\Finance\Currency\CurrencyController;
use App\Http\Controllers\Admin\Market\MarketBrand\MarketBrandController;
use App\Http\Controllers\Admin\Market\MarketCategory\MarketCategoryController;
use App\Http\Controllers\Admin\Market\MarketCompany\MarketCompanyController;
use App\Http\Controllers\Admin\Market\MarketShop\MarketShopController;
use App\Http\Controllers\Admin\School\SchoolAssignment\SchoolAssignmentController;
use App\Http\Controllers\Admin\School\SchoolBundle\SchoolBundleController;
use App\Http\Controllers\Admin\School\SchoolBundlePrice\SchoolBundlePriceController;
use App\Http\Controllers\Admin\School\SchoolCourse\SchoolCourseController;
use App\Http\Controllers\Admin\School\SchoolCoursePrice\SchoolCoursePriceController;
use App\Http\Controllers\Admin\School\SchoolCourseSchedule\SchoolCourseScheduleController;
use App\Http\Controllers\Admin\School\SchoolHashtag\SchoolHashtagController;
use App\Http\Controllers\Admin\School\SchoolInstructorProfile\SchoolInstructorProfileController;
use App\Http\Controllers\Admin\School\SchoolLesson\SchoolLessonController;
use App\Http\Controllers\Admin\School\SchoolModule\SchoolModuleController;
use App\Http\Controllers\Admin\School\SchoolQuiz\SchoolQuizController;
use App\Http\Controllers\Admin\School\SchoolQuizAnswer\SchoolQuizAnswerController;
use App\Http\Controllers\Admin\School\SchoolQuizQuestion\SchoolQuizQuestionController;
use App\Http\Controllers\Admin\School\SchoolSubscriptionPlan\SchoolSubscriptionPlanController;
use App\Http\Controllers\Admin\School\SchoolTrack\SchoolTrackController;
use App\Http\Controllers\Admin\System\Parameter\ParameterController;
use Illuminate\Support\Facades\Route;

Route::put('/settings/{setting}/activity',
    [ParameterController::class, 'updateActivity'])
    ->name('settings.updateActivity');

Route::put('/currencies/{currency}/activity',
    [CurrencyController::class, 'updateActivity'])
    ->name('currencies.updateActivity');

Route::put('/comments/{comment}/activity',
    [CommentController::class, 'updateActivity'])
    ->name('comments.updateActivity');

// блог
Route::put('/blog-rubrics/{blogRubric}/activity',
    [BlogRubricController::class, 'updateActivity'])
    ->name('blogRubrics.updateActivity');

Route::put('/blog-articles/{blogArticle}/activity',
    [BlogArticleController::class, 'updateActivity'])
    ->name('blogArticles.updateActivity');

Route::put('/blog-tags/{blogTag}/activity',
    [BlogTagController::class, 'updateActivity'])
    ->name('blogTags.updateActivity');

Route::put('/blog-banners/{blogBanner}/activity',
    [BlogBannerController::class, 'updateActivity'])
    ->name('blogBanners.updateActivity');

Route::put('/blog-videos/{blogVideo}/activity',
    [BlogVideoController::class, 'updateActivity'])
    ->name('blogVideos.updateActivity');

// школа
Route::put('/school-instructor-profiles/{schoolInstructorProfile}/activity',
    [SchoolInstructorProfileController::class, 'updateActivity'])
    ->whereNumber('schoolInstructorProfile')
    ->name('schoolInstructorProfiles.updateActivity');

Route::put('/school-tracks/{schoolTrack}/activity',
    [SchoolTrackController::class, 'updateActivity'])
    ->whereNumber('schoolTrack')
    ->name('schoolTracks.updateActivity');

Route::put('/school-hashtags/{schoolHashtag}/activity',
    [SchoolHashtagController::class, 'updateActivity'])
    ->name('schoolHashtags.updateActivity');

Route::put('/school-courses/{schoolCourse}/activity',
    [SchoolCourseController::class, 'updateActivity'])
    ->whereNumber('schoolCourse')
    ->name('schoolCourses.updateActivity');

Route::put('/school-modules/{schoolModule}/activity',
    [SchoolModuleController::class, 'updateActivity'])
    ->whereNumber('schoolModule')
    ->name('schoolModules.updateActivity');

Route::put('/school-lessons/{schoolLesson}/activity',
    [SchoolLessonController::class, 'updateActivity'])
    ->whereNumber('schoolLesson')
    ->name('schoolLessons.updateActivity');

Route::put('/school-assignments/{schoolAssignment}/activity',
    [SchoolAssignmentController::class, 'updateActivity'])
    ->whereNumber('schoolAssignment')
    ->name('schoolAssignments.updateActivity');

Route::put('/school-course-schedules/{schoolCourseSchedule}/activity',
    [SchoolCourseScheduleController::class, 'updateActivity'])
    ->whereNumber('schoolCourseSchedule')
    ->name('schoolCourseSchedules.updateActivity');

Route::put('/school-quizzes/{schoolQuiz}/activity',
    [SchoolQuizController::class, 'updateActivity'])
    ->whereNumber('schoolQuiz')
    ->name('schoolQuizzes.updateActivity');

Route::put('/school-quiz-questions/{schoolQuizQuestion}/activity',
    [SchoolQuizQuestionController::class, 'updateActivity'])
    ->whereNumber('schoolQuizQuestion')
    ->name('schoolQuizQuestions.updateActivity');

Route::put('/school-quiz-answers/{schoolQuizAnswer}/activity',
    [SchoolQuizAnswerController::class, 'updateActivity'])
    ->whereNumber('schoolQuizAnswer')
    ->name('schoolQuizAnswers.updateActivity');

Route::put('/school-bundles/{schoolBundle}/activity',
    [SchoolBundleController::class, 'updateActivity'])
    ->whereNumber('schoolBundle')
    ->name('schoolBundles.updateActivity');

Route::put('/school-course-prices/{schoolCoursePrice}/activity',
    [SchoolCoursePriceController::class, 'updateActivity'])
    ->whereNumber('schoolCoursePrice')
    ->name('schoolCoursePrices.updateActivity');

Route::put('/school-bundle-prices/{schoolBundlePrice}/activity',
    [SchoolBundlePriceController::class, 'updateActivity'])
    ->whereNumber('schoolBundlePrice')
    ->name('schoolBundlePrices.updateActivity');

Route::put('/school-subscription-plans/{schoolSubscriptionPlan}/activity',
    [SchoolSubscriptionPlanController::class, 'updateActivity'])
    ->whereNumber('schoolSubscriptionPlan')
    ->name('schoolSubscriptionPlans.updateActivity');

// маркет
Route::put('/market-companies/{marketCompany}/activity',
    [MarketCompanyController::class, 'updateActivity'])
    ->whereNumber('marketCompany')
    ->name('marketCompanies.updateActivity');

Route::put('/market-shops/{marketShop}/activity',
    [MarketShopController::class, 'updateActivity'])
    ->whereNumber('marketShop')
    ->name('marketShops.updateActivity');

Route::put('/market-categories/{marketCategory}/activity',
    [MarketCategoryController::class, 'updateActivity'])
    ->whereNumber('marketCategory')
    ->name('marketCategories.updateActivity');

Route::put('/market-brands/{marketBrand}/activity',
    [MarketBrandController::class, 'updateActivity'])
    ->whereNumber('marketBrand')
    ->name('marketBrands.updateActivity');

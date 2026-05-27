<?php

// Обновление сортировки для одной строки

use App\Http\Controllers\Admin\Blog\BlogArticle\BlogArticleController;
use App\Http\Controllers\Admin\Blog\BlogBanner\BlogBannerController;
use App\Http\Controllers\Admin\Blog\BlogRubric\BlogRubricController;
use App\Http\Controllers\Admin\Blog\BlogTag\BlogTagController;
use App\Http\Controllers\Admin\Blog\BlogVideo\BlogVideoController;
use App\Http\Controllers\Admin\Finance\Currency\CurrencyController;
use App\Http\Controllers\Admin\Market\MarketCompany\MarketCompanyController;
use App\Http\Controllers\Admin\Market\MarketStorefront\MarketStorefrontController;
use App\Http\Controllers\Admin\School\Assignment\SchoolAssignmentController;
use App\Http\Controllers\Admin\School\Bundle\BundleController;
use App\Http\Controllers\Admin\School\BundlePrice\BundlePriceController;
use App\Http\Controllers\Admin\School\Course\SchoolCourseController;
use App\Http\Controllers\Admin\School\CoursePrice\CoursePriceController;
use App\Http\Controllers\Admin\School\CourseSchedule\SchoolCourseScheduleController;
use App\Http\Controllers\Admin\School\Hashtag\SchoolHashtagController;
use App\Http\Controllers\Admin\School\InstructorProfile\SchoolInstructorProfileController;
use App\Http\Controllers\Admin\School\Lesson\SchoolLessonController;
use App\Http\Controllers\Admin\School\Module\SchoolModuleController;
use App\Http\Controllers\Admin\School\Quiz\SchoolQuizController;
use App\Http\Controllers\Admin\School\QuizAnswer\SchoolQuizAnswerController;
use App\Http\Controllers\Admin\School\QuizQuestion\SchoolQuizQuestionController;
use App\Http\Controllers\Admin\School\SubscriptionPlan\SubscriptionPlanController;
use App\Http\Controllers\Admin\School\Track\SchoolTrackController;
use App\Http\Controllers\Admin\System\Parameter\ParameterController;
use Illuminate\Support\Facades\Route;

Route::put('/blog-rubrics/{blogRubric}/sort',
    [BlogRubricController::class, 'updateSort'])
    ->name('blogRubrics.updateSort');

Route::put('/blog-articles/{blogArticle}/sort',
    [BlogArticleController::class, 'updateSort'])
    ->name('blogArticles.updateSort');

Route::put('/blog-tags/{blogTag}/sort',
    [BlogTagController::class, 'updateSort'])
    ->name('blogTags.updateSort');

Route::put('/blog-banners/{blogBanner}/sort',
    [BlogBannerController::class, 'updateSort'])
    ->name('blogBanners.updateSort');

Route::put('/blog-videos/{blogVideo}/sort',
    [BlogVideoController::class, 'updateSort'])
    ->name('blogVideos.updateSort');

Route::put('/parameters/{parameter}/sort',
    [ParameterController::class, 'updateSort'])
    ->name('parameters.updateSort');

Route::put('/currencies/{currency}/sort',
    [CurrencyController::class, 'updateSort'])
    ->name('currencies.updateSort');

Route::put('/school-instructor-profiles/{schoolInstructorProfile}/sort',
    [SchoolInstructorProfileController::class, 'updateSort'])
    ->whereNumber('schoolInstructorProfile')
    ->name('schoolInstructorProfiles.updateSort');

Route::put('/school-tracks/{schoolTrack}/sort',
    [SchoolTrackController::class, 'updateSort'])
    ->whereNumber('schoolTrack')
    ->name('schoolTracks.updateSort');

Route::put('/school-hashtags/{schoolHashtag}/sort',
    [SchoolHashtagController::class, 'updateSort'])
    ->whereNumber('schoolHashtag')
    ->name('schoolHashtags.updateSort');

Route::put('/school-courses/{schoolCourse}/sort',
    [SchoolCourseController::class, 'updateSort'])
    ->whereNumber('schoolCourse')
    ->name('schoolCourses.updateSort');

Route::put('/school-modules/{schoolModule}/sort',
    [SchoolModuleController::class, 'updateSort'])
    ->whereNumber('schoolModule')
    ->name('schoolModules.updateSort');

Route::put('/school-lessons/{schoolLesson}/sort',
    [SchoolLessonController::class, 'updateSort'])
    ->whereNumber('schoolLesson')
    ->name('schoolLessons.updateSort');

Route::put('/school-assignments/{schoolAssignment}/sort',
    [SchoolAssignmentController::class, 'updateSort'])
    ->whereNumber('schoolAssignment')
    ->name('schoolAssignments.updateSort');

Route::put('/school-course-schedules/{schoolCourseSchedule}/sort',
    [SchoolCourseScheduleController::class, 'updateSort'])
    ->whereNumber('schoolCourseSchedule')
    ->name('schoolCourseSchedules.updateSort');

Route::put('/school-quizzes/{schoolQuiz}/sort',
    [SchoolQuizController::class, 'updateSort'])
    ->whereNumber('schoolQuiz')
    ->name('schoolQuizzes.updateSort');

Route::put('/school-quiz-questions/{schoolQuizQuestion}/sort',
    [SchoolQuizQuestionController::class, 'updateSort'])
    ->whereNumber('schoolQuizQuestion')
    ->name('schoolQuizQuestions.updateSort');

Route::put('/school-quiz-answers/{schoolQuizAnswer}/sort',
    [SchoolQuizAnswerController::class, 'updateSort'])
    ->whereNumber('schoolQuizAnswer')
    ->name('schoolQuizAnswers.updateSort');

Route::put('/bundles/{bundle}/sort',
    [BundleController::class, 'updateSort'])
    ->name('bundles.updateSort');

Route::put('course-prices/{coursePrice}/update-sort',
    [CoursePriceController::class, 'updateSort'])
    ->name('coursePrices.updateSort');

Route::put('bundle-prices/{bundlePrice}/update-sort',
    [BundlePriceController::class, 'updateSort'])
    ->name('bundlePrices.updateSort');

Route::put('subscription-plans/{subscriptionPlan}/update-sort',
    [SubscriptionPlanController::class, 'updateSort'])
    ->name('subscriptionPlans.updateSort');

Route::put('market-companies/{marketCompany}/update-sort',
    [MarketCompanyController::class, 'updateSort'])
    ->name('marketCompanies.updateSort');

Route::put('market-storefronts/{marketStorefront}/update-sort',
    [MarketStorefrontController::class, 'updateSort'])
    ->name('marketStorefronts.updateSort');

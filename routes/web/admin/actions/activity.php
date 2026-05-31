<?php

// Переключение активности (Используем имена моделей для параметров RMB)

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

Route::put('/settings/{setting}/activity',
    [ParameterController::class, 'updateActivity'])
    ->name('settings.updateActivity');

Route::put('/comments/{comment}/activity',
    [CommentController::class, 'updateActivity'])
    ->name('comments.updateActivity');

Route::put('/currencies/{currency}/activity',
    [CurrencyController::class, 'updateActivity'])
    ->name('currencies.updateActivity');

Route::put('/school-course-prices/{schoolCoursePrice}/activity',
    [SchoolCoursePriceController::class, 'updateActivity'])
    ->whereNumber('schoolCoursePrice')
    ->name('schoolCoursePrices.updateActivity');

Route::put('/school-bundle-prices/{schoolBundlePrice}/activity',
    [SchoolBundlePriceController::class, 'updateActivity'])
    ->whereNumber('schoolBundlePrice')
    ->name('schoolBundlePrices.updateActivity');

Route::put('subscription-plans/{subscriptionPlan}/update-activity',
    [SubscriptionPlanController::class, 'updateActivity'])
    ->name('subscriptionPlans.updateActivity');

Route::put('market-companies/{marketCompany}/update-activity',
    [MarketCompanyController::class, 'updateActivity'])
    ->name('marketCompanies.updateActivity');

Route::put('market-storefronts/{marketStorefront}/update-activity',
    [MarketStorefrontController::class, 'updateActivity'])
    ->name('marketStorefronts.updateActivity');

<?php

// Переключение активности (Используем имена моделей для параметров RMB)

use App\Http\Controllers\Admin\Blog\BlogArticle\BlogArticleController;
use App\Http\Controllers\Admin\Blog\BlogBanner\BlogBannerController;
use App\Http\Controllers\Admin\Blog\BlogRubric\BlogRubricController;
use App\Http\Controllers\Admin\Blog\BlogTag\BlogTagController;
use App\Http\Controllers\Admin\Blog\BlogVideo\BlogVideoController;
use App\Http\Controllers\Admin\Blog\Comment\CommentController;
use App\Http\Controllers\Admin\Finance\BundlePrice\BundlePriceController;
use App\Http\Controllers\Admin\Finance\CoursePrice\CoursePriceController;
use App\Http\Controllers\Admin\Finance\Currency\CurrencyController;
use App\Http\Controllers\Admin\Finance\SubscriptionPlan\SubscriptionPlanController;
use App\Http\Controllers\Admin\Market\MarketCompany\MarketCompanyController;
use App\Http\Controllers\Admin\Market\MarketStorefront\MarketStorefrontController;
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

Route::put('/instructors/{instructorProfile}/activity',
    [InstructorProfileController::class, 'updateActivity'])
    ->name('instructors.updateActivity');

Route::put('/learning-categories/{learningCategory}/activity',
    [LearningCategoryController::class, 'updateActivity'])
    ->name('learningCategories.updateActivity');

Route::put('/hashtags/{hashtag}/activity',
    [HashtagController::class, 'updateActivity'])
    ->name('hashtags.updateActivity');

Route::put('/courses/{course}/activity',
    [CourseController::class, 'updateActivity'])
    ->name('courses.updateActivity');

Route::put('/modules/{module}/activity',
    [ModuleController::class, 'updateActivity'])
    ->name('modules.updateActivity');

Route::put('/lessons/{lesson}/activity',
    [LessonController::class, 'updateActivity'])
    ->name('lessons.updateActivity');

Route::put('/assignments/{assignment}/activity',
    [AssignmentController::class, 'updateActivity'])
    ->name('assignments.updateActivity');

Route::put('/course-schedules/{courseSchedule}/activity',
    [CourseScheduleController::class, 'updateActivity'])
    ->name('courseSchedules.updateActivity');

Route::put('/quizzes/{quiz}/activity',
    [QuizController::class, 'updateActivity'])
    ->name('quizzes.updateActivity');

Route::put('/quiz-questions/{quizQuestion}/activity',
    [QuizQuestionController::class, 'updateActivity'])
    ->name('quizQuestions.updateActivity');

Route::put('/quiz-answers/{quizAnswer}/activity',
    [QuizAnswerController::class, 'updateActivity'])
    ->name('quizAnswers.updateActivity');

Route::put('/bundles/{bundle}/activity',
    [BundleController::class, 'updateActivity'])
    ->name('bundles.updateActivity');

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

Route::put('course-prices/{coursePrice}/update-activity',
    [CoursePriceController::class, 'updateActivity'])
    ->name('coursePrices.updateActivity');

Route::put('bundle-prices/{bundlePrice}/update-activity',
    [BundlePriceController::class, 'updateActivity'])
    ->name('bundlePrices.updateActivity');

Route::put('subscription-plans/{subscriptionPlan}/update-activity',
    [SubscriptionPlanController::class, 'updateActivity'])
    ->name('subscriptionPlans.updateActivity');

Route::put('market-companies/{marketCompany}/update-activity',
    [MarketCompanyController::class, 'updateActivity'])
    ->name('marketCompanies.updateActivity');

Route::put('market-storefronts/{marketStorefront}/update-activity',
    [MarketStorefrontController::class, 'updateActivity'])
    ->name('marketStorefronts.updateActivity');

<?php

// Переключение активности (Используем имена моделей для параметров RMB)

use App\Http\Controllers\Admin\Blog\Article\ArticleController;
use App\Http\Controllers\Admin\Blog\Banner\BannerController;
use App\Http\Controllers\Admin\Blog\Comment\CommentController;
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

Route::put('/rubrics/{rubric}/activity',
    [RubricController::class, 'updateActivity'])
    ->name('rubrics.updateActivity');

Route::put('/articles/{article}/activity',
    [ArticleController::class, 'updateActivity'])
    ->name('articles.updateActivity');

Route::put('/tags/{tag}/activity',
    [TagController::class, 'updateActivity'])
    ->name('tags.updateActivity');

Route::put('/banners/{banner}/activity',
    [BannerController::class, 'updateActivity'])
    ->name('banners.updateActivity');

Route::put('/videos/{video}/activity',
    [VideoController::class, 'updateActivity'])
    ->name('videos.updateActivity');

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

<?php

// Обновление сортировки для одной строки

use App\Http\Controllers\Admin\Blog\Banner\BannerController;
use App\Http\Controllers\Admin\Blog\Rubric\RubricController;
use App\Http\Controllers\Admin\Blog\Tag\TagController;
use App\Http\Controllers\Admin\Blog\Video\VideoController;
use App\Http\Controllers\Admin\Finance\BundlePrice\BundlePriceController;
use App\Http\Controllers\Admin\Finance\CoursePrice\CoursePriceController;
use App\Http\Controllers\Admin\Finance\Currency\CurrencyController;
use App\Http\Controllers\Admin\Finance\SubscriptionPlan\SubscriptionPlanController;
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

Route::put('/instructors/{instructor}/sort',
    [InstructorProfileController::class, 'updateSort'])
    ->name('instructors.updateSort');

Route::put('/learning-categories/{learningCategory}/sort',
    [LearningCategoryController::class, 'updateSort'])
    ->whereNumber('learningCategory')
    ->name('learningCategories.updateSort');

Route::put('/hashtags/{hashtag}/sort',
    [HashtagController::class, 'updateSort'])
    ->whereNumber('hashtag')
    ->name('hashtags.updateSort');

Route::put('/courses/{course}/sort',
    [CourseController::class, 'updateSort'])
    ->name('courses.updateSort');

Route::put('/modules/{module}/sort',
    [ModuleController::class, 'updateSort'])
    ->name('modules.updateSort');

Route::put('/lessons/{lesson}/sort',
    [LessonController::class, 'updateSort'])
    ->name('lessons.updateSort');

Route::put('/assignments/{assignment}/sort',
    [AssignmentController::class, 'updateSort'])
    ->name('assignments.updateSort');

Route::put('/course-schedules/{courseSchedule}/sort',
    [CourseScheduleController::class, 'updateSort'])
    ->whereNumber('courseSchedule')
    ->name('courseSchedules.updateSort');

Route::put('/quizzes/{quiz}/sort',
    [QuizController::class, 'updateSort'])
    ->name('quizzes.updateSort');

Route::put('/quiz-questions/{quizQuestion}/sort',
    [QuizQuestionController::class, 'updateSort'])
    ->whereNumber('quizQuestion')
    ->name('quizQuestions.updateSort');

Route::put('/quiz-answers/{quizAnswer}/sort',
    [QuizAnswerController::class, 'updateSort'])
    ->whereNumber('quizAnswer')
    ->name('quizAnswers.updateSort');

Route::put('/bundles/{bundle}/sort',
    [BundleController::class, 'updateSort'])
    ->name('bundles.updateSort');

Route::put('/rubrics/{rubric}/sort',
    [RubricController::class, 'updateSort'])
    ->name('rubrics.updateSort');

Route::put('/tags/{tag}/sort',
    [TagController::class, 'updateSort'])
    ->name('tags.updateSort');

Route::put('/banners/{banner}/sort',
    [BannerController::class, 'updateSort'])
    ->name('banners.updateSort');

Route::put('/videos/{video}/sort',
    [VideoController::class, 'updateSort'])
    ->name('videos.updateSort');

Route::put('/parameters/{parameter}/sort',
    [ParameterController::class, 'updateSort'])
    ->name('parameters.updateSort');

Route::put('/currencies/{currency}/sort',
    [CurrencyController::class, 'updateSort'])
    ->name('currencies.updateSort');

Route::put('course-prices/{coursePrice}/update-sort',
    [CoursePriceController::class, 'updateSort'])
    ->name('coursePrices.updateSort');

Route::put('bundle-prices/{bundlePrice}/update-sort',
    [BundlePriceController::class, 'updateSort'])
    ->name('bundlePrices.updateSort');

Route::put('subscription-plans/{subscriptionPlan}/update-sort',
    [SubscriptionPlanController::class, 'updateSort'])
    ->name('subscriptionPlans.updateSort');

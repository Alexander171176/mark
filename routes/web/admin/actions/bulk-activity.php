<?php

// Переключение активности массово

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

Route::put('/instructors/bulk-activity',
    [InstructorProfileController::class, 'bulkUpdateActivity'])
    ->name('instructors.bulkUpdateActivity');

Route::put('/learning-categories/bulk-activity',
    [LearningCategoryController::class, 'bulkUpdateActivity'])
    ->name('learningCategories.bulkUpdateActivity');

Route::put('/hashtags/bulk-activity',
    [HashtagController::class, 'bulkUpdateActivity'])
    ->name('hashtags.bulkUpdateActivity');

Route::put('/courses/bulk-activity',
    [CourseController::class, 'bulkUpdateActivity'])
    ->name('courses.bulkUpdateActivity');

Route::put('/modules/bulk-activity',
    [ModuleController::class, 'bulkUpdateActivity'])
    ->name('modules.bulkUpdateActivity');

Route::put('/lessons/bulk-activity',
    [LessonController::class, 'bulkUpdateActivity'])
    ->name('lessons.bulkUpdateActivity');

Route::put('/assignments/bulk-activity',
    [AssignmentController::class, 'bulkUpdateActivity'])
    ->name('assignments.bulkUpdateActivity');

Route::put('/course-schedules/bulk-activity',
    [CourseScheduleController::class, 'bulkUpdateActivity'])
    ->name('courseSchedules.bulkUpdateActivity');

Route::put('/quizzes/bulk-activity',
    [QuizController::class, 'bulkUpdateActivity'])
    ->name('quizzes.bulkUpdateActivity');

Route::put('/quiz-questions/bulk-activity',
    [QuizQuestionController::class, 'bulkUpdateActivity'])
    ->name('quizQuestions.bulkUpdateActivity');

Route::put('/quiz-answers/bulk-activity',
    [QuizAnswerController::class, 'bulkUpdateActivity'])
    ->name('quizAnswers.bulkUpdateActivity');

Route::put('/bundles/bulk-activity',
    [BundleController::class, 'bulkUpdateActivity'])
    ->name('bundles.bulkUpdateActivity');

Route::put('/rubrics/bulk-activity',
    [RubricController::class, 'bulkUpdateActivity'])
    ->name('rubrics.bulkUpdateActivity');

Route::put('/articles/bulk-activity',
    [ArticleController::class, 'bulkUpdateActivity'])
    ->name('articles.bulkUpdateActivity');

Route::put('/tags/bulk-activity',
    [TagController::class, 'bulkUpdateActivity'])
    ->name('tags.bulkUpdateActivity');

Route::put('/banners/bulk-activity',
    [BannerController::class, 'bulkUpdateActivity'])
    ->name('banners.bulkUpdateActivity');

Route::put('/videos/bulk-activity',
    [VideoController::class, 'bulkUpdateActivity'])
    ->name('videos.bulkUpdateActivity');

Route::put('/settings/bulk-activity',
    [ParameterController::class, 'bulkUpdateActivity'])
    ->name('settings.bulkUpdateActivity');

Route::put('/comments/bulk-activity',
    [CommentController::class, 'bulkUpdateActivity'])
    ->name('comments.bulkUpdateActivity');

Route::put('/currencies/bulk-activity',
    [CurrencyController::class, 'bulkUpdateActivity'])
    ->name('currencies.bulkUpdateActivity');

Route::put('course-prices/bulk-update-activity',
    [CoursePriceController::class, 'bulkUpdateActivity'])
    ->name('coursePrices.bulkUpdateActivity');

Route::put('bundle-prices/bulk-update-activity',
    [BundlePriceController::class, 'bulkUpdateActivity'])
    ->name('bundlePrices.bulkUpdateActivity');

Route::put('subscription-plans/bulk-update-activity',
    [SubscriptionPlanController::class, 'bulkUpdateActivity'])
    ->name('subscriptionPlans.bulkUpdateActivity');

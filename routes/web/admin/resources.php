<?php

// --- Основные CRUD Ресурсы ---

use App\Http\Controllers\Admin\Blog\Article\ArticleController;
use App\Http\Controllers\Admin\Blog\Banner\BannerController;
use App\Http\Controllers\Admin\Blog\Comment\CommentController;
use App\Http\Controllers\Admin\Blog\Rubric\RubricController;
use App\Http\Controllers\Admin\Blog\Tag\TagController;
use App\Http\Controllers\Admin\Blog\Video\VideoController;
use App\Http\Controllers\Admin\Finance\BundlePrice\BundlePriceController;
use App\Http\Controllers\Admin\Finance\CoursePrice\CoursePriceController;
use App\Http\Controllers\Admin\Finance\Currency\CurrencyController;
use App\Http\Controllers\Admin\Finance\Order\OrderController;
use App\Http\Controllers\Admin\Finance\SubscriptionPlan\SubscriptionPlanController;
use App\Http\Controllers\Admin\Market\MarketCompany\MarketCompanyController;
use App\Http\Controllers\Admin\School\Assignment\AssignmentController;
use App\Http\Controllers\Admin\School\Bundle\BundleController;
use App\Http\Controllers\Admin\School\CohortEnrollment\CohortEnrollmentController;
use App\Http\Controllers\Admin\School\Course\CourseController;
use App\Http\Controllers\Admin\School\CourseSchedule\CourseScheduleController;
use App\Http\Controllers\Admin\School\Enrollment\EnrollmentController;
use App\Http\Controllers\Admin\School\Hashtag\HashtagController;
use App\Http\Controllers\Admin\School\InstructorProfile\InstructorProfileController;
use App\Http\Controllers\Admin\School\LearningCategory\LearningCategoryController;
use App\Http\Controllers\Admin\School\Lesson\LessonController;
use App\Http\Controllers\Admin\School\Module\ModuleController;
use App\Http\Controllers\Admin\School\Quiz\QuizController;
use App\Http\Controllers\Admin\School\QuizAnswer\QuizAnswerController;
use App\Http\Controllers\Admin\School\QuizAttempt\QuizAttemptController;
use App\Http\Controllers\Admin\School\QuizAttemptItem\QuizAttemptItemController;
use App\Http\Controllers\Admin\School\QuizQuestion\QuizQuestionController;
use App\Http\Controllers\Admin\Statistics\Chart\ChartController;
use App\Http\Controllers\Admin\System\Component\ComponentController;
use App\Http\Controllers\Admin\System\Parameter\ParameterController;
use App\Http\Controllers\Admin\System\Permission\PermissionController;
use App\Http\Controllers\Admin\System\Report\ReportController;
use App\Http\Controllers\Admin\System\Role\RoleController;
use App\Http\Controllers\Admin\System\Setting\SettingController;
use App\Http\Controllers\Admin\System\User\UserController;
use Illuminate\Support\Facades\Route;

Route::resource('/settings', SettingController::class);

Route::resource('/parameters', ParameterController::class);

Route::resource('/users', UserController::class);

Route::resource('/roles', RoleController::class);

Route::resource('/permissions', PermissionController::class);

Route::resource('instructor-profiles', InstructorProfileController::class)
    ->parameters(['instructor-profiles' => 'instructorProfile'])
    ->names('instructorProfiles');

Route::resource('/learning-categories', LearningCategoryController::class)
    ->parameters(['learning-categories' => 'learningCategory'])
    ->names('learningCategories');

Route::resource('/hashtags', HashtagController::class)
    ->parameters(['hashtags' => 'hashtag'])
    ->names('hashtags');

Route::resource('/courses', CourseController::class);

Route::resource('/course-prices', CoursePriceController::class)
    ->parameters(['course-prices' => 'coursePrice'])
    ->names('coursePrices');

Route::resource('/modules', ModuleController::class);

Route::resource('/lessons', LessonController::class);

Route::resource('/assignments', AssignmentController::class);

Route::resource('/course-schedules', CourseScheduleController::class)
    ->parameters(['course-schedules' => 'courseSchedule'])
    ->names('courseSchedules');

Route::resource('/cohort-enrollments', CohortEnrollmentController::class)
    ->only(['index'])
    ->parameters(['cohort-enrollments' => 'cohortEnrollment'])
    ->names('cohortEnrollments');

Route::resource('/enrollments', EnrollmentController::class);

Route::resource('/quizzes', QuizController::class);

Route::resource('/quiz-questions', QuizQuestionController::class)
    ->parameters(['quiz-questions' => 'quizQuestion'])
    ->names('quizQuestions');

Route::resource('/quiz-answers', QuizAnswerController::class)
    ->parameters(['quiz-answers' => 'quizAnswer'])
    ->names('quizAnswers');

Route::resource('/quiz-attempts', QuizAttemptController::class)
    ->parameters(['quiz-attempts' => 'quizAttempt'])
    ->names('quizAttempts');

Route::resource('/quiz-attempt-items', QuizAttemptItemController::class)
    ->parameters(['quiz-attempt-items' => 'quizAttemptItem'])
    ->names('quizAttemptItems')
    ->except(['create', 'store']);

Route::resource('/bundles', BundleController::class);

Route::resource('/bundle-prices', BundlePriceController::class)
    ->parameters(['bundle-prices' => 'bundlePrice'])
    ->names('bundlePrices');

Route::resource('/subscription-plans', SubscriptionPlanController::class)
    ->parameters(['subscription-plans' => 'subscriptionPlan'])
    ->names('subscriptionPlans');

Route::resource('/currencies', CurrencyController::class);

Route::resource('/orders', OrderController::class);

Route::resource('/rubrics', RubricController::class);

Route::resource('/articles', ArticleController::class);

Route::resource('/tags', TagController::class);

Route::resource('/banners', BannerController::class);

Route::resource('/videos', VideoController::class);

Route::resource('/charts', ChartController::class)->except(['show']);

Route::resource('/reports', ReportController::class)->only(['index']);

Route::resource('/comments', CommentController::class)->except(['create', 'store', 'show']); // Админ обычно не создает комменты с нуля
Route::resource('/components', ComponentController::class);

Route::post('/components/save', [ComponentController::class, 'save'])
    ->name('components.save'); // Выносим отдельно, т.к. не ресурсный

Route::get('/reports/download', [ReportController::class, 'download'])
    ->name('reports.download'); // Выносим отдельно

Route::resource('/market-companies', MarketCompanyController::class)
    ->parameters(['market-companies' => 'marketCompany'])
    ->names('marketCompanies');

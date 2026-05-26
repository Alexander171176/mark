<?php

// --- Основные CRUD Ресурсы ---

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
use App\Http\Controllers\Admin\School\Bundle\BundleController;
use App\Http\Controllers\Admin\School\BundlePrice\BundlePriceController;
use App\Http\Controllers\Admin\School\CohortEnrollment\SchoolCohortEnrollmentController;
use App\Http\Controllers\Admin\School\Course\SchoolCourseController;
use App\Http\Controllers\Admin\School\CoursePrice\CoursePriceController;
use App\Http\Controllers\Admin\School\CourseSchedule\SchoolCourseScheduleController;
use App\Http\Controllers\Admin\School\Enrollment\SchoolEnrollmentController;
use App\Http\Controllers\Admin\School\Hashtag\SchoolHashtagController;
use App\Http\Controllers\Admin\School\InstructorProfile\SchoolInstructorProfileController;
use App\Http\Controllers\Admin\School\Lesson\SchoolLessonController;
use App\Http\Controllers\Admin\School\Module\SchoolModuleController;
use App\Http\Controllers\Admin\School\Order\SchoolOrderController;
use App\Http\Controllers\Admin\School\Quiz\SchoolQuizController;
use App\Http\Controllers\Admin\School\QuizAnswer\QuizAnswerController;
use App\Http\Controllers\Admin\School\QuizAttempt\QuizAttemptController;
use App\Http\Controllers\Admin\School\QuizAttemptItem\QuizAttemptItemController;
use App\Http\Controllers\Admin\School\QuizQuestion\SchoolQuizQuestionController;
use App\Http\Controllers\Admin\School\SubscriptionPlan\SubscriptionPlanController;
use App\Http\Controllers\Admin\School\Track\SchoolTrackController;
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

Route::resource('/school-instructor-profiles',
    SchoolInstructorProfileController::class)
    ->parameters(['school-instructor-profiles' => 'schoolInstructorProfile'])
    ->names('schoolInstructorProfiles');

Route::resource('/school-tracks',
    SchoolTrackController::class)
    ->parameters(['school-tracks' => 'schoolTrack'])
    ->names('schoolTracks');

Route::resource('/school-hashtags',
    SchoolHashtagController::class)
    ->parameters(['school-hashtags' => 'schoolHashtag'])
    ->names('schoolHashtags');

Route::resource('/school-courses',
    SchoolCourseController::class)
    ->parameters(['school-courses' => 'schoolCourse'])
    ->names('schoolCourses');

Route::resource('/course-prices', CoursePriceController::class)
    ->parameters(['course-prices' => 'coursePrice'])
    ->names('coursePrices');

Route::resource('/school-modules',
    SchoolModuleController::class)
    ->parameters(['school-modules' => 'schoolModule'])
    ->names('schoolModules');

Route::resource('/school-lessons',
    SchoolLessonController::class)
    ->parameters(['school-lessons' => 'schoolLesson'])
    ->names('schoolLessons');

Route::resource('/school-assignments',
    SchoolAssignmentController::class)
    ->parameters(['school-assignments' => 'schoolAssignment'])
    ->names('schoolAssignments');

Route::resource('/school-course-schedules',
    SchoolCourseScheduleController::class)
    ->parameters(['school-course-schedules' => 'schoolCourseSchedule'])
    ->names('schoolCourseSchedules');

Route::resource('/school-cohort-enrollments',
    SchoolCohortEnrollmentController::class)
    ->parameters(['school-cohort-enrollments' => 'schoolCohortEnrollment'])
    ->names('schoolCohortEnrollments');

Route::resource('/school-enrollments',
    SchoolEnrollmentController::class)
    ->parameters(['school-enrollments' => 'schoolEnrollment'])
    ->names('schoolEnrollments');

Route::resource('/school-quizzes',
    SchoolQuizController::class)
    ->parameters(['school-quizzes' => 'schoolQuiz'])
    ->names('schoolQuizzes');

Route::resource('/school-quiz-questions',
    SchoolQuizQuestionController::class)
    ->parameters(['school-quiz-questions' => 'schoolQuizQuestion'])
    ->names('schoolQuizQuestions');

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

Route::resource('/school-orders',
    SchoolOrderController::class)
    ->parameters(['school-orders' => 'schoolOrder'])
    ->names('schoolOrders');

Route::resource('/blog-rubrics', BlogRubricController::class)
    ->parameters(['blog-rubrics' => 'blogRubric'])
    ->names('blogRubrics');

Route::resource('/blog-articles', BlogArticleController::class)
    ->parameters(['blog-articles' => 'blogArticle'])
    ->names('blogArticles');

Route::resource('/blog-tags', BlogTagController::class)
    ->parameters(['blog-tags' => 'blogTag'])
    ->names('blogTags');

Route::resource('/blog-banners', BlogBannerController::class)
    ->parameters(['blog-banners' => 'blogBanner'])
    ->names('blogBanners');

Route::resource('/blog-videos', BlogVideoController::class)
    ->parameters(['blog-videos' => 'blogVideo'])
    ->names('blogVideos');

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

Route::resource('/market-storefronts', MarketStorefrontController::class)
    ->parameters(['market-storefronts' => 'marketStorefront'])
    ->names('marketStorefronts');

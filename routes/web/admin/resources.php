<?php

// --- Основные CRUD Ресурсы ---

use App\Http\Controllers\Admin\Blog\BlogArticle\BlogArticleController;
use App\Http\Controllers\Admin\Blog\BlogBanner\BlogBannerController;
use App\Http\Controllers\Admin\Blog\BlogRubric\BlogRubricController;
use App\Http\Controllers\Admin\Blog\BlogTag\BlogTagController;
use App\Http\Controllers\Admin\Blog\BlogVideo\BlogVideoController;
use App\Http\Controllers\Admin\Blog\Comment\CommentController;
use App\Http\Controllers\Admin\Finance\Currency\CurrencyController;
use App\Http\Controllers\Admin\Market\MarketAttribute\MarketAttributeController;
use App\Http\Controllers\Admin\Market\MarketAttributeGroup\MarketAttributeGroupController;
use App\Http\Controllers\Admin\Market\MarketAttributeValue\MarketAttributeValueController;
use App\Http\Controllers\Admin\Market\MarketBrand\MarketBrandController;
use App\Http\Controllers\Admin\Market\MarketCategory\MarketCategoryController;
use App\Http\Controllers\Admin\Market\MarketCompany\MarketCompanyController;
use App\Http\Controllers\Admin\Market\MarketShop\MarketShopController;
use App\Http\Controllers\Admin\Market\MarketTag\MarketTagController;
use App\Http\Controllers\Admin\School\SchoolAssignment\SchoolAssignmentController;
use App\Http\Controllers\Admin\School\SchoolBundle\SchoolBundleController;
use App\Http\Controllers\Admin\School\SchoolBundlePrice\SchoolBundlePriceController;
use App\Http\Controllers\Admin\School\SchoolCohortEnrollment\SchoolCohortEnrollmentController;
use App\Http\Controllers\Admin\School\SchoolCourse\SchoolCourseController;
use App\Http\Controllers\Admin\School\SchoolCoursePrice\SchoolCoursePriceController;
use App\Http\Controllers\Admin\School\SchoolCourseSchedule\SchoolCourseScheduleController;
use App\Http\Controllers\Admin\School\SchoolEnrollment\SchoolEnrollmentController;
use App\Http\Controllers\Admin\School\SchoolHashtag\SchoolHashtagController;
use App\Http\Controllers\Admin\School\SchoolInstructorProfile\SchoolInstructorProfileController;
use App\Http\Controllers\Admin\School\SchoolLesson\SchoolLessonController;
use App\Http\Controllers\Admin\School\SchoolModule\SchoolModuleController;
use App\Http\Controllers\Admin\School\SchoolOrder\SchoolOrderController;
use App\Http\Controllers\Admin\School\SchoolQuiz\SchoolQuizController;
use App\Http\Controllers\Admin\School\SchoolQuizAnswer\SchoolQuizAnswerController;
use App\Http\Controllers\Admin\School\SchoolQuizAttempt\SchoolQuizAttemptController;
use App\Http\Controllers\Admin\School\SchoolQuizAttemptItem\SchoolQuizAttemptItemController;
use App\Http\Controllers\Admin\School\SchoolQuizQuestion\SchoolQuizQuestionController;
use App\Http\Controllers\Admin\School\SchoolSubscriptionPlan\SchoolSubscriptionPlanController;
use App\Http\Controllers\Admin\School\SchoolTrack\SchoolTrackController;
use App\Http\Controllers\Admin\Statistics\Chart\ChartController;
use App\Http\Controllers\Admin\System\Component\ComponentController;
use App\Http\Controllers\Admin\System\Parameter\ParameterController;
use App\Http\Controllers\Admin\System\Permission\PermissionController;
use App\Http\Controllers\Admin\System\Report\ReportController;
use App\Http\Controllers\Admin\System\Role\RoleController;
use App\Http\Controllers\Admin\System\Setting\SettingController;
use App\Http\Controllers\Admin\System\User\UserController;
use Illuminate\Support\Facades\Route;

// системные
Route::resource('/settings', SettingController::class);
Route::resource('/parameters', ParameterController::class);
Route::resource('/users', UserController::class);
Route::resource('/roles', RoleController::class);
Route::resource('/permissions', PermissionController::class);

// редактор компонентов в админке
Route::resource('/components', ComponentController::class);
Route::post('/components/save', [ComponentController::class, 'save'])
    ->name('components.save');

// отчёты по таблицам БД
Route::resource('/reports', ReportController::class)->only(['index']);
Route::get('/reports/download', [ReportController::class, 'download'])
    ->name('reports.download');

// графики
Route::resource('/charts', ChartController::class)->except(['show']);

// валюты
Route::resource('/currencies', CurrencyController::class);

// комментарии
Route::resource('/comments', CommentController::class)->except(['create', 'store', 'show']);

// маршруты блога
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

// маршруты онлайн школы
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

Route::resource('/school-quiz-answers',
    SchoolQuizAnswerController::class)
    ->parameters(['school-quiz-answers' => 'schoolQuizAnswer'])
    ->names('schoolQuizAnswers');

Route::resource('/school-quiz-attempts',
    SchoolQuizAttemptController::class)
    ->parameters(['school-quiz-attempts' => 'schoolQuizAttempt'])
    ->names('schoolQuizAttempts');

Route::resource('/school-quiz-attempt-items',
    SchoolQuizAttemptItemController::class)
    ->parameters(['school-quiz-attempt-items' => 'schoolQuizAttemptItem'])
    ->names('schoolQuizAttemptItems');

Route::resource('/school-bundles',
    SchoolBundleController::class)
    ->parameters(['school-bundles' => 'schoolBundle'])
    ->names('schoolBundles');

Route::resource('/school-course-prices',
    SchoolCoursePriceController::class)
    ->parameters(['school-course-prices' => 'schoolCoursePrice'])
    ->names('schoolCoursePrices');

Route::resource('/school-bundle-prices',
    SchoolBundlePriceController::class)
    ->parameters(['school-bundle-prices' => 'schoolBundlePrice'])
    ->names('schoolBundlePrices');

Route::resource('/school-subscription-plans',
    SchoolSubscriptionPlanController::class)
    ->parameters(['school-subscription-plans' => 'schoolSubscriptionPlan'])
    ->names('schoolSubscriptionPlans');

Route::resource('/school-orders',
    SchoolOrderController::class)
    ->parameters(['school-orders' => 'schoolOrder'])
    ->names('schoolOrders');

// маршруты маркетплейса
Route::resource('/market-companies', MarketCompanyController::class)
    ->parameters(['market-companies' => 'marketCompany'])
    ->names('marketCompanies');

Route::resource('/market-shops', MarketShopController::class)
    ->parameters(['market-shops' => 'marketShop'])
    ->names('marketShops');

Route::resource('/market-categories', MarketCategoryController::class)
    ->parameters(['market-categories' => 'marketCategory'])
    ->names('marketCategories');

Route::resource('/market-brands', MarketBrandController::class)
    ->parameters(['market-brands' => 'marketBrand'])
    ->names('marketBrands');

Route::resource('/market-tags', MarketTagController::class)
    ->parameters(['market-tags' => 'marketTag'])
    ->names('marketTags');

Route::resource('/market-attribute-groups', MarketAttributeGroupController::class)
    ->parameters(['market-attribute-groups' => 'marketAttributeGroup'])
    ->names('marketAttributeGroups');

Route::resource('/market-attributes', MarketAttributeController::class)
    ->parameters(['market-attributes' => 'marketAttribute'])
    ->names('marketAttributes');

Route::resource('/market-attribute-values', MarketAttributeValueController::class)
    ->parameters(['market-attribute-values' => 'marketAttributeValue'])
    ->names('marketAttributeValues');

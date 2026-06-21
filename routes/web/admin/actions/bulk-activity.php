<?php

// Переключение активности массово
use App\Http\Controllers\Admin\Blog\BlogArticle\BlogArticleController;
use App\Http\Controllers\Admin\Blog\BlogBanner\BlogBannerController;
use App\Http\Controllers\Admin\Blog\BlogRubric\BlogRubricController;
use App\Http\Controllers\Admin\Blog\BlogTag\BlogTagController;
use App\Http\Controllers\Admin\Blog\BlogVideo\BlogVideoController;
use App\Http\Controllers\Admin\Blog\Comment\CommentController;
use App\Http\Controllers\Admin\Finance\Currency\CurrencyController;
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
use App\Http\Controllers\Admin\System\ImageProcessor\ImageProcessorProfileController;
use App\Http\Controllers\Admin\System\ImageProcessor\ImageProcessorVariantController;
use App\Http\Controllers\Admin\System\Parameter\ParameterController;
use Illuminate\Support\Facades\Route;

Route::put('/settings/bulk-activity',
    [ParameterController::class, 'bulkUpdateActivity'])
    ->name('settings.bulkUpdateActivity');

Route::put('/currencies/bulk-activity',
    [CurrencyController::class, 'bulkUpdateActivity'])
    ->name('currencies.bulkUpdateActivity');

Route::put('/comments/bulk-activity',
    [CommentController::class, 'bulkUpdateActivity'])
    ->name('comments.bulkUpdateActivity');

// Image Processor
Route::put('/image-processor-profiles/bulk-activity',
    [ImageProcessorProfileController::class, 'bulkUpdateActivity'])
    ->name('imageProcessorProfiles.bulkUpdateActivity');

Route::put('/image-processor-variants/bulk-activity',
    [ImageProcessorVariantController::class, 'bulkUpdateActivity'])
    ->name('imageProcessorVariants.bulkUpdateActivity');

// блог
Route::put('/blog-rubrics/bulk-activity',
    [BlogRubricController::class, 'bulkUpdateActivity'])
    ->name('blogRubrics.bulkUpdateActivity');

Route::put('/blog-articles/bulk-activity',
    [BlogArticleController::class, 'bulkUpdateActivity'])
    ->name('blogArticles.bulkUpdateActivity');

Route::put('/blog-tags/bulk-activity',
    [BlogTagController::class, 'bulkUpdateActivity'])
    ->name('blogTags.bulkUpdateActivity');

Route::put('/blog-banners/bulk-activity',
    [BlogBannerController::class, 'bulkUpdateActivity'])
    ->name('blogBanners.bulkUpdateActivity');

Route::put('/blog-videos/bulk-activity',
    [BlogVideoController::class, 'bulkUpdateActivity'])
    ->name('blogVideos.bulkUpdateActivity');

// онлайн школа
Route::put('/school-instructor-profiles/bulk-activity',
    [SchoolInstructorProfileController::class, 'bulkUpdateActivity'])
    ->name('schoolInstructorProfiles.bulkUpdateActivity');

Route::put('/school-tracks/bulk-activity',
    [SchoolTrackController::class, 'bulkUpdateActivity'])
    ->name('schoolTracks.bulkUpdateActivity');

Route::put('/school-hashtags/bulk-activity',
    [SchoolHashtagController::class, 'bulkUpdateActivity'])
    ->name('schoolHashtags.bulkUpdateActivity');

Route::put('/school-courses/bulk-activity',
    [SchoolCourseController::class, 'bulkUpdateActivity'])
    ->name('schoolCourses.bulkUpdateActivity');

Route::put('/school-modules/bulk-activity',
    [SchoolModuleController::class, 'bulkUpdateActivity'])
    ->name('schoolModules.bulkUpdateActivity');

Route::put('/school-lessons/bulk-activity',
    [SchoolLessonController::class, 'bulkUpdateActivity'])
    ->name('schoolLessons.bulkUpdateActivity');

Route::put('/school-assignments/bulk-activity',
    [SchoolAssignmentController::class, 'bulkUpdateActivity'])
    ->name('schoolAssignments.bulkUpdateActivity');

Route::put('/school-course-schedules/bulk-activity',
    [SchoolCourseScheduleController::class, 'bulkUpdateActivity'])
    ->name('schoolCourseSchedules.bulkUpdateActivity');

Route::put('/school-quizzes/bulk-activity',
    [SchoolQuizController::class, 'bulkUpdateActivity'])
    ->name('schoolQuizzes.bulkUpdateActivity');

Route::put('/school-quiz-questions/bulk-activity',
    [SchoolQuizQuestionController::class, 'bulkUpdateActivity'])
    ->name('schoolQuizQuestions.bulkUpdateActivity');

Route::put('/school-quiz-answers/bulk-activity',
    [SchoolQuizAnswerController::class, 'bulkUpdateActivity'])
    ->name('schoolQuizAnswers.bulkUpdateActivity');

Route::put('/school-bundles/bulk-activity',
    [SchoolBundleController::class, 'bulkUpdateActivity'])
    ->name('schoolBundles.bulkUpdateActivity');

Route::put('/school-course-prices/bulk-activity',
    [SchoolCoursePriceController::class, 'bulkUpdateActivity'])
    ->name('schoolCoursePrices.bulkUpdateActivity');

Route::put('/school-bundle-prices/bulk-activity',
    [SchoolBundlePriceController::class, 'bulkUpdateActivity'])
    ->name('schoolBundlePrices.bulkUpdateActivity');

Route::put('/school-subscription-plans/bulk-activity',
    [SchoolSubscriptionPlanController::class, 'bulkUpdateActivity'])
    ->name('schoolSubscriptionPlans.bulkUpdateActivity');

// маркет
Route::put('/market-companies/bulk-activity',
    [MarketCompanyController::class, 'bulkUpdateActivity'])
    ->name('marketCompanies.bulkUpdateActivity');

Route::put('/market-shops/bulk-activity',
    [MarketShopController::class, 'bulkUpdateActivity'])
    ->name('marketShops.bulkUpdateActivity');

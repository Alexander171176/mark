<?php

// Обновление сортировки для одной строки

use App\Http\Controllers\Admin\Blog\BlogArticle\BlogArticleController;
use App\Http\Controllers\Admin\Blog\BlogBanner\BlogBannerController;
use App\Http\Controllers\Admin\Blog\BlogRubric\BlogRubricController;
use App\Http\Controllers\Admin\Blog\BlogTag\BlogTagController;
use App\Http\Controllers\Admin\Blog\BlogVideo\BlogVideoController;
use App\Http\Controllers\Admin\Cms\CmsPage\CmsPageController;
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
use App\Http\Controllers\Admin\System\ImagePreset\ImagePresetController;
use App\Http\Controllers\Admin\System\Parameter\ParameterController;
use Illuminate\Support\Facades\Route;

Route::put('/parameters/{parameter}/sort',
    [ParameterController::class, 'updateSort'])
    ->name('parameters.updateSort');

Route::put(
    '/image-presets/{imagePreset}/sort',
    [ImagePresetController::class, 'updateSort']
)
    ->whereNumber('imagePreset')
    ->name('imagePresets.updateSort');

Route::put('/currencies/{currency}/sort',
    [CurrencyController::class, 'updateSort'])
    ->name('currencies.updateSort');

// блог
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

// школа
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

Route::put('/school-bundles/{schoolBundle}/sort',
    [SchoolBundleController::class, 'updateSort'])
    ->whereNumber('schoolBundle')
    ->name('schoolBundles.updateSort');

Route::put('/school-course-prices/{schoolCoursePrice}/sort',
    [SchoolCoursePriceController::class, 'updateSort'])
    ->whereNumber('schoolCoursePrice')
    ->name('schoolCoursePrices.updateSort');

Route::put('/school-bundle-prices/{schoolBundlePrice}/sort',
    [SchoolBundlePriceController::class, 'updateSort'])
    ->whereNumber('schoolBundlePrice')
    ->name('schoolBundlePrices.updateSort');

Route::put('/school-subscription-plans/{schoolSubscriptionPlan}/sort',
    [SchoolSubscriptionPlanController::class, 'updateSort'])
    ->whereNumber('schoolSubscriptionPlan')
    ->name('schoolSubscriptionPlans.updateSort');

// конструктор страниц
Route::put('/cms-pages/{cmsPage}/sort',
    [CmsPageController::class, 'updateSort'])
    ->whereNumber('cmsPage')
    ->name('cmsPages.updateSort');

// маркет
Route::put('/market-companies/{marketCompany}/sort',
    [MarketCompanyController::class, 'updateSort'])
    ->whereNumber('marketCompany')
    ->name('marketCompanies.updateSort');

Route::put('/market-shops/{marketShop}/sort',
    [MarketShopController::class, 'updateSort'])
    ->whereNumber('marketShop')
    ->name('marketShops.updateSort');

Route::put('/market-categories/{marketCategory}/sort',
    [MarketCategoryController::class, 'updateSort'])
    ->whereNumber('marketCategory')
    ->name('marketCategories.updateSort');

Route::put('/market-brands/{marketBrand}/sort',
    [MarketBrandController::class, 'updateSort'])
    ->whereNumber('marketBrand')
    ->name('marketBrands.updateSort');

Route::put('/market-tags/{marketTag}/sort',
    [MarketTagController::class, 'updateSort'])
    ->whereNumber('marketTag')
    ->name('marketTags.updateSort');

Route::put('/market-attribute-groups/{marketAttributeGroup}/sort',
    [MarketAttributeGroupController::class, 'updateSort'])
    ->whereNumber('marketAttributeGroup')
    ->name('marketAttributeGroups.updateSort');

Route::put('/market-attributes/{marketAttribute}/sort',
    [MarketAttributeController::class, 'updateSort'])
    ->whereNumber('marketAttribute')
    ->name('marketAttributes.updateSort');

Route::put('/market-attribute-values/{marketAttributeValue}/sort',
    [MarketAttributeValueController::class, 'updateSort'])
    ->whereNumber('marketAttributeValue')
    ->name('marketAttributeValues.updateSort');

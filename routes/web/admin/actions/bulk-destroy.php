<?php

// Массовое удаление

use App\Http\Controllers\Admin\Blog\BlogArticle\BlogArticleController;
use App\Http\Controllers\Admin\Blog\BlogBanner\BlogBannerController;
use App\Http\Controllers\Admin\Blog\BlogRubric\BlogRubricController;
use App\Http\Controllers\Admin\Blog\BlogTag\BlogTagController;
use App\Http\Controllers\Admin\Blog\BlogVideo\BlogVideoController;
use App\Http\Controllers\Admin\Blog\Comment\CommentController;
use App\Http\Controllers\Admin\Cms\CmsPage\CmsPageController;
use App\Http\Controllers\Admin\Finance\Currency\CurrencyController;
use App\Http\Controllers\Admin\Market\MarketAttribute\MarketAttributeController;
use App\Http\Controllers\Admin\Market\MarketAttributeGroup\MarketAttributeGroupController;
use App\Http\Controllers\Admin\Market\MarketAttributeValue\MarketAttributeValueController;
use App\Http\Controllers\Admin\Market\MarketBrand\MarketBrandController;
use App\Http\Controllers\Admin\Market\MarketCategory\MarketCategoryController;
use App\Http\Controllers\Admin\Market\MarketCompany\MarketCompanyController;
use App\Http\Controllers\Admin\Market\MarketProduct\MarketProductController;
use App\Http\Controllers\Admin\Market\MarketProductBundle\MarketProductBundleController;
use App\Http\Controllers\Admin\Market\MarketProductVariant\MarketProductVariantController;
use App\Http\Controllers\Admin\Market\MarketShop\MarketShopController;
use App\Http\Controllers\Admin\Market\MarketTag\MarketTagController;
use App\Http\Controllers\Admin\Review\ReviewController;
use App\Http\Controllers\Admin\School\SchoolAssignment\SchoolAssignmentController;
use App\Http\Controllers\Admin\School\SchoolBundlePrice\SchoolBundlePriceController;
use App\Http\Controllers\Admin\School\SchoolCoursePrice\SchoolCoursePriceController;
use App\Http\Controllers\Admin\School\SchoolHashtag\SchoolHashtagController;
use App\Http\Controllers\Admin\School\SchoolQuiz\SchoolQuizController;
use App\Http\Controllers\Admin\School\SchoolQuizAnswer\SchoolQuizAnswerController;
use App\Http\Controllers\Admin\School\SchoolQuizAttempt\SchoolQuizAttemptController;
use App\Http\Controllers\Admin\School\SchoolQuizAttemptItem\SchoolQuizAttemptItemController;
use App\Http\Controllers\Admin\School\SchoolQuizQuestion\SchoolQuizQuestionController;
use Illuminate\Support\Facades\Route;

Route::delete('/currencies/bulk-delete',
    [CurrencyController::class, 'bulkDestroy'])
    ->name('currencies.bulkDestroy');

Route::delete('/comments/bulk-delete',
    [CommentController::class, 'bulkDestroy'])
    ->name('comments.bulkDestroy');

Route::delete('/reviews/bulk-delete',
    [ReviewController::class, 'bulkDestroy'])
    ->name('reviews.bulkDestroy');

// блог
Route::delete('/blog-rubrics/bulk-delete',
    [BlogRubricController::class, 'bulkDestroy'])
    ->name('blogRubrics.bulkDestroy');

Route::delete('/blog-articles/bulk-delete',
    [BlogArticleController::class, 'bulkDestroy'])
    ->name('blogArticles.bulkDestroy');

Route::delete('/blog-tags/bulk-delete',
    [BlogTagController::class, 'bulkDestroy'])
    ->name('blogTags.bulkDestroy');

Route::delete('/blog-banners/bulk-delete',
    [BlogBannerController::class, 'bulkDestroy'])
    ->name('blogBanners.bulkDestroy');

Route::delete('/blog-videos/bulk-delete',
    [BlogVideoController::class, 'bulkDestroy'])
    ->name('blogVideos.bulkDestroy');

// школа
Route::delete('/school-hashtags/bulk-delete',
    [SchoolHashtagController::class, 'bulkDestroy'])
    ->name('schoolHashtags.bulkDestroy');

Route::delete('/school-assignments/bulk-delete',
    [SchoolAssignmentController::class, 'bulkDestroy'])
    ->name('schoolAssignments.bulkDestroy');

Route::delete('/school-quizzes/bulk-delete',
    [SchoolQuizController::class, 'bulkDestroy'])
    ->name('schoolQuizzes.bulkDestroy');

Route::delete('/school-quiz-questions/bulk-delete',
    [SchoolQuizQuestionController::class, 'bulkDestroy'])
    ->name('schoolQuizQuestions.bulkDestroy');

Route::delete('/school-quiz-answers/bulk-delete',
    [SchoolQuizAnswerController::class, 'bulkDestroy'])
    ->name('schoolQuizAnswers.bulkDestroy');

Route::delete('/school-quiz-attempts/bulk-delete',
    [SchoolQuizAttemptController::class, 'bulkDestroy'])
    ->name('schoolQuizAttempts.bulkDestroy');

Route::delete('/school-quiz-attempt-items/bulk-delete',
    [SchoolQuizAttemptItemController::class, 'bulkDestroy'])
    ->name('schoolQuizAttemptItems.bulkDestroy');

Route::delete('/school-course-prices/bulk-delete',
    [SchoolCoursePriceController::class, 'bulkDestroy'])
    ->name('schoolCoursePrices.bulkDestroy');

Route::delete('/school-bundle-prices/bulk-delete',
    [SchoolBundlePriceController::class, 'bulkDestroy'])
    ->name('schoolBundlePrices.bulkDestroy');

// конструктор страниц
Route::delete('/cms-pages/bulk-delete',
    [CmsPageController::class, 'bulkDestroy'])
    ->name('cmsPages.bulkDestroy');

// маркет
Route::delete('/market-companies/bulk-delete',
    [MarketCompanyController::class, 'bulkDestroy'])
    ->name('marketCompanies.bulkDestroy');

Route::delete('/market-shops/bulk-delete',
    [MarketShopController::class, 'bulkDestroy'])
    ->name('marketShops.bulkDestroy');

Route::delete('/market-categories/bulk-delete',
    [MarketCategoryController::class, 'bulkDestroy'])
    ->name('marketCategories.bulkDestroy');

Route::delete('/market-products/bulk-delete',
    [MarketProductController::class, 'bulkDestroy'])
    ->name('marketProducts.bulkDestroy');

Route::delete('/market-product-bundles/bulk-delete',
    [MarketProductBundleController::class, 'bulkDestroy'])
    ->name('marketProductBundles.bulkDestroy');

Route::delete('/market-product-variants/bulk-delete',
    [MarketProductVariantController::class, 'bulkDestroy'])
    ->name('marketProductVariants.bulkDestroy');

Route::delete('/market-brands/bulk-delete',
    [MarketBrandController::class, 'bulkDestroy'])
    ->name('marketBrands.bulkDestroy');

Route::delete('/market-tags/bulk-delete',
    [MarketTagController::class, 'bulkDestroy'])
    ->name('marketTags.bulkDestroy');

Route::delete('/market-attribute-groups/bulk-delete',
    [MarketAttributeGroupController::class, 'bulkDestroy'])
    ->name('marketAttributeGroups.bulkDestroy');

Route::delete('/market-attributes/bulk-delete',
    [MarketAttributeController::class, 'bulkDestroy'])
    ->name('marketAttributes.bulkDestroy');

Route::delete('/market-attribute-values/bulk-delete',
    [MarketAttributeValueController::class, 'bulkDestroy'])
    ->name('marketAttributeValues.bulkDestroy');

<?php

// Способ сортировки
use App\Http\Controllers\Admin\System\Setting\SettingController;
use Illuminate\Support\Facades\Route;

// системные
Route::put('/update-sort/settings',
    [SettingController::class, 'updateAdminSortSettings'])
    ->name('updateAdminSortSettings');

Route::put('/update-sort/image-presets',
    [SettingController::class, 'updateAdminSortImagePresets'])
    ->name('updateAdminSortImagePresets');

Route::put('/update-sort/users',
    [SettingController::class, 'updateAdminSortUsers'])
    ->name('updateAdminSortUsers');

Route::put('/update-sort/roles',
    [SettingController::class, 'updateAdminSortRoles'])
    ->name('updateAdminSortRoles');

Route::put('/update-sort/permissions',
    [SettingController::class, 'updateAdminSortPermissions'])
    ->name('updateAdminSortPermissions');

// валюта
Route::put('/update-sort/currencies',
    [SettingController::class, 'updateAdminSortCurrencies'])
    ->name('updateAdminSortCurrencies');

// комментарии
Route::put('/update-sort/comments',
    [SettingController::class, 'updateAdminSortComments'])
    ->name('updateAdminSortComments');

// блог
Route::put('/update-sort/blog-rubrics',
    [SettingController::class, 'updateAdminSortBlogRubrics'])
    ->name('updateAdminSortBlogRubrics');

Route::put('/update-sort/blog-articles',
    [SettingController::class, 'updateAdminSortBlogArticles'])
    ->name('updateAdminSortBlogArticles');

Route::put('/update-sort/blog-tags',
    [SettingController::class, 'updateAdminSortBlogTags'])
    ->name('updateAdminSortBlogTags');

Route::put('/update-sort/blog-banners',
    [SettingController::class, 'updateAdminSortBlogBanners'])
    ->name('updateAdminSortBlogBanners');

Route::put('/update-sort/blog-videos',
    [SettingController::class, 'updateAdminSortBlogVideos'])
    ->name('updateAdminSortBlogVideos');

// школа
Route::put('/update-sort/school-instructors',
    [SettingController::class, 'updateAdminSortSchoolInstructors'])
    ->name('updateAdminSortSchoolInstructors');

Route::put('/update-sort/school-tracks',
    [SettingController::class, 'updateAdminSortSchoolTracks'])
    ->name('updateAdminSortSchoolTracks');

Route::put('/update-sort/school-hashtags',
    [SettingController::class, 'updateAdminSortSchoolHashtags'])
    ->name('updateAdminSortSchoolHashtags');

Route::put('/update-sort/school-courses',
    [SettingController::class, 'updateAdminSortSchoolCourses'])
    ->name('updateAdminSortSchoolCourses');

Route::put('/update-sort/school-lessons',
    [SettingController::class, 'updateAdminSortSchoolLessons'])
    ->name('updateAdminSortSchoolLessons');

Route::put('/update-sort/school-modules',
    [SettingController::class, 'updateAdminSortSchoolModules'])
    ->name('updateAdminSortSchoolModules');

Route::put('/update-sort/school-assignments',
    [SettingController::class, 'updateAdminSortSchoolAssignments'])
    ->name('updateAdminSortSchoolAssignments');

Route::put('/update-sort/school-course-schedules',
    [SettingController::class, 'updateAdminSortSchoolCourseSchedules'])
    ->name('updateAdminSortSchoolCourseSchedules');

Route::put('/update-sort/school-cohort-enrollments',
    [SettingController::class, 'updateAdminSortSchoolCohortEnrollments'])
    ->name('updateAdminSortSchoolCohortEnrollments');

Route::put('/update-sort/school-enrollments',
    [SettingController::class, 'updateAdminSortSchoolEnrollments'])
    ->name('updateAdminSortSchoolEnrollments');

Route::put('/update-sort/school-quizzes',
    [SettingController::class, 'updateAdminSortSchoolQuizzes'])
    ->name('updateAdminSortSchoolQuizzes');

Route::put('/update-sort/school-quiz-questions',
    [SettingController::class, 'updateAdminSortSchoolQuizQuestions'])
    ->name('updateAdminSortSchoolQuizQuestions');

Route::put('/update-sort/school-quiz-answers',
    [SettingController::class, 'updateAdminSortSchoolQuizAnswers'])
    ->name('updateAdminSortSchoolQuizAnswers');

Route::put('/update-sort/school-quiz-attempts',
    [SettingController::class, 'updateAdminSortSchoolQuizAttempts'])
    ->name('updateAdminSortSchoolQuizAttempts');

Route::put('/update-sort/school-quiz-attempt-items',
    [SettingController::class, 'updateAdminSortSchoolQuizAttemptItems'])
    ->name('updateAdminSortSchoolQuizAttemptItems');

Route::put('/update-sort/school-bundles',
    [SettingController::class, 'updateAdminSortSchoolBundles'])
    ->name('updateAdminSortSchoolBundles');

Route::put('/update-sort/school-orders',
    [SettingController::class, 'updateAdminSortSchoolOrders'])
    ->name('updateAdminSortSchoolOrders');

Route::put('/update-sort/school-course-prices',
    [SettingController::class, 'updateAdminSortSchoolCoursePrices'])
    ->name('updateAdminSortSchoolCoursePrices');

Route::put('/update-sort/school-bundle-prices',
    [SettingController::class, 'updateAdminSortSchoolBundlePrices'])
    ->name('updateAdminSortSchoolBundlePrices');

Route::put('/update-sort/school-subscription-plans',
    [SettingController::class, 'updateAdminSortSchoolSubscriptionPlans'])
    ->name('updateAdminSortSchoolSubscriptionPlans');

// конструктор страниц
Route::put('/update-sort/cms-pages',
    [SettingController::class, 'updateAdminSortCmsPages'])
    ->name('updateAdminSortCmsPages');

// маркет
Route::put('/update-sort/market-companies',
    [SettingController::class, 'updateAdminSortMarketCompanies'])
    ->name('updateAdminSortMarketCompanies');

Route::put('/update-sort/market-shops',
    [SettingController::class, 'updateAdminSortMarketShops'])
    ->name('updateAdminSortMarketShops');

Route::put('/update-sort/market-categories',
    [SettingController::class, 'updateAdminSortMarketCategories'])
    ->name('updateAdminSortMarketCategories');

Route::put('/update-sort/market-products',
    [SettingController::class, 'updateAdminSortMarketProducts'])
    ->name('updateAdminSortMarketProducts');

Route::put('/update-sort/market-brands',
    [SettingController::class, 'updateAdminSortMarketBrands'])
    ->name('updateAdminSortMarketBrands');

Route::put('/update-sort/market-tags',
    [SettingController::class, 'updateAdminSortMarketTags'])
    ->name('updateAdminSortMarketTags');

Route::put('/update-sort/market-attribute-groups',
    [SettingController::class, 'updateAdminSortMarketAttributeGroups'])
    ->name('updateAdminSortMarketAttributeGroups');

Route::put('/update-sort/market-attributes',
    [SettingController::class, 'updateAdminSortMarketAttributes'])
    ->name('updateAdminSortMarketAttributes');

Route::put('/update-sort/market-attribute-values',
    [SettingController::class, 'updateAdminSortMarketAttributeValues'])
    ->name('updateAdminSortMarketAttributeValues');

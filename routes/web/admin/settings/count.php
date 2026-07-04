<?php

// Количество элементов на странице
use App\Http\Controllers\Admin\System\Setting\SettingController;
use Illuminate\Support\Facades\Route;

// системные
Route::put('/update-count/settings',
    [SettingController::class, 'updateAdminCountSettings'])
    ->name('updateAdminCountSettings');

Route::put('/update-count/image-presets',

    [SettingController::class, 'updateAdminCountImagePresets'])
    ->name('updateAdminCountImagePresets');

Route::put('/update-count/users',
    [SettingController::class, 'updateAdminCountUsers'])
    ->name('updateAdminCountUsers');

Route::put('/update-count/roles',
    [SettingController::class, 'updateAdminCountRoles'])
    ->name('updateAdminCountRoles');

Route::put('/update-count/permissions',
    [SettingController::class, 'updateAdminCountPermissions'])
    ->name('updateAdminCountPermissions');

// валюта
Route::put('/update-count/currencies',
    [SettingController::class, 'updateAdminCountCurrencies'])
    ->name('updateAdminCountCurrencies');

// комментарии
Route::put('/update-count/comments',
    [SettingController::class, 'updateAdminCountComments'])
    ->name('updateAdminCountComments');

// блог
Route::put('/update-count/blog-rubrics',
    [SettingController::class, 'updateAdminCountBlogRubrics'])
    ->name('updateAdminCountBlogRubrics');

Route::put('/update-count/blog-articles',
    [SettingController::class, 'updateAdminCountBlogArticles'])
    ->name('updateAdminCountBlogArticles');

Route::put('/update-count/blog-tags',
    [SettingController::class, 'updateAdminCountBlogTags'])
    ->name('updateAdminCountBlogTags');

Route::put('/update-count/blog-banners',
    [SettingController::class, 'updateAdminCountBlogBanners'])
    ->name('updateAdminCountBlogBanners');

Route::put('/update-count/blog-videos',
    [SettingController::class, 'updateAdminCountBlogVideos'])
    ->name('updateAdminCountBlogVideos');

// школа
Route::put('/update-count/school-instructors',
    [SettingController::class, 'updateAdminCountSchoolInstructors'])
    ->name('updateAdminCountSchoolInstructors');

Route::put('/update-count/school-tracks',
    [SettingController::class, 'updateAdminCountSchoolTracks'])
    ->name('updateAdminCountSchoolTracks');

Route::put('/update-count/school-hashtags',
    [SettingController::class, 'updateAdminCountSchoolHashtags'])
    ->name('updateAdminCountSchoolHashtags');

Route::put('/update-count/school-courses',
    [SettingController::class, 'updateAdminCountSchoolCourses'])
    ->name('updateAdminCountSchoolCourses');

Route::put('/update-count/school-modules',
    [SettingController::class, 'updateAdminCountSchoolModules'])
    ->name('updateAdminCountSchoolModules');

Route::put('/update-count/school-lessons',
    [SettingController::class, 'updateAdminCountSchoolLessons'])
    ->name('updateAdminCountSchoolLessons');

Route::put('/update-count/school-assignments',
    [SettingController::class, 'updateAdminCountSchoolAssignments'])
    ->name('updateAdminCountSchoolAssignments');

Route::put('/update-count/school-course-schedules',
    [SettingController::class, 'updateAdminCountSchoolCourseSchedules'])
    ->name('updateAdminCountSchoolCourseSchedules');

Route::put('/update-count/school-cohort-enrollments',
    [SettingController::class, 'updateAdminCountSchoolCohortEnrollments'])
    ->name('updateAdminCountSchoolCohortEnrollments');

Route::put('/update-count/school-enrollments',
    [SettingController::class, 'updateAdminCountSchoolEnrollments'])
    ->name('updateAdminCountSchoolEnrollments');

Route::put('/update-count/school-quizzes',
    [SettingController::class, 'updateAdminCountSchoolQuizzes'])
    ->name('updateAdminCountSchoolQuizzes');

Route::put('/update-count/school-quiz-questions',
    [SettingController::class, 'updateAdminCountSchoolQuizQuestions'])
    ->name('updateAdminCountSchoolQuizQuestions');

Route::put('/update-count/school-quiz-answers',
    [SettingController::class, 'updateAdminCountSchoolQuizAnswers'])
    ->name('updateAdminCountSchoolQuizAnswers');

Route::put('/update-count/school-quiz-attempts',
    [SettingController::class, 'updateAdminCountSchoolQuizAttempts'])
    ->name('updateAdminCountSchoolQuizAttempts');

Route::put('/update-count/school-quiz-attempt-items',
    [SettingController::class, 'updateAdminCountSchoolQuizAttemptItems'])
    ->name('updateAdminCountSchoolQuizAttemptItems');

Route::put('/update-count/school-bundles',
    [SettingController::class, 'updateAdminCountSchoolBundles'])
    ->name('updateAdminCountSchoolBundles');

Route::put('/update-count/school-orders',
    [SettingController::class, 'updateAdminCountSchoolOrders'])
    ->name('updateAdminCountSchoolOrders');

Route::put('/update-count/school-course-prices',
    [SettingController::class, 'updateAdminCountSchoolCoursePrices'])
    ->name('updateAdminCountSchoolCoursePrices');

Route::put('/update-count/school-bundle-prices',
    [SettingController::class, 'updateAdminCountSchoolBundlePrices'])
    ->name('updateAdminCountSchoolBundlePrices');

Route::put('/update-count/school-subscription-plans',
    [SettingController::class, 'updateAdminCountSchoolSubscriptionPlans'])
    ->name('updateAdminCountSchoolSubscriptionPlans');

// маркет
Route::put('/update-count/market-companies',
    [SettingController::class, 'updateAdminCountMarketCompanies'])
    ->name('updateAdminCountMarketCompanies');

Route::put('/update-count/market-shops',
    [SettingController::class, 'updateAdminCountMarketShops'])
    ->name('updateAdminCountMarketShops');

Route::put('/update-count/market-categories',
    [SettingController::class, 'updateAdminCountMarketCategories'])
    ->name('updateAdminCountMarketCategories');

Route::put('/update-count/market-brands',
    [SettingController::class, 'updateAdminCountMarketBrands'])
    ->name('updateAdminCountMarketBrands');

Route::put('/update-count/market-tags',
    [SettingController::class, 'updateAdminCountMarketTags'])
    ->name('updateAdminCountMarketTags');

Route::put('/update-count/market-attribute-groups',
    [SettingController::class, 'updateAdminCountMarketAttributeGroups'])
    ->name('updateAdminCountMarketAttributeGroups');

Route::put('/update-count/market-attributes',
    [SettingController::class, 'updateAdminCountMarketAttributes'])
    ->name('updateAdminCountMarketAttributes');

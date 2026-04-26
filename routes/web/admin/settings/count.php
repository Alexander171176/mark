<?php

use App\Http\Controllers\Admin\System\Setting\SettingController;
use Illuminate\Support\Facades\Route;

// Количество элементов на странице
Route::put('/update-count/rubrics', [SettingController::class, 'updateAdminCountRubrics'])
    ->name('updateAdminCountRubrics');
Route::put('/update-count/articles', [SettingController::class, 'updateAdminCountArticles'])
    ->name('updateAdminCountArticles');
Route::put('/update-count/tags', [SettingController::class, 'updateAdminCountTags'])
    ->name('updateAdminCountTags');
Route::put('/update-count/comments', [SettingController::class, 'updateAdminCountComments'])
    ->name('updateAdminCountComments');
Route::put('/update-count/banners', [SettingController::class, 'updateAdminCountBanners'])
    ->name('updateAdminCountBanners');
Route::put('/update-count/videos', [SettingController::class, 'updateAdminCountVideos'])
    ->name('updateAdminCountVideos');
Route::put('/update-count/instructors', [SettingController::class, 'updateAdminCountInstructors'])
    ->name('updateAdminCountInstructors');
Route::put('/update-count/learning-categories', [SettingController::class, 'updateAdminCountLearningCategories'])
    ->name('updateAdminCountLearningCategories');
Route::put('/update-count/hashtags', [SettingController::class, 'updateAdminCountHashtags'])
    ->name('updateAdminCountHashtags');
Route::put('/update-count/courses', [SettingController::class, 'updateAdminCountLearningCourses'])
    ->name('updateAdminCountLearningCourses');
Route::put('/update-count/modules', [SettingController::class, 'updateAdminCountLearningModules'])
    ->name('updateAdminCountLearningModules');
Route::put('/update-count/lessons', [SettingController::class, 'updateAdminCountLearningLessons'])
    ->name('updateAdminCountLearningLessons');
Route::put('/update-count/assignments', [SettingController::class, 'updateAdminCountAssignments'])
    ->name('updateAdminCountAssignments');
Route::put('/update-count/course-schedules', [SettingController::class, 'updateAdminCountCourseSchedules'])
    ->name('updateAdminCountCourseSchedules');
Route::put('/update-count/cohort-enrollments', [SettingController::class, 'updateAdminCountCohortEnrollments'])
    ->name('updateAdminCountCohortEnrollments');
Route::put('/update-count/enrollments', [SettingController::class, 'updateAdminCountEnrollments'])
    ->name('updateAdminCountEnrollments');
Route::put('/update-count/quizzes', [SettingController::class, 'updateAdminCountQuizzes'])
    ->name('updateAdminCountQuizzes');
Route::put('/update-count/quiz-questions', [SettingController::class, 'updateAdminCountQuizQuestions'])
    ->name('updateAdminCountQuizQuestions');
Route::put('/update-count/quiz-answers', [SettingController::class, 'updateAdminCountQuizAnswers'])
    ->name('updateAdminCountQuizAnswers');
Route::put('/update-count/quiz-attempts', [SettingController::class, 'updateAdminCountQuizAttempts'])
    ->name('updateAdminCountQuizAttempts');
Route::put('/update-count/quiz-attempt-items', [SettingController::class, 'updateAdminCountQuizAttemptItems'])
    ->name('updateAdminCountQuizAttemptItems');
Route::put('/update-count/bundles', [SettingController::class, 'updateAdminCountBundles'])
    ->name('updateAdminCountBundles');
Route::put('/update-count/currencies', [SettingController::class, 'updateAdminCountCurrencies'])
    ->name('updateAdminCountCurrencies');
Route::put('/update-count/orders', [SettingController::class, 'updateAdminCountOrders'])
    ->name('updateAdminCountOrders');
Route::put('/update-count/course-prices', [SettingController::class, 'updateAdminCountCoursePrices'])
    ->name('updateAdminCountCoursePrices');
Route::put('/update-count/bundle-prices', [SettingController::class, 'updateAdminCountBundlePrices'])
    ->name('updateAdminCountBundlePrices');
Route::put('/update-count/subscription-plans', [SettingController::class, 'updateAdminCountSubscriptionPlans'])
    ->name('updateAdminCountSubscriptionPlans');
Route::put('/update-count/users', [SettingController::class, 'updateAdminCountUsers'])
    ->name('updateAdminCountUsers');
Route::put('/update-count/roles', [SettingController::class, 'updateAdminCountRoles'])
    ->name('updateAdminCountRoles');
Route::put('/update-count/permissions', [SettingController::class, 'updateAdminCountPermissions'])
    ->name('updateAdminCountPermissions');
Route::put('/update-count/settings', [SettingController::class, 'updateAdminCountSettings'])
    ->name('updateAdminCountSettings');
Route::put('/update-count/settings', [SettingController::class, 'updateAdminCountMarketCompanies'])
    ->name('updateAdminCountMarketCompanies');
Route::put('/update-count/settings', [SettingController::class, 'updateAdminCountMarketStorefronts'])
    ->name('updateAdminCountMarketStorefronts');

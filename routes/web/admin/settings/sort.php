<?php

use App\Http\Controllers\Admin\System\Setting\SettingController;
use Illuminate\Support\Facades\Route;

// Тип сортировки
Route::put('/update-sort/rubrics', [SettingController::class, 'updateAdminSortRubrics'])
    ->name('updateAdminSortRubrics');
Route::put('/update-sort/articles', [SettingController::class, 'updateAdminSortArticles'])
    ->name('updateAdminSortArticles');
Route::put('/update-sort/tags', [SettingController::class, 'updateAdminSortTags'])
    ->name('updateAdminSortTags');
Route::put('/update-sort/comments', [SettingController::class, 'updateAdminSortComments'])
    ->name('updateAdminSortComments');
Route::put('/update-sort/banners', [SettingController::class, 'updateAdminSortBanners'])
    ->name('updateAdminSortBanners');
Route::put('/update-sort/videos', [SettingController::class, 'updateAdminSortVideos'])
    ->name('updateAdminSortVideos');
Route::put('/update-sort/instructors', [SettingController::class, 'updateAdminSortInstructors'])
    ->name('updateAdminSortInstructors');
Route::put('/update-sort/learning-categories', [SettingController::class, 'updateAdminSortLearningCategories'])
    ->name('updateAdminSortLearningCategories');
Route::put('/update-sort/hashtags', [SettingController::class, 'updateAdminSortHashtags'])
    ->name('updateAdminSortHashtags');
Route::put('/update-sort/courses', [SettingController::class, 'updateAdminSortLearningCourses'])
    ->name('updateAdminSortLearningCourses');
Route::put('/update-sort/lessons', [SettingController::class, 'updateAdminSortLearningLessons'])
    ->name('updateAdminSortLearningLessons');
Route::put('/update-sort/modules', [SettingController::class, 'updateAdminSortLearningModules'])
    ->name('updateAdminSortLearningModules');
Route::put('/update-sort/assignments', [SettingController::class, 'updateAdminSortAssignments'])
    ->name('updateAdminSortAssignments');
Route::put('/update-sort/course-schedules', [SettingController::class, 'updateAdminSortCourseSchedules'])
    ->name('updateAdminSortCourseSchedules');
Route::put('/update-sort/cohort-enrollments', [SettingController::class, 'updateAdminSortCohortEnrollments'])
    ->name('updateAdminSortCohortEnrollments');
Route::put('/update-sort/enrollments', [SettingController::class, 'updateAdminSortEnrollments'])
    ->name('updateAdminSortEnrollments');
Route::put('/update-sort/quizzes', [SettingController::class, 'updateAdminSortQuizzes'])
    ->name('updateAdminSortQuizzes');
Route::put('/update-sort/quiz-questions', [SettingController::class, 'updateAdminSortQuizQuestions'])
    ->name('updateAdminSortQuizQuestions');
Route::put('/update-sort/quiz-answers', [SettingController::class, 'updateAdminSortQuizAnswers'])
    ->name('updateAdminSortQuizAnswers');
Route::put('/update-sort/quiz-attempts', [SettingController::class, 'updateAdminSortQuizAttempts'])
    ->name('updateAdminSortQuizAttempts');
Route::put('/update-sort/quiz-attempt-items', [SettingController::class, 'updateAdminSortQuizAttemptItems'])
    ->name('updateAdminSortQuizAttemptItems');
Route::put('/update-sort/bundles', [SettingController::class, 'updateAdminSortBundles'])
    ->name('updateAdminSortBundles');
Route::put('/update-sort/currencies', [SettingController::class, 'updateAdminSortCurrencies'])
    ->name('updateAdminSortCurrencies');
Route::put('/update-sort/orders', [SettingController::class, 'updateAdminSortOrders'])
    ->name('updateAdminSortOrders');
Route::put('/update-sort/course-prices', [SettingController::class, 'updateAdminSortCoursePrices'])
    ->name('updateAdminSortCoursePrices');
Route::put('/update-sort/bundle-prices', [SettingController::class, 'updateAdminSortBundlePrices'])
    ->name('updateAdminSortBundlePrices');
Route::put('/update-sort/subscription-plans', [SettingController::class, 'updateAdminSortSubscriptionPlans'])
    ->name('updateAdminSortSubscriptionPlans');
Route::put('/update-sort/users', [SettingController::class, 'updateAdminSortUsers'])
    ->name('updateAdminSortUsers');
Route::put('/update-sort/roles', [SettingController::class, 'updateAdminSortRoles'])
    ->name('updateAdminSortRoles');
Route::put('/update-sort/permissions', [SettingController::class, 'updateAdminSortPermissions'])
    ->name('updateAdminSortPermissions');
Route::put('/update-sort/settings', [SettingController::class, 'updateAdminSortSettings'])
    ->name('updateAdminSortSettings');
Route::put('/update-sort/settings', [SettingController::class, 'updateAdminSortMarketCompanies'])
    ->name('updateAdminSortMarketCompanies');

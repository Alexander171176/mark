<?php

use App\Http\Controllers\Admin\Analytics\AnalyticsVisitorLog\AnalyticsVisitorLogController;
use App\Http\Controllers\Api\Blog\BlogArticle\ApiBlogArticleController;
use App\Http\Controllers\Api\Blog\BlogRubric\ApiBlogRubricController;
use App\Http\Controllers\Public\Privacy\PrivacyUserConsentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Resources\Admin\System\User\UserResource;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// --- Публичные API Маршруты (не требуют аутентификации или защищены иначе) ---

// Определение контроллеров публичной части
$siteLayout = config('site_settings.siteLayout', 'Default');
$publicRubricControllerClass = "App\\Http\\Controllers\\Public\\{$siteLayout}\\Blog\\BlogRubric\\BlogRubricController";
$publicCommentControllerClass = "App\\Http\\Controllers\\Public\\{$siteLayout}\\Blog\\CommentController";

// Рубрики для меню
if (class_exists($publicRubricControllerClass)) {
    Route::get('/menu-rubrics', [$publicRubricControllerClass, 'menuRubrics'])->name('api.rubrics.menu');
}

// Комментарии (публичная часть)
Route::prefix('comments')->name('api.comments.')->group(function () use ($publicCommentControllerClass) {
    Route::get('/', [$publicCommentControllerClass, 'index'])->name('index');
    Route::post('/', [$publicCommentControllerClass, 'store'])->name('store')->middleware('auth:sanctum');
    Route::put('/{comment}', [$publicCommentControllerClass, 'update'])->name('update')->middleware('auth:sanctum');
    Route::delete('/{comment}', [$publicCommentControllerClass, 'destroy'])->name('destroy')->middleware('auth:sanctum');
});

/*
|--------------------------------------------------------------------------
| Privacy
|--------------------------------------------------------------------------
*/

Route::prefix('privacy')
    ->name('privacy.')
    ->group(function () {

        Route::get('/consent', [PrivacyUserConsentController::class, 'show'])
            ->name('consent.show');

        Route::post('/consent', [PrivacyUserConsentController::class, 'store'])
            ->name('consent.store');

    });

/*
|--------------------------------------------------------------------------
| Analytics
|--------------------------------------------------------------------------
*/

Route::prefix('analytics')
    ->name('analytics.')
    ->group(function () {

        Route::post('/visitor-logs', [AnalyticsVisitorLogController::class, 'store'])
            ->name('visitor-logs.store');

    });

// --- Маршруты, Требующие Аутентификации (Sanctum) ---
Route::middleware('auth:sanctum')->group(function () {
    // Получение данных текущего пользователя
    Route::get('/user', function (Request $request) {
        // Используем UserResource из Admin пространства имен, т.к. он обычно более полный
        return new UserResource($request->user()->loadMissing(['roles', 'permissions']));
    })->name('api.user');

    // TODO: Добавить другие защищенные API маршруты для пользователя
});

// --- Маршруты API СТРОГО для Админ-панели ---
Route::middleware([/* 'auth:sanctum', 'role:admin' // TODO: Добавить проверку роли/разрешения */])
    ->prefix('admin') // Префикс URL /api/admin/...
    ->name('api.admin.') // Префикс имени api.admin.*
    ->group(function () {


    }); // Конец группы admin API

// --- Swagger / Blog API ---
Route::apiResource('blog-rubrics', ApiBlogRubricController::class);
Route::apiResource('blog-articles', ApiBlogArticleController::class);

<?php

use App\Http\Controllers\Admin\Analytics\AnalyticsVisitorLog\AnalyticsVisitorLogController;
use App\Http\Controllers\Api\Blog\BlogArticle\ApiBlogArticleController;
use App\Http\Controllers\Api\Blog\BlogRubric\ApiBlogRubricController;
use App\Http\Controllers\Public\Privacy\PrivacyUserConsentController;
use App\Http\Resources\Admin\System\User\UserResource;
use App\Services\SiteSettings\PublicSettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// --- Публичные API Маршруты (не требуют аутентификации или защищены иначе) ---

// Определение контроллеров публичной части
$siteLayout = app(PublicSettingsService::class)
    ->string('siteLayout', 'Default');

$publicRubricControllerClass =
    "App\\Http\\Controllers\\Public\\{$siteLayout}\\Blog\\BlogRubric\\BlogRubricController";

$publicCommentControllerClass =
    "App\\Http\\Controllers\\Public\\{$siteLayout}\\Blog\\CommentController";

// Fallback на базовый шаблон Default
if (!class_exists($publicRubricControllerClass)) {
    $publicRubricControllerClass =
        "App\\Http\\Controllers\\Public\\Default\\Blog\\BlogRubric\\BlogRubricController";
}

if (!class_exists($publicCommentControllerClass)) {
    $publicCommentControllerClass =
        "App\\Http\\Controllers\\Public\\Default\\Blog\\CommentController";
}

// Рубрики для меню
if (class_exists($publicRubricControllerClass)) {
    Route::get('/menu-rubrics', [$publicRubricControllerClass, 'menuRubrics'])
        ->name('api.rubrics.menu');
}

// Комментарии (публичная часть)
Route::prefix('comments')
    ->name('api.comments.')
    ->group(function () use ($publicCommentControllerClass) {

        Route::get('/', [$publicCommentControllerClass, 'index'])
            ->name('index');

        Route::post('/', [$publicCommentControllerClass, 'store'])
            ->middleware('auth:sanctum')
            ->name('store');

        Route::put('/{comment}', [$publicCommentControllerClass, 'update'])
            ->middleware('auth:sanctum')
            ->name('update');

        Route::delete('/{comment}', [$publicCommentControllerClass, 'destroy'])
            ->middleware('auth:sanctum')
            ->name('destroy');
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
        return new UserResource(
            $request->user()->loadMissing(['roles', 'permissions'])
        );
    })->name('api.user');

    // TODO: Добавить другие защищенные API маршруты для пользователя
});

// --- Маршруты API СТРОГО для Админ-панели ---
Route::middleware([
    /* 'auth:sanctum', 'role:admin' // TODO: Добавить проверку роли/разрешения */
])
    ->prefix('admin')
    ->name('api.admin.')
    ->group(function () {


    });

// --- Swagger / Blog API ---
Route::apiResource('blog-rubrics', ApiBlogRubricController::class);
Route::apiResource('blog-articles', ApiBlogArticleController::class);

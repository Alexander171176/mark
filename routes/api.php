<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Article\ApiArticleController;
use App\Http\Controllers\Api\Rubric\ApiRubricController;
use App\Http\Resources\Admin\System\User\UserResource;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// --- Публичные API Маршруты (не требуют аутентификации или защищены иначе) ---

// Определение контроллеров публичной части
$siteLayout = config('site_settings.siteLayout', 'Default');
$publicRubricControllerClass = "App\\Http\\Controllers\\Public\\{$siteLayout}\\Blog\\RubricController";
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

Route::apiResource('rubrics', ApiRubricController::class);
Route::apiResource('articles', ApiArticleController::class);

<?php

// --- Маршруты удаления связей ManyToMany ---

use App\Http\Controllers\Admin\System\Invokable\RemoveArticleFromTagController;
use App\Http\Controllers\Admin\System\Invokable\RemoveArticleFromVideoController;
use App\Http\Controllers\Admin\System\Invokable\RemovePermissionFromRoleController;
use App\Http\Controllers\Admin\System\Invokable\RemovePermissionFromUserController;
use App\Http\Controllers\Admin\System\Invokable\RemoveRoleFromUserController;
use App\Http\Controllers\Admin\System\Invokable\RemoveTagFromArticleController;
use Illuminate\Support\Facades\Route;

Route::delete('/roles/{role}/permissions/{permission}',
    RemovePermissionFromRoleController::class)
    ->name('roles.permissions.destroy');

Route::delete('/users/{user}/roles/{role}',
    RemoveRoleFromUserController::class)
    ->name('users.roles.destroy');

Route::delete('/users/{user}/permissions/{permission}',
    RemovePermissionFromUserController::class)
    ->name('users.permissions.destroy');

<?php

// Одобрение комментария (Используем имя модели для параметра RMB)

use App\Http\Controllers\Admin\Blog\Comment\CommentController;
use Illuminate\Support\Facades\Route;

Route::put('/comments/{comment}/approve', [CommentController::class, 'approve'])
    ->name('comments.approve');

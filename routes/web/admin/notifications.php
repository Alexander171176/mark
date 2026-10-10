<?php

use App\Http\Controllers\Admin\Notification\NotificationController;
use App\Http\Controllers\Admin\NotificationEmailLog\NotificationEmailLogController;
use Illuminate\Support\Facades\Route;

Route::prefix('notifications')
    ->name('notifications.')
    ->controller(NotificationController::class)
    ->group(function () {
        /**
         * Страница центра уведомлений.
         */
        Route::get(
            '/',
            'index'
        )->name('index');

        /**
         * Последние собственные уведомления
         * для глобального колокольчика.
         */
        Route::get(
            '/recent',
            'recent'
        )->name('recent');

        /**
         * Количество собственных
         * непрочитанных уведомлений.
         */
        Route::get(
            '/unread-count',
            'unreadCount'
        )->name('unreadCount');

        /**
         * Отметить все собственные
         * уведомления как прочитанные.
         */
        Route::patch(
            '/read-all',
            'markAllAsRead'
        )->name('markAllAsRead');

        /**
         * Отметить собственное
         * уведомление как прочитанное.
         */
        Route::patch(
            '/{notification}/read',
            'markAsRead'
        )->name('markAsRead');

        /**
         * Удалить собственное уведомление.
         */
        Route::delete(
            '/{notification}',
            'destroy'
        )->name('destroy');
    });

Route::get('notification-email-logs',
    [NotificationEmailLogController::class, 'index'])
    ->name('notificationEmailLogs.index');

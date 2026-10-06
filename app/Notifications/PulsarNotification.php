<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

abstract class PulsarNotification extends Notification
{
    use Queueable;

    /**
     * Доступные категории внутренних уведомлений PulsarCMS.
     */
    public const CATEGORY_SYSTEM = 'system';
    public const CATEGORY_BLOG = 'blog';
    public const CATEGORY_COMMENT = 'comment';
    public const CATEGORY_REVIEW = 'review';
    public const CATEGORY_FORM = 'form';
    public const CATEGORY_MARKET = 'market';
    public const CATEGORY_SCHOOL = 'school';
    public const CATEGORY_CRM = 'crm';

    /**
     * Доступные уровни внутренних уведомлений PulsarCMS.
     */
    public const LEVEL_INFO = 'info';
    public const LEVEL_SUCCESS = 'success';
    public const LEVEL_WARNING = 'warning';
    public const LEVEL_ERROR = 'error';

    /**
     * Каналы доставки уведомления.
     *
     * Пока используем только внутренние
     * уведомления PulsarCMS через БД.
     *
     * Позже конкретные уведомления смогут
     * дополнительно подключать mail и другие каналы.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Сформировать универсальную структуру
     * внутреннего уведомления PulsarCMS.
     */
    protected function databaseData(
        string $category,
        string $type,
        string $level,
        string $title,
        string $message,
        ?string $entityType = null,
        int|string|null $entityId = null,
        ?string $url = null,
        array $data = []
    ): array {
        return [
            'category' => $category,
            'type' => $type,
            'level' => $level,
            'title' => $title,
            'message' => $message,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'url' => $url,
            'data' => $data,
        ];
    }
}

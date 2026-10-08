<?php

namespace App\Services\Admin\Notification;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailable;
use Throwable;

readonly class EmailDeliveryService
{
    public function __construct(
        private EmailSettingsService $settings
    ) {
    }

    /**
     * Отправить письмо для разрешённого события.
     *
     * Возвращает true при успешной передаче письма
     * почтовому транспорту.
     */
    public function send(
        string $event,
        string $recipient,
        Mailable $mailable
    ): bool {
        // Проверяем глобальный и событийный переключатели.
        if (!$this->settings->eventEnabled($event)) {
            return false;
        }

        // Проверяем адрес получателя.
        if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            Log::warning('Email Notifications: некорректный получатель', [
                'event' => $event,
            ]);

            return false;
        }

        try {
            // Используем настроенный Laravel Mail транспорт.
            Mail::to($recipient)->send(
                $mailable->from(
                    $this->settings->fromAddress(),
                    $this->settings->fromName()
                )
            );

            return true;
        } catch (Throwable $exception) {
            Log::error('Email Notifications: ошибка отправки', [
                'event' => $event,
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);

            return false;
        }
    }
}

<?php

namespace App\Services\Admin\Notification;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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
     * Возвращает true при успешной передаче
     * письма почтовому транспорту.
     *
     * При ошибке транспорта выбрасывает исключение,
     * чтобы Queue Worker мог повторить задание.
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

        // Проверяем корректность Email.
        if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            Log::warning('Email Notifications: некорректный получатель', [
                'event' => $event,
            ]);

            return false;
        }

        try {
            // Отправляем письмо через настроенный Mail транспорт.
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

            // Передаём ошибку Laravel Queue для повторной попытки.
            throw $exception;
        }
    }
}

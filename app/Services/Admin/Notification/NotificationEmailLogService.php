<?php

namespace App\Services\Admin\Notification;

use App\Jobs\SendNotificationEmailJob;
use App\Models\Admin\NotificationEmailLog\NotificationEmailLog;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Str;
use Throwable;

class NotificationEmailLogService
{

    /**
     * Создать запись журнала перед постановкой письма в очередь.
     */
    public function create(
        string $event,
        string $recipient,
        Mailable $mailable,
        string $queue = 'emails'
    ): NotificationEmailLog {
        // Подготавливаем метаданные письма.
        // Blade-шаблон при этом не рендерится.
        $mailable->build();

        return NotificationEmailLog::create([
            'uuid' => (string) Str::uuid(),
            'event' => $event,
            'recipient' => $recipient,
            'subject' => $mailable->subject,
            'status' => NotificationEmailLog::STATUS_PENDING,
            'attempts' => 0,
            'queue' => $queue,
            'queued_at' => now(),
        ]);
    }

    /**
     * Начало обработки задания.
     */
    public function processing(
        string $uuid,
        int $attempt
    ): void {
        NotificationEmailLog::where('uuid', $uuid)
            ->update([
                'status' => NotificationEmailLog::STATUS_PROCESSING,
                'attempts' => $attempt,
                'processing_at' => now(),
                'failed_at' => null,
            ]);
    }



    /**
     * Создать запись журнала и поставить письмо в Redis Queue.
     */
    public function dispatch(
        string $event,
        string $recipient,
        Mailable $mailable
    ): void {
        // Создаём запись вместе с темой письма.
        $log = $this->create(
            $event,
            $recipient,
            $mailable,
            'emails'
        );

        // Отправляем задание после завершения транзакции.
        SendNotificationEmailJob::dispatch(
            $event,
            $recipient,
            $mailable,
            $log->uuid
        )->afterCommit();
    }


    /**
     * Письмо передано почтовому транспорту.
     */
    public function sent(
        string $uuid,
        ?string $mailer = null
    ): void {
        NotificationEmailLog::where('uuid', $uuid)
            ->update([
                'status' => NotificationEmailLog::STATUS_SENT,
                'mailer' => $mailer,
                'sent_at' => now(),
                'failed_at' => null,
                'error_type' => null,
                'error_message' => null,
            ]);
    }

    /**
     * Отправка пропущена настройками или проверками.
     */
    public function skipped(string $uuid): void
    {
        NotificationEmailLog::where('uuid', $uuid)
            ->update([
                'status' => NotificationEmailLog::STATUS_SKIPPED,
            ]);
    }

    /**
     * Промежуточная ошибка.
     */
    public function retrying(
        string $uuid,
        Throwable $exception
    ): void {
        NotificationEmailLog::where('uuid', $uuid)
            ->update([
                'status' => NotificationEmailLog::STATUS_RETRYING,
                'error_type' => $exception::class,
                'error_message' => $this->safeError($exception),
            ]);
    }

    /**
     * Окончательная ошибка.
     */
    public function failed(
        string $uuid,
        ?Throwable $exception
    ): void {
        NotificationEmailLog::where('uuid', $uuid)
            ->update([
                'status' => NotificationEmailLog::STATUS_FAILED,
                'failed_at' => now(),
                'error_type' => $exception ? $exception::class : null,
                'error_message' => $exception
                    ? $this->safeError($exception)
                    : 'Задание завершилось с ошибкой',
            ]);
    }

    /**
     * Безопасное описание ошибки для административного журнала.
     *
     * Полный текст исключения не сохраняем:
     * он может содержать адреса, учётные данные
     * или фрагменты SMTP-ответов.
     */
    private function safeError(Throwable $exception): string
    {
        return Str::limit(
            'Ошибка отправки: ' . class_basename($exception),
            1000
        );
    }
}

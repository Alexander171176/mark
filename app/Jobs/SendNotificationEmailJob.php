<?php

namespace App\Jobs;

use App\Services\Admin\Notification\EmailDeliveryService;
use App\Services\Admin\Notification\NotificationEmailLogService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendNotificationEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 10;

    public int $timeout = 60;

    public function __construct(
        public string $event,
        public string $recipient,
        public Mailable $mailable,
        public ?string $emailLogUuid = null
    ) {
        $this->onConnection('redis');
        $this->onQueue('emails');
    }

    /**
     * Выполнение Email задания.
     */
    public function handle(
        EmailDeliveryService $delivery,
        NotificationEmailLogService $emailLogs
    ): void {
        $attempt = $this->attempts();

        /*
         * Обновляем состояние перед отправкой.
         */
        if ($this->emailLogUuid !== null) {
            $emailLogs->processing(
                $this->emailLogUuid,
                $attempt
            );
        }

        try {
            $sent = $delivery->send(
                $this->event,
                $this->recipient,
                $this->mailable
            );

            if ($sent) {
                if ($this->emailLogUuid !== null) {
                    $emailLogs->sent(
                        $this->emailLogUuid,
                        (string) config('mail.default')
                    );
                }

                Log::info(
                    'Email Notifications: письмо передано транспорту',
                    [
                        'event' => $this->event,
                        'recipient' => $this->recipient,
                        'attempt' => $attempt,
                        'email_log_uuid' => $this->emailLogUuid,
                    ]
                );

                return;
            }

            /*
             * Настройки запретили отправку
             * либо получатель некорректен.
             */
            if ($this->emailLogUuid !== null) {
                $emailLogs->skipped($this->emailLogUuid);
            }

            Log::info(
                'Email Notifications: отправка пропущена',
                [
                    'event' => $this->event,
                    'email_log_uuid' => $this->emailLogUuid,
                ]
            );
        } catch (Throwable $exception) {
            if ($this->emailLogUuid !== null) {
                try {
                    $emailLogs->retrying(
                        $this->emailLogUuid,
                        $exception
                    );
                } catch (Throwable $logException) {
                    Log::error(
                        'Email Notifications: ошибка записи журнала',
                        [
                            'email_log_uuid' => $this->emailLogUuid,
                            'exception' => $logException::class,
                        ]
                    );
                }
            }

            /*
             * Обязательно передаём исключение Laravel Queue.
             */
            throw $exception;
        }
    }

    /**
     * Все попытки исчерпаны.
     */
    public function failed(?Throwable $exception): void
    {
        if ($this->emailLogUuid !== null) {
            try {
                app(NotificationEmailLogService::class)->failed(
                    $this->emailLogUuid,
                    $exception
                );
            } catch (Throwable $logException) {
                Log::error(
                    'Email Notifications: не удалось записать окончательную ошибку',
                    [
                        'email_log_uuid' => $this->emailLogUuid,
                        'exception' => $logException::class,
                    ]
                );
            }
        }

        Log::error(
            'Email Notifications: исчерпаны попытки отправки',
            [
                'event' => $this->event,
                'recipient' => $this->recipient,
                'email_log_uuid' => $this->emailLogUuid,
                'exception' => $exception?->getMessage(),
            ]
        );
    }
}

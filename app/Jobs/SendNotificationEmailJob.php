<?php

namespace App\Jobs;

use App\Services\Admin\Notification\EmailDeliveryService;
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
        public Mailable $mailable
    ) {
        $this->onConnection('redis');
        $this->onQueue('default');
    }

    public function handle(EmailDeliveryService $delivery): void
    {
        $sent = $delivery->send(
            $this->event,
            $this->recipient,
            $this->mailable
        );

        if ($sent) {
            Log::info('Email Notifications: письмо передано транспорту', [
                'event' => $this->event,
                'recipient' => $this->recipient,
                'attempt' => $this->attempts(),
            ]);
        }
    }

    public function failed(?Throwable $exception): void
    {
        Log::error('Email Notifications: исчерпаны попытки отправки', [
            'event' => $this->event,
            'recipient' => $this->recipient,
            'exception' => $exception?->getMessage(),
        ]);
    }
}

<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class TestFailedQueueJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Максимальное количество попыток.
     */
    public int $tries = 3;

    /**
     * Задержка между попытками в секундах.
     */
    public int $backoff = 10;

    /**
     * Управляемый тестовый сбой.
     *
     * Пока true — задание завершается исключением.
     * После переключения на false — выполняется успешно.
     */
    private const SIMULATE_FAILURE = false;

    /**
     * Выполнение тестового задания.
     */
    public function handle(): void
    {
        Log::info('PulsarCMS Queue: начало тестового задания', [
            'job' => self::class,
            'attempt' => $this->attempts(),
        ]);

        if (self::SIMULATE_FAILURE) {
            throw new RuntimeException(
                'PulsarCMS Queue: тестовая ошибка для проверки failed_jobs'
            );
        }

        Log::info('PulsarCMS Queue: тестовое задание восстановлено и выполнено', [
            'job' => self::class,
            'attempt' => $this->attempts(),
        ]);
    }

    /**
     * Фиксируем окончательный отказ задания.
     */
    public function failed(?\Throwable $exception): void
    {
        Log::error('PulsarCMS Queue: задание окончательно завершилось ошибкой', [
            'job' => self::class,
            'message' => $exception?->getMessage(),
        ]);
    }
}

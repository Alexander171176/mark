<?php

namespace App\Console\Commands;

use App\Models\Admin\Analytics\AnalyticsVisitorLog\AnalyticsVisitorLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RuntimeException;
use Throwable;

class ImportAnalyticsVisitorLogsCommand extends Command
{
    /**
     * Название команды.
     *
     * @var string
     */
    protected $signature = 'analytics:import-visitor-logs';

    /**
     * Описание команды.
     *
     * @var string
     */
    protected $description = 'Импортирует журналы посещений из JSONL файлов в SQLite аналитику';

    /**
     * Выполнение команды.
     */
    public function handle(): int
    {
        $incomingDirectory = storage_path('logs/analytics/incoming');
        $processingDirectory = storage_path('logs/analytics/processing');
        $processedDirectory = storage_path('logs/analytics/processed');
        $failedDirectory = storage_path('logs/analytics/failed');

        foreach ([
                     $incomingDirectory,
                     $processingDirectory,
                     $processedDirectory,
                     $failedDirectory,
                 ] as $directory) {
            if (! File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }
        }

        $files = File::files($incomingDirectory);

        if (empty($files)) {
            $this->info('Нет файлов для импорта.');

            return self::SUCCESS;
        }

        foreach ($files as $file) {
            $processingPath = $this->buildTargetPath(
                $processingDirectory,
                now()->format('Ymd_His') . '_' . $file->getFilename()
            );

            File::move($file->getPathname(), $processingPath);

            $filename = basename($processingPath);
            $imported = 0;

            try {
                DB::connection('analytics')->transaction(function () use ($processingPath, &$imported) {
                    foreach (File::lines($processingPath) as $line) {
                        if (blank($line)) {
                            continue;
                        }

                        $data = json_decode($line, true);

                        if (! is_array($data)) {
                            throw new RuntimeException('Некорректная JSON строка.');
                        }

                        AnalyticsVisitorLog::create($data);

                        $imported++;
                    }
                });

                File::move(
                    $processingPath,
                    $this->buildTargetPath($processedDirectory, $filename)
                );

                $this->info("Файл импортирован: {$filename}, записей: {$imported}");
            } catch (Throwable $exception) {
                File::move(
                    $processingPath,
                    $this->buildTargetPath($failedDirectory, $filename)
                );

                $this->error("Ошибка импорта файла {$filename}: {$exception->getMessage()}");
            }
        }

        return self::SUCCESS;
    }

    /**
     * Формирование безопасного пути назначения.
     */
    private function buildTargetPath(string $directory, string $filename): string
    {
        $path = $directory . DIRECTORY_SEPARATOR . $filename;

        if (! File::exists($path)) {
            return $path;
        }

        return $directory . DIRECTORY_SEPARATOR . now()->format('Ymd_His') . '_' . $filename;
    }
}

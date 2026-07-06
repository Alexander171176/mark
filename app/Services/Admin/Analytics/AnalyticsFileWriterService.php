<?php

namespace App\Services\Admin\Analytics;

use App\Services\Admin\Base\File\JsonlFileWriterService;

readonly class AnalyticsFileWriterService
{
    public function __construct(
        private JsonlFileWriterService $jsonlFileWriter
    ) {
    }

    public function writeVisitorLog(array $data): void
    {
        $directory = storage_path('logs/analytics/incoming');

        $filename = sprintf(
            'visitor_logs-%s.jsonl',
            now()->format('Y-m-d')
        );

        $this->jsonlFileWriter->write(
            directory: $directory,
            filename: $filename,
            data: $data
        );
    }
}

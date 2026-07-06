<?php

namespace App\Services\Admin\Base\File;

use Illuminate\Support\Facades\File;

readonly class JsonlFileWriterService
{
    public function write(string $directory, string $filename, array $data): void
    {
        if (! File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $path = $directory . DIRECTORY_SEPARATOR . $filename;

        File::append(
            $path,
            json_encode(
                $data,
                JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES
                | JSON_INVALID_UTF8_SUBSTITUTE
            ) . PHP_EOL
        );
    }
}

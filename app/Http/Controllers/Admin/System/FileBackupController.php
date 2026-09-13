<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use FilesystemIterator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use RecursiveCallbackFilterIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;
use ZipArchive;

class FileBackupController extends Controller
{
    private const BACKUP_DIR = 'file_backups';
    private const JOB_DIR = 'file_backups/jobs';
    private const BATCH_SIZE = 300;

    /**
     * Страница резервных копий файлов сайта.
     */
    public function index(): Response
    {
        $this->ensureDirectories();

        return Inertia::render('Admin/System/FileBackup');
    }

    /**
     * Список созданных архивов.
     */
    public function list(): JsonResponse
    {
        $this->ensureDirectories();

        $archives = collect(Storage::files(self::BACKUP_DIR))
            ->filter(fn (string $file) => str_ends_with(strtolower($file), '.zip'))
            ->map(fn (string $file) => [
                'name' => basename($file),
                'size' => Storage::size($file),
                'created' => Storage::lastModified($file),
            ])
            ->sortByDesc('created')
            ->values();

        return response()->json([
            'archives' => $archives,
        ]);
    }

    /**
     * Подготовить задачу архивации.
     */
    public function start(): JsonResponse
    {
        try {
            $this->ensureDirectories();

            $job = Str::uuid()->toString();

            $filename = 'site_backup_' . now()->format('Y-m-d_H-i-s') . '.zip';

            $archive = storage_path(
                'app/' . self::BACKUP_DIR . '/' . $filename
            );

            $files = $this->collectProjectFiles();

            if (empty($files)) {
                throw new RuntimeException(
                    'Нет файлов для архивации.'
                );
            }

            $state = [
                'job' => $job,
                'filename' => $filename,
                'archive' => $archive,

                'processed' => 0,
                'added' => 0,
                'skipped' => 0,

                'total' => count($files),
                'progress' => 0,

                'status' => 'processing',
                'message' => 'Подготовлен список файлов',

                'errors' => [],
                'files' => $files,
            ];

            $this->saveState(
                $job,
                $state
            );

            return response()->json(
                $this->publicState($state)
            );
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ошибка подготовки архива: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Добавить очередную порцию файлов в архив.
     */
    public function process(Request $request): JsonResponse
    {
        $request->validate([
            'job' => [
                'required',
                'string',
            ],
        ]);

        $job = basename(
            $request->job
        );

        $state = $this->loadState(
            $job
        );

        if (!$state) {
            return response()->json([
                'status' => 'error',
                'message' => 'Задача архивации не найдена',
            ], 404);
        }

        if (
            ($state['status'] ?? null) !==
            'processing'
        ) {
            return response()->json(
                $this->publicState($state)
            );
        }

        $zip = new ZipArchive();

        try {
            $archive =
                $state['archive'] ?? null;

            if (!$archive) {
                throw new RuntimeException(
                    'Не определён путь к архиву.'
                );
            }

            $processed =
                (int) ($state['processed'] ?? 0);

            $total =
                (int) ($state['total'] ?? 0);

            $mode =
                $processed === 0
                    ? ZipArchive::CREATE | ZipArchive::OVERWRITE
                    : ZipArchive::CREATE;

            $result = $zip->open(
                $archive,
                $mode
            );

            if ($result !== true) {
                throw new RuntimeException(
                    'Не удалось открыть ZIP-архив. Код ошибки: ' . $result
                );
            }

            $start = $processed;

            $end = min(
                $start + self::BATCH_SIZE,
                $total
            );

            for (
                $i = $start;
                $i < $end;
                $i++
            ) {
                $file =
                    $state['files'][$i] ??
                    null;

                if (
                    !$file ||
                    empty($file['absolute']) ||
                    empty($file['relative'])
                ) {
                    $state['skipped']++;

                    $this->addStateError(
                        $state,
                        'Пропущен некорректный элемент списка файлов.'
                    );

                    continue;
                }

                $absolute =
                    $file['absolute'];

                $relative =
                    $file['relative'];

                if (
                    !File::exists($absolute) ||
                    !File::isFile($absolute)
                ) {
                    $state['skipped']++;

                    $this->addStateError(
                        $state,
                        "Файл не найден: {$relative}"
                    );

                    continue;
                }

                if (!File::isReadable($absolute)) {
                    $state['skipped']++;

                    $this->addStateError(
                        $state,
                        "Файл недоступен для чтения: {$relative}"
                    );

                    continue;
                }

                $added = $zip->addFile(
                    $absolute,
                    $relative
                );

                if (!$added) {
                    $state['skipped']++;

                    $this->addStateError(
                        $state,
                        "Не удалось добавить файл в архив: {$relative}"
                    );

                    continue;
                }

                $state['added']++;
            }

            if (!$zip->close()) {
                throw new RuntimeException(
                    'Не удалось корректно завершить запись ZIP-архива.'
                );
            }

            $state['processed'] =
                $end;

            $state['progress'] =
                (int) floor(
                    (
                        $end /
                        max(1, $total)
                    ) * 100
                );

            $state['message'] =
                "Обработано {$end} из {$total} файлов";

            if ($end >= $total) {
                $this->finalizeArchive(
                    $state
                );
            }

            $this->saveState(
                $job,
                $state
            );

            return response()->json(
                $this->publicState($state)
            );
        } catch (Throwable $e) {
            try {
                $zip->close();
            } catch (Throwable) {
                //
            }

            $state['status'] =
                'error';

            $state['progress'] =
                (int) ($state['progress'] ?? 0);

            $state['message'] =
                'Ошибка создания архива: ' .
                $e->getMessage();

            $this->addStateError(
                $state,
                $e->getMessage()
            );

            $this->saveState(
                $job,
                $state
            );

            return response()->json(
                $this->publicState($state),
                500
            );
        }
    }

    /**
     * Получить состояние задачи.
     */
    public function status(string $job): JsonResponse
    {
        $state = $this->loadState(
            basename($job)
        );

        if (!$state) {
            return response()->json([
                'status' => 'error',
                'message' => 'Задача не найдена',
            ], 404);
        }

        return response()->json(
            $this->publicState($state)
        );
    }

    /**
     * Скачать архив.
     */
    public function download(string $file): StreamedResponse
    {
        $filename =
            basename($file);

        $path =
            self::BACKUP_DIR .
            '/' .
            $filename;

        if (!Storage::exists($path)) {
            abort(404);
        }

        return Storage::download(
            $path,
            $filename
        );
    }

    /**
     * Удалить архив.
     */
    public function delete(Request $request): JsonResponse
    {
        $request->validate([
            'file' => [
                'required',
                'string',
            ],
        ]);

        $filename =
            basename(
                $request->file
            );

        $path =
            self::BACKUP_DIR .
            '/' .
            $filename;

        if (!Storage::exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'Архив не найден',
            ], 404);
        }

        if (!Storage::delete($path)) {
            return response()->json([
                'success' => false,
                'message' => 'Не удалось удалить архив',
            ], 500);
        }

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Собрать файлы проекта.
     */
    private function collectProjectFiles(): array
    {
        $basePath = str_replace(
            '\\',
            '/',
            base_path()
        );

        /**
         * Каталоги, которые не должны попадать
         * в резервную копию файлов проекта.
         */
        $excludeDirectories = [
            '/storage/app/file_backups/',
            '/storage/app/backups/',
            '/storage/docker/',
            '/vendor/',
            '/node_modules/',
            '/.git/',
            '/.idea/',
            '/.vscode/',
            '/storage/logs/',
            '/storage/framework/cache/',
            '/storage/framework/views/',
            '/storage/framework/sessions/',
            '/storage/framework/testing/',
            '/bootstrap/cache/',
        ];

        /**
         * Отдельные файлы проекта,
         * содержащие секреты или локальные данные.
         */
        $excludeFiles = [
            '.env',
        ];

        $files = [];

        $directoryIterator =
            new RecursiveDirectoryIterator(
                base_path(),
                FilesystemIterator::SKIP_DOTS
            );

        $filterIterator =
            new RecursiveCallbackFilterIterator(
                $directoryIterator,
                function ($current) use (
                    $excludeDirectories,
                    $excludeFiles
                ) {
                    $path = str_replace(
                        '\\',
                        '/',
                        $current->getPathname()
                    );

                    if ($current->isFile()) {
                        if (
                            in_array(
                                $current->getFilename(),
                                $excludeFiles,
                                true
                            )
                        ) {
                            return false;
                        }
                    }

                    foreach (
                        $excludeDirectories
                        as $directory
                    ) {
                        if (
                            str_contains(
                                $path . (
                                $current->isDir()
                                    ? '/'
                                    : ''
                                ),
                                $directory
                            )
                        ) {
                            return false;
                        }
                    }

                    if (
                        $current->isDir() &&
                        !is_readable($path)
                    ) {
                        return false;
                    }

                    return true;
                }
            );

        $iterator =
            new RecursiveIteratorIterator(
                $filterIterator
            );

        foreach ($iterator as $file) {
            if (!$file->isFile()) {
                continue;
            }

            $absolute = str_replace(
                '\\',
                '/',
                $file->getPathname()
            );

            if (!is_readable($absolute)) {
                continue;
            }

            $relative = ltrim(
                str_replace(
                    $basePath,
                    '',
                    $absolute
                ),
                '/'
            );

            if ($relative === '') {
                continue;
            }

            $files[] = [
                'absolute' => $absolute,
                'relative' => $relative,
            ];
        }

        return $files;
    }

    /**
     * Проверить итоговый ZIP-архив.
     */
    private function finalizeArchive(array &$state): void
    {
        clearstatcache(
            true,
            $state['archive']
        );

        if (!File::exists($state['archive'])) {
            throw new RuntimeException(
                'Созданный архив не найден.'
            );
        }

        $size =
            File::size(
                $state['archive']
            );

        if ($size <= 0) {
            throw new RuntimeException(
                'Архив создан пустым.'
            );
        }

        $zip =
            new ZipArchive();

        $result =
            $zip->open(
                $state['archive']
            );

        if ($result !== true) {
            throw new RuntimeException(
                'Созданный ZIP-архив повреждён. Код ошибки: ' .
                $result
            );
        }

        try {
            $archiveFiles =
                $zip->numFiles;
        } finally {
            $zip->close();
        }

        if ($archiveFiles <= 0) {
            throw new RuntimeException(
                'ZIP-архив не содержит файлов.'
            );
        }

        $expectedAdded =
            (int) ($state['added'] ?? 0);

        if ($archiveFiles !== $expectedAdded) {
            throw new RuntimeException(
                "Проверка архива не пройдена. " .
                "Добавлено файлов: {$expectedAdded}, " .
                "найдено в ZIP: {$archiveFiles}."
            );
        }

        $state['archive_files'] =
            $archiveFiles;

        $state['archive_size'] =
            $size;

        $state['status'] =
            'done';

        $state['progress'] =
            100;

        $state['message'] =
            $this->buildCompletionMessage(
                $state
            );
    }

    /**
     * Сообщение после успешного завершения.
     */
    private function buildCompletionMessage(array $state): string
    {
        $added =
            (int) ($state['added'] ?? 0);

        $skipped =
            (int) ($state['skipped'] ?? 0);

        if ($skipped > 0) {
            return "Архив создан. Добавлено файлов: {$added}, пропущено: {$skipped}.";
        }

        return "Архив полностью создан. Добавлено файлов: {$added}.";
    }

    /**
     * Добавить ошибку или предупреждение в состояние задачи.
     */
    private function addStateError(
        array &$state,
        string $message
    ): void {
        if (!isset($state['errors'])) {
            $state['errors'] = [];
        }

        /**
         * Ограничиваем журнал, чтобы state-файл
         * не разрастался при большом количестве ошибок.
         */
        if (count($state['errors']) >= 50) {
            return;
        }

        $state['errors'][] =
            $message;
    }

    /**
     * Создать служебные каталоги.
     */
    private function ensureDirectories(): void
    {
        Storage::makeDirectory(
            self::BACKUP_DIR
        );

        Storage::makeDirectory(
            self::JOB_DIR
        );
    }

    /**
     * Сохранить состояние задачи.
     */
    private function saveState(
        string $job,
        array $state
    ): void {
        $path = storage_path(
            'app/' .
            self::JOB_DIR .
            '/' .
            basename($job) .
            '.json'
        );

        $json = json_encode(
            $state,
            JSON_UNESCAPED_UNICODE |
            JSON_UNESCAPED_SLASHES
        );

        if ($json === false) {
            throw new RuntimeException(
                'Не удалось подготовить состояние задачи архивации.'
            );
        }

        if (File::put($path, $json) === false) {
            throw new RuntimeException(
                'Не удалось сохранить состояние задачи архивации.'
            );
        }
    }

    /**
     * Загрузить состояние задачи.
     */
    private function loadState(string $job): ?array
    {
        $path = storage_path(
            'app/' .
            self::JOB_DIR .
            '/' .
            basename($job) .
            '.json'
        );

        if (!File::exists($path)) {
            return null;
        }

        $content =
            File::get($path);

        $state =
            json_decode(
                $content,
                true
            );

        if (!is_array($state)) {
            return null;
        }

        return $state;
    }

    /**
     * Состояние для frontend без тяжёлых
     * и внутренних данных.
     */
    private function publicState(array $state): array
    {
        unset(
            $state['files'],
            $state['archive']
        );

        return $state;
    }
}

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
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class FileBackupController extends Controller
{
    private const BACKUP_DIR = 'file_backups';
    private const JOB_DIR = 'file_backups/jobs';
    private const BATCH_SIZE = 300;

    /** Страница резервных копий */
    public function index(): Response
    {
        return Inertia::render('Admin/System/FileBackup');
    }

    /** Список архивов */
    public function list(): JsonResponse
    {
        $this->ensureDirectories();

        $archives = collect(Storage::files(self::BACKUP_DIR))
            ->filter(fn ($file) => str_ends_with($file, '.zip'))
            ->map(fn ($file) => [
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

    /** Запуск архивации */
    public function start(): JsonResponse
    {
        try {
            $this->ensureDirectories();

            $job = Str::uuid()->toString();
            $filename = 'site_backup_' . now()->format('Y-m-d_H-i-s') . '.zip';
            $archive = storage_path('app/' . self::BACKUP_DIR . '/' . $filename);
            $files = $this->collectProjectFiles();

            if (empty($files)) {
                throw new \RuntimeException('Нет файлов для архивации.');
            }

            $state = [
                'job' => $job,
                'filename' => $filename,
                'archive' => $archive,
                'processed' => 0,
                'total' => count($files),
                'progress' => 0,
                'status' => 'processing',
                'message' => 'Подготовлен список файлов',
                'files' => $files,
            ];

            $this->saveState($job, $state);

            return response()->json($this->publicState($state));
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ошибка подготовки архива: ' . $e->getMessage(),
            ], 500);
        }
    }

    /** Добавить очередную порцию файлов в архив */
    public function process(Request $request): JsonResponse
    {
        $request->validate([
            'job' => ['required', 'string'],
        ]);

        $job = basename($request->job);
        $state = $this->loadState($job);

        if (!$state) {
            return response()->json([
                'status' => 'error',
                'message' => 'Задача архивации не найдена',
            ], 404);
        }

        if (($state['status'] ?? null) !== 'processing') {
            return response()->json($this->publicState($state));
        }

        $zip = new ZipArchive();

        try {
            $mode = (int) $state['processed'] === 0
                ? ZipArchive::CREATE | ZipArchive::OVERWRITE
                : ZipArchive::CREATE;

            if ($zip->open($state['archive'], $mode) !== true) {
                throw new \RuntimeException('Не удалось открыть архив');
            }

            $start = (int) $state['processed'];
            $end = min($start + self::BATCH_SIZE, (int) $state['total']);

            for ($i = $start; $i < $end; $i++) {
                $file = $state['files'][$i] ?? null;

                if (!$file || empty($file['absolute']) || empty($file['relative'])) {
                    continue;
                }

                if (!File::exists($file['absolute']) || !File::isReadable($file['absolute'])) {
                    continue;
                }

                $zip->addFile($file['absolute'], $file['relative']);
            }

            $zip->close();

            $state['processed'] = $end;
            $state['progress'] = (int) floor(($end / max(1, (int) $state['total'])) * 100);
            $state['message'] = "Добавлено {$end} из {$state['total']} файлов";

            if ($end >= (int) $state['total']) {
                clearstatcache();

                if (!File::exists($state['archive']) || File::size($state['archive']) === 0) {
                    throw new \RuntimeException('Архив создан пустым.');
                }

                $state['status'] = 'done';
                $state['progress'] = 100;
                $state['message'] = 'Архив полностью создан';
            }

            $this->saveState($job, $state);

            return response()->json($this->publicState($state));
        } catch (\Throwable $e) {
            try {
                $zip->close();
            } catch (\Throwable) {
                //
            }

            $state['status'] = 'error';
            $state['message'] = 'Ошибка создания архива: ' . $e->getMessage();

            $this->saveState($job, $state);

            return response()->json($this->publicState($state), 500);
        }
    }

    /** Получить статус */
    public function status(string $job): JsonResponse
    {
        $state = $this->loadState(basename($job));

        if (!$state) {
            return response()->json([
                'status' => 'error',
                'message' => 'Задача не найдена',
            ], 404);
        }

        return response()->json($this->publicState($state));
    }

    /** Скачать архив */
    public function download(string $file): StreamedResponse
    {
        return Storage::download(self::BACKUP_DIR . '/' . basename($file));
    }

    /** Удалить архив */
    public function delete(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'string'],
        ]);

        Storage::delete(self::BACKUP_DIR . '/' . basename($request->file));

        return response()->json([
            'success' => true,
        ]);
    }

    /** Собрать файлы проекта */
    private function collectProjectFiles(): array
    {
        $exclude = [
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

        $files = [];

        $iterator = new RecursiveIteratorIterator(
            new \RecursiveCallbackFilterIterator(
                new RecursiveDirectoryIterator(
                    base_path(),
                    FilesystemIterator::SKIP_DOTS
                ),

                function ($current) use ($exclude) {

                    $path = str_replace(
                        '\\',
                        '/',
                        $current->getPathname()
                    );

                    foreach ($exclude as $dir) {
                        if (str_contains($path, $dir)) {
                            return false;
                        }
                    }

                    if ($current->isDir() && !is_readable($path)) {
                        return false;
                    }

                    return true;
                }
            )
        );

        foreach ($iterator as $file) {

            if (!$file->isFile()) {
                continue;
            }

            $path = str_replace(
                '\\',
                '/',
                $file->getPathname()
            );

            if (!is_readable($path)) {
                continue;
            }

            $files[] = [
                'absolute' => $path,

                'relative' => ltrim(
                    str_replace(
                        str_replace('\\', '/', base_path()),
                        '',
                        $path
                    ),
                    '/'
                ),
            ];
        }

        return $files;
    }

    /** Создать служебные папки */
    private function ensureDirectories(): void
    {
        Storage::makeDirectory(self::BACKUP_DIR);
        Storage::makeDirectory(self::JOB_DIR);
    }

    /** Сохранить состояние */
    private function saveState(string $job, array $state): void
    {
        File::put(
            storage_path('app/' . self::JOB_DIR . '/' . $job . '.json'),
            json_encode($state, JSON_UNESCAPED_UNICODE)
        );
    }

    /** Загрузить состояние */
    private function loadState(string $job): ?array
    {
        $path = storage_path('app/' . self::JOB_DIR . '/' . $job . '.json');

        if (!File::exists($path)) {
            return null;
        }

        return json_decode(File::get($path), true);
    }

    /** Состояние для фронта без тяжёлых данных */
    private function publicState(array $state): array
    {
        unset($state['files'], $state['archive']);

        return $state;
    }
}

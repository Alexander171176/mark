<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Response;
use Inertia\ResponseFactory;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Throwable;

class DatabaseBackupController extends Controller
{
    private const BACKUP_DIR = 'backups';

    /**
     * Страница Архивации и Восстановления
     */
    public function index(): Response|ResponseFactory
    {
        $this->ensureBackupDir();

        $backups = $this->getBackups();

        return inertia('Admin/System/DatabaseBackup', [
            'backups' => $backups,
        ]);
    }

    /**
     * Создать бэкап
     */
    public function create(): RedirectResponse
    {
        $this->ensureBackupDir();

        $filename = 'backup_' . now()->format('Y-m-d_H-i-s') . '.sql';
        $path = $this->backupPath($filename);

        try {
            $this->makeDump($path);
        } catch (Throwable $e) {
            return back()->with('error', 'Ошибка при создании бэкапа: ' . $e->getMessage());
        }

        return back()->with('success', 'Бэкап успешно создан');
    }

    /**
     * Восстановить бэкап
     */
    public function restore(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'string'],
        ]);

        $filename = basename($request->file);
        $backupToRestore = $this->backupPath($filename);

        if (!File::exists($backupToRestore)) {
            return back()->with('error', "Файл '{$filename}' не найден");
        }

        if (File::size($backupToRestore) === 0) {
            return back()->with('error', "Файл '{$filename}' пустой. Восстановление отменено.");
        }

        $rollback = 'safety_' . now()->format('Y-m-d_H-i-s') . '_' . Str::random(4) . '.sql';
        $rollbackPath = $this->backupPath($rollback);

        try {
            // 1. Создаём страховочный дамп текущей БД
            $this->makeDump($rollbackPath);

            // 2. Восстанавливаем выбранный файл
            $this->restoreDump($backupToRestore);

            return back()->with(
                'success',
                "БД успешно восстановлена из '{$filename}'. Страховочный бэкап сохранён как '{$rollback}'"
            );
        } catch (Throwable $e) {
            // 3. Если восстановление упало — пробуем откатиться
            try {
                if (File::exists($rollbackPath) && File::size($rollbackPath) > 0) {
                    $this->restoreDump($rollbackPath);
                }
            } catch (Throwable $rollbackException) {
                return back()->with(
                    'error',
                    "Ошибка восстановления: {$e->getMessage()}. "
                    . "Дополнительно не удалось выполнить откат: {$rollbackException->getMessage()}"
                );
            }

            return back()->with(
                'error',
                "Ошибка восстановления: {$e->getMessage()}. "
                . "База данных откатена до состояния перед восстановлением. Файл отката: '{$rollback}'"
            );
        }
    }

    /**
     * Удалить бэкап
     */
    public function delete(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'string'],
        ]);

        $filename = basename($request->file);
        $path = $this->backupPath($filename);

        if (!File::exists($path)) {
            return back()->with('error', 'Файл не найден');
        }

        File::delete($path);

        return back()->with('success', 'Бэкап успешно удалён');
    }

    /**
     * Список бэкапов
     */
    public function list(): JsonResponse
    {
        $this->ensureBackupDir();

        return response()->json([
            'backups' => $this->getBackups(),
        ]);
    }

    /**
     * Загрузить дамп на ПК
     */
    public function download(string $filename): StreamedResponse|RedirectResponse
    {
        $filename = basename($filename);
        $path = self::BACKUP_DIR . '/' . $filename;

        if (!Storage::disk('local')->exists($path)) {
            return back()->with('error', 'Файл не найден для загрузки.');
        }

        return Storage::disk('local')->download($path);
    }

    /**
     * Получить список файлов бэкапов
     */
    private function getBackups(): array
    {
        return collect(Storage::disk('local')->files(self::BACKUP_DIR))
            ->filter(fn ($file) => str_ends_with($file, '.sql'))
            ->filter(fn ($file) => str_starts_with(basename($file), 'backup_') || str_starts_with(basename($file), 'safety_'))
            ->map(fn ($file) => [
                'name' => basename($file),
                'size' => Storage::disk('local')->size($file),
                'created' => Storage::disk('local')->lastModified($file),
            ])
            ->sortByDesc('created')
            ->values()
            ->all();
    }

    /**
     * Создать папку для бэкапов
     */
    private function ensureBackupDir(): void
    {
        Storage::disk('local')->makeDirectory(self::BACKUP_DIR);
    }

    /**
     * Получить абсолютный путь к файлу бэкапа
     */
    private function backupPath(string $filename): string
    {
        return storage_path('app/' . self::BACKUP_DIR . '/' . basename($filename));
    }

    /**
     * Создать SQL-дамп БД
     */
    private function makeDump(string $path): void
    {
        $db = config('database.connections.mysql');

        $command = [
            'mysqldump',
            '-h' . $db['host'],
            '-P' . $db['port'],
            '-u' . $db['username'],
            '--single-transaction',
            '--quick',
            '--routines',
            '--triggers',
            '--events',
            '--default-character-set=utf8mb4',
            '--result-file=' . $path,
            $db['database'],
        ];

        $process = new Process($command);
        $process->setTimeout(300);

        if (!empty($db['password'])) {
            $process->setEnv([
                'MYSQL_PWD' => $db['password'],
            ]);
        }

        try {
            $process->mustRun();
        } catch (ProcessFailedException $e) {
            throw new RuntimeException($e->getMessage());
        }

        if (!File::exists($path) || File::size($path) === 0) {
            throw new RuntimeException('mysqldump выполнился, но файл бэкапа пустой.');
        }
    }

    /**
     * Восстановить SQL-дамп
     */
    private function restoreDump(string $path): void
    {
        $db = config('database.connections.mysql');

        $command = sprintf(
            'mysql -h%s -P%s -u%s %s < %s',
            escapeshellarg($db['host']),
            escapeshellarg($db['port']),
            escapeshellarg($db['username']),
            escapeshellarg($db['database']),
            escapeshellarg($path)
        );

        $process = new Process(['/bin/sh', '-c', $command]);
        $process->setTimeout(300);

        if (!empty($db['password'])) {
            $process->setEnv([
                'MYSQL_PWD' => $db['password'],
            ]);
        }

        try {
            $process->mustRun();
        } catch (ProcessFailedException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }
}

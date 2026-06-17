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
    private const JOB_DIR = 'backups/jobs';

    /** Страница Архивации и Восстановления */
    public function index(): Response|ResponseFactory
    {
        $this->ensureBackupDir();

        return inertia('Admin/System/DatabaseBackup', [
            'backups' => $this->getBackups(),
        ]);
    }

    /** Создать бэкап */
    public function create(): RedirectResponse|JsonResponse
    {
        $this->ensureBackupDir();

        $filename = 'backup_' . now()->format('Y-m-d_H-i-s') . '.sql';
        $path = $this->backupPath($filename);

        try {
            $this->makeDump($path);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Ошибка при создании бэкапа: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Бэкап успешно создан',
            'filename' => $filename,
        ]);
    }

    /** Запустить восстановление в фоне */
    public function restoreStart(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'string'],
        ]);

        $this->ensureBackupDir();
        $this->ensureJobDir();

        $filename = basename($request->file);
        $backupToRestore = $this->backupPath($filename);

        if (!File::exists($backupToRestore)) {
            return response()->json([
                'status' => 'error',
                'message' => "Файл '{$filename}' не найден",
            ], 404);
        }

        if (File::size($backupToRestore) === 0) {
            return response()->json([
                'status' => 'error',
                'message' => "Файл '{$filename}' пустой. Восстановление отменено.",
            ], 422);
        }

        $job = Str::uuid()->toString();
        $rollback = 'safety_' . now()->format('Y-m-d_H-i-s') . '_' . Str::random(4) . '.sql';

        $state = [
            'job' => $job,
            'filename' => $filename,
            'rollback' => $rollback,
            'status' => 'processing',
            'step' => 1,
            'progress' => 5,
            'message' => 'Задача восстановления подготовлена',
        ];

        $this->saveJobState($job, $state);

        try {
            $this->createRestoreScript($job, $filename, $rollback);
            $this->runRestoreScript($job);
        } catch (Throwable $e) {
            $state['status'] = 'error';
            $state['message'] = 'Не удалось запустить восстановление: ' . $e->getMessage();
            $this->saveJobState($job, $state);

            return response()->json($state, 500);
        }

        return response()->json($state);
    }

    /** Получить статус восстановления */
    public function restoreStatus(string $job): JsonResponse
    {
        $state = $this->getJobState(basename($job));

        if (!$state) {
            return response()->json([
                'status' => 'error',
                'message' => 'Задача восстановления не найдена',
            ], 404);
        }

        return response()->json($state);
    }

    /** Удалить бэкап */
    public function delete(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'file' => ['required', 'string'],
        ]);

        $filename = basename($request->file);
        $path = $this->backupPath($filename);

        if (!File::exists($path)) {
            return response()->json([
                'message' => 'Файл не найден',
            ], 404);
        }

        File::delete($path);

        return response()->json([
            'message' => 'Бэкап успешно удалён',
        ]);
    }

    /** Список бэкапов */
    public function list(): JsonResponse
    {
        $this->ensureBackupDir();

        return response()->json([
            'backups' => $this->getBackups(),
        ]);
    }

    /** Скачать дамп */
    public function download(string $filename): StreamedResponse|RedirectResponse
    {
        $filename = basename($filename);
        $path = self::BACKUP_DIR . '/' . $filename;

        if (!Storage::disk('local')->exists($path)) {
            return back()->with('error', 'Файл не найден для загрузки.');
        }

        return Storage::disk('local')->download($path);
    }

    /** Получить список файлов бэкапов */
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

    /** Создать SQL-дамп БД */
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
            $process->setEnv(['MYSQL_PWD' => $db['password']]);
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

    /** Создать shell-скрипт восстановления */
    private function createRestoreScript(string $job, string $filename, string $rollback): void
    {
        $db = config('database.connections.mysql');

        $jobPath = $this->jobPath($job);
        $logPath = $this->jobLogPath($job);
        $backupPath = $this->backupPath($filename);
        $rollbackPath = $this->backupPath($rollback);

        $host = escapeshellarg($db['host']);
        $port = escapeshellarg((string) $db['port']);
        $user = escapeshellarg($db['username']);
        $database = escapeshellarg($db['database']);
        $password = escapeshellarg((string) ($db['password'] ?? ''));

        $script = <<<'SH'
#!/bin/sh

write_state() {
    STATUS="$1"
    STEP="$2"
    PROGRESS="$3"
    MESSAGE="$4"

    cat > __JOB_PATH__ <<JSON
{"job":"__JOB__","filename":"__FILENAME__","rollback":"__ROLLBACK__","status":"$STATUS","step":$STEP,"progress":$PROGRESS,"message":"$MESSAGE"}
JSON
}

export MYSQL_PWD=__PASSWORD__

write_state "processing" 1 15 "Создаётся страховочный дамп текущей базы"
mysqldump -h__HOST__ -P__PORT__ -u__USER__ --single-transaction --quick --routines --triggers --events --default-character-set=utf8mb4 --result-file=__ROLLBACK_PATH__ __DATABASE__ >> __LOG_PATH__ 2>&1

if [ $? -ne 0 ]; then
    write_state "error" 1 100 "Ошибка создания страховочного дампа"
    exit 1
fi

if [ ! -s __ROLLBACK_PATH__ ]; then
    write_state "error" 1 100 "Страховочный дамп пустой. Восстановление отменено"
    exit 1
fi

write_state "processing" 2 55 "Восстанавливается выбранный дамп"
mysql -h__HOST__ -P__PORT__ -u__USER__ __DATABASE__ < __BACKUP_PATH__ >> __LOG_PATH__ 2>&1

if [ $? -ne 0 ]; then
    write_state "processing" 3 80 "Ошибка восстановления. Выполняется откат"
    mysql -h__HOST__ -P__PORT__ -u__USER__ __DATABASE__ < __ROLLBACK_PATH__ >> __LOG_PATH__ 2>&1

    if [ $? -ne 0 ]; then
        write_state "error" 3 100 "Ошибка восстановления. Дополнительно не удалось выполнить откат"
        exit 1
    fi

    write_state "error" 3 100 "Ошибка восстановления. База откатена до состояния перед восстановлением"
    exit 1
fi

write_state "done" 4 100 "База данных успешно восстановлена"
exit 0
SH;

        $script = str_replace(
            [
                '__JOB__',
                '__FILENAME__',
                '__ROLLBACK__',
                '__JOB_PATH__',
                '__LOG_PATH__',
                '__BACKUP_PATH__',
                '__ROLLBACK_PATH__',
                '__HOST__',
                '__PORT__',
                '__USER__',
                '__DATABASE__',
                '__PASSWORD__',
            ],
            [
                $job,
                $filename,
                $rollback,
                escapeshellarg($jobPath),
                escapeshellarg($logPath),
                escapeshellarg($backupPath),
                escapeshellarg($rollbackPath),
                trim($host, "'"),
                trim($port, "'"),
                trim($user, "'"),
                trim($database, "'"),
                $password,
            ],
            $script
        );

        File::put($this->jobScriptPath($job), $script);
        chmod($this->jobScriptPath($job), 0755);
    }

    /** Запустить shell-скрипт восстановления в фоне */
    private function runRestoreScript(string $job): void
    {
        $script = escapeshellarg($this->jobScriptPath($job));
        $command = "nohup /bin/sh {$script} > /dev/null 2>&1 &";

        $process = new Process(['/bin/sh', '-c', $command]);
        $process->setTimeout(10);
        $process->run();
    }

    /** Создать папку для бэкапов */
    private function ensureBackupDir(): void
    {
        Storage::disk('local')->makeDirectory(self::BACKUP_DIR);
    }

    /** Создать папку задач */
    private function ensureJobDir(): void
    {
        Storage::disk('local')->makeDirectory(self::JOB_DIR);
    }

    /** Получить абсолютный путь к файлу бэкапа */
    private function backupPath(string $filename): string
    {
        return storage_path('app/' . self::BACKUP_DIR . '/' . basename($filename));
    }

    /** Путь к JSON-файлу задачи */
    private function jobPath(string $job): string
    {
        return storage_path('app/' . self::JOB_DIR . '/' . basename($job) . '.json');
    }

    /** Путь к лог-файлу задачи */
    private function jobLogPath(string $job): string
    {
        return storage_path('app/' . self::JOB_DIR . '/' . basename($job) . '.log');
    }

    /** Путь к shell-скрипту задачи */
    private function jobScriptPath(string $job): string
    {
        return storage_path('app/' . self::JOB_DIR . '/' . basename($job) . '.sh');
    }

    /** Сохранить состояние задачи */
    private function saveJobState(string $job, array $state): void
    {
        File::put($this->jobPath($job), json_encode($state, JSON_UNESCAPED_UNICODE));
    }

    /** Получить состояние задачи */
    private function getJobState(string $job): ?array
    {
        $path = $this->jobPath($job);

        if (!File::exists($path)) {
            return null;
        }

        return json_decode(File::get($path), true);
    }
}

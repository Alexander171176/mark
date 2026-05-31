<?php

namespace App\Http\Controllers\Admin\System\Component;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Throwable;

class ComponentController extends Controller
{
    protected array $editableDirectories = [
        'Blog'           => 'js/Pages/Public/Default/Blog',
        'School'           => 'js/Pages/Public/Default/School',

        'Components'      => 'js/Components/Public/Default',

        'Partials'           => 'js/Partials/Default',
        'locales'            => 'js/locales',
    ];

    protected array $editableFiles = [
        'js/Pages/Public/Default/Index.vue' => 'js/Pages/Public/Default/Index.vue',
        'js/Layouts/DefaultLayout.vue' => 'js/Layouts/DefaultLayout.vue',
    ];

    public function index(): InertiaResponse
    {
        $fileContents = $this->getEditableFilesContent();

        return Inertia::render('Admin/System/Components/Index', [
            'fileContents' => $fileContents,
        ]);
    }

    protected function getEditableFilesContent(): array
    {
        $allFilesData = [];

        foreach ($this->editableDirectories as $displayName => $relativePath) {
            $fullDirectoryPath = resource_path(ltrim($relativePath, '/'));
            $filesData = [];

            if (! File::isDirectory($fullDirectoryPath)) {
                Log::warning("Директория компонентов не найдена: {$relativePath}");
                continue;
            }

            $files = $this->getEditableFilesFromDirectory($fullDirectoryPath);

            foreach ($files as $fullFilePath) {
                $fileKey = $this->getRelativePath($fullFilePath);

                if (! $fileKey || ! $this->isPathAllowed($fullFilePath)) {
                    continue;
                }

                try {
                    $filesData[$fileKey] = File::get($fullFilePath);
                } catch (Throwable $e) {
                    Log::error("Ошибка чтения файла компонента: {$fileKey}", [
                        'exception' => $e,
                    ]);

                    $filesData[$fileKey] = 'Ошибка чтения файла.';
                }
            }

            if (! empty($filesData)) {
                ksort($filesData);
                $allFilesData[$displayName] = $filesData;
            }
        }

        $singleFilesData = [];

        foreach ($this->editableFiles as $fileKey => $relativePath) {
            $fullFilePath = resource_path(ltrim($relativePath, '/'));

            if (! File::isFile($fullFilePath) || ! $this->isPathAllowed($fullFilePath)) {
                $singleFilesData[$fileKey] = 'Файл не найден или не разрешен.';

                Log::warning("Попытка доступа к неразрешенному файлу: {$fileKey}");

                continue;
            }

            try {
                $singleFilesData[$fileKey] = File::get($fullFilePath);
            } catch (Throwable $e) {
                Log::error("Ошибка чтения файла компонента: {$fileKey}", [
                    'exception' => $e,
                ]);

                $singleFilesData[$fileKey] = 'Ошибка чтения файла.';
            }
        }

        if (! empty($singleFilesData)) {
            ksort($singleFilesData);
            $allFilesData['Files'] = $singleFilesData;
        }

        return $allFilesData;
    }

    public function save(Request $request): RedirectResponse
    {
        $allowedFilePaths = $this->getAllAllowedFilePaths();

        $validated = $request->validate([
            'fileName' => [
                'required',
                'string',
                Rule::in(array_keys($allowedFilePaths)),
            ],
            'fileContent' => 'nullable|string|max:5242880',
        ]);

        $fileKey = $validated['fileName'];
        $newContent = $validated['fileContent'] ?? '';

        $relativePath = $allowedFilePaths[$fileKey] ?? null;
        $fullFilePath = $relativePath ? resource_path(ltrim($relativePath, '/')) : null;

        if (
            ! $fullFilePath
            || ! File::isFile($fullFilePath)
            || ! File::isWritable($fullFilePath)
            || ! $this->isPathAllowed($fullFilePath)
        ) {
            Log::error('Попытка записи в неразрешенный или несуществующий файл', [
                'key' => $fileKey,
                'path' => $fullFilePath,
            ]);

            return redirect()
                ->route('admin.components.index')
                ->with('error', __('admin/controllers.file_not_allowed_error'));
        }

        try {
            File::put($fullFilePath, $newContent, true);

            Log::info('Файл компонента успешно сохранен', [
                'path' => $fileKey,
            ]);

            return redirect()
                ->route('admin.components.index')
                ->with('success', __('admin/controllers.file_saved_success', [
                    'filename' => basename($fileKey),
                ]));
        } catch (Throwable $e) {
            Log::error("Ошибка сохранения файла компонента: {$fileKey}", [
                'exception' => $e,
            ]);

            return redirect()
                ->route('admin.components.index')
                ->with('error', __('admin/controllers.file_save_error', [
                    'filename' => basename($fileKey),
                ]));
        }
    }

    private function getAllAllowedFilePaths(): array
    {
        $allowed = $this->editableFiles;

        foreach ($this->editableDirectories as $relativePath) {
            $fullDirectoryPath = resource_path(ltrim($relativePath, '/'));

            if (! File::isDirectory($fullDirectoryPath)) {
                continue;
            }

            $files = $this->getEditableFilesFromDirectory($fullDirectoryPath);

            foreach ($files as $fullFilePath) {
                $fileKey = $this->getRelativePath($fullFilePath);

                if (! $fileKey || ! $this->isPathAllowed($fullFilePath)) {
                    continue;
                }

                $allowed[$fileKey] = $fileKey;
            }
        }

        return $allowed;
    }

    private function getEditableFilesFromDirectory(string $fullDirectoryPath): array
    {
        return collect(File::allFiles($fullDirectoryPath))
            ->filter(function ($file) {
                return in_array($file->getExtension(), ['vue', 'js'], true);
            })
            ->map(function ($file) {
                return $file->getRealPath();
            })
            ->filter()
            ->values()
            ->all();
    }

    private function isPathAllowed(string $fullPath): bool
    {
        $realFullPath = realpath($fullPath);

        if ($realFullPath === false) {
            return false;
        }

        foreach ($this->editableDirectories as $relativePath) {
            $realAllowedDir = realpath(resource_path(ltrim($relativePath, '/')));

            if (
                $realAllowedDir !== false
                && (
                    $realFullPath === $realAllowedDir
                    || str_starts_with($realFullPath, $realAllowedDir . DIRECTORY_SEPARATOR)
                )
            ) {
                return true;
            }
        }

        foreach ($this->editableFiles as $relativeFilePath) {
            $realAllowedFile = realpath(resource_path(ltrim($relativeFilePath, '/')));

            if ($realAllowedFile !== false && $realFullPath === $realAllowedFile) {
                return true;
            }
        }

        return false;
    }

    private function getRelativePath(string $fullPath): ?string
    {
        $resourcePath = realpath(resource_path());
        $realFullPath = realpath($fullPath);

        if ($resourcePath === false || $realFullPath === false) {
            return null;
        }

        if (str_starts_with($realFullPath, $resourcePath)) {
            return ltrim(str_replace($resourcePath, '', $realFullPath), DIRECTORY_SEPARATOR);
        }

        return null;
    }
}

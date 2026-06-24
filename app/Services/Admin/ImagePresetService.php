<?php

namespace App\Services\Admin;

use App\Models\Admin\System\ImagePreset\ImagePreset;
use GdImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ImagePresetService
{
    /**
     * Получить пресет по системному ключу.
     *
     * @throws ValidationException
     */
    public function getPreset(
        string $key
    ): ImagePreset {
        /** @var ImagePreset|null $preset */
        $preset = ImagePreset::query()
            ->firstWhere(
                'key',
                $key
            );

        if (!$preset) {
            throw ValidationException::withMessages([
                'preset_key'
                => "Пресет изображения [{$key}] не найден.",
            ]);
        }

        return $preset;
    }

    /**
     * Проверить загруженный файл по настройкам пресета.
     *
     * @throws ValidationException
     */
    public function validateFile(
        UploadedFile $file,
        ImagePreset $preset
    ): void {
        if (!$file->isValid()) {
            throw ValidationException::withMessages([
                'image'
                => 'Файл изображения загружен некорректно.',
            ]);
        }

        $fileSizeKb = $this->fileSizeKb($file);

        if ($fileSizeKb > $preset->max_file_size_kb) {
            throw ValidationException::withMessages([
                'image'
                => "Размер файла не должен превышать {$preset->max_file_size_kb} КБ.",
            ]);
        }

        if (!str_starts_with((string) $file->getMimeType(), 'image/')) {
            throw ValidationException::withMessages([
                'image'
                => 'Файл должен быть изображением.',
            ]);
        }
    }

    /**
     * Подготовить данные пресета для фронтового редактора.
     *
     * @throws ValidationException
     */
    public function editorPayload(
        string $presetKey
    ): array {
        $preset = $this->getPreset($presetKey);

        return $this->presetPayload($preset);
    }

    /**
     * Преобразовать модель пресета в массив для фронта.
     */
    public function presetPayload(
        ImagePreset $preset
    ): array {
        return [
            'id' => $preset->id,

            'key' => $preset->key,
            'description' => $preset->description,

            'shape' => $preset->shape,

            'width' => (int) $preset->width,
            'height' => (int) $preset->height,

            'single_size' => (int) $preset->single_size,
            'resolution' => $preset->resolution,

            'aspect_ratio' => $preset->height > 0
                ? round($preset->width / $preset->height, 2)
                : null,

            'image_rotation_enabled' => (bool) $preset->image_rotation_enabled,
            'crop_rotation_enabled' => (bool) $preset->crop_rotation_enabled,

            'max_file_size_kb' => (int) $preset->max_file_size_kb,
            'max_file_size_mb' => round($preset->max_file_size_kb / 1024, 2),

            'keep_original' => (bool) $preset->keep_original,
        ];
    }

    /**
     * Сохранить оригинал изображения, если это разрешено пресетом.
     */
    public function storeOriginalIfNeeded(
        UploadedFile $file,
        ImagePreset $preset,
        string $directory = 'images/originals',
        string $disk = 'public'
    ): ?string {
        if (!$preset->keep_original) {
            return null;
        }

        return $file->store(
            path: $directory,
            options: $disk
        );
    }

    /**
     * Первичная обработка загрузки.
     *
     * На этом этапе:
     * - получаем пресет;
     * - проверяем файл;
     * - сохраняем оригинал, если нужно;
     * - возвращаем данные для фронтового редактора.
     *
     * @throws ValidationException
     */
    public function prepareUpload(
        UploadedFile $file,
        string $presetKey,
        string $directory = 'images/originals',
        string $disk = 'public'
    ): array {
        $preset = $this->getPreset($presetKey);

        $this->validateFile(
            file: $file,
            preset: $preset
        );

        $originalPath = $this->storeOriginalIfNeeded(
            file: $file,
            preset: $preset,
            directory: $directory,
            disk: $disk
        );

        return [
            'preset' => $this->presetPayload($preset),

            'original_path' => $originalPath,
            'original_url' => $originalPath
                ? Storage::disk($disk)->url($originalPath)
                : null,

            'file' => $this->filePayload($file),
        ];
    }

    /**
     * Данные файла для ответа.
     */
    public function filePayload(
        UploadedFile $file
    ): array {
        return [
            'name' => $file->getClientOriginalName(),
            'mime' => $file->getMimeType(),
            'size_kb' => $this->fileSizeKb($file),
            'extension' => $file->getClientOriginalExtension(),
        ];
    }

    /**
     * Размер файла в килобайтах.
     */
    public function fileSizeKb(
        UploadedFile $file
    ): int {
        return (int) ceil(
            $file->getSize() / 1024
        );
    }

    /**
     * Сохранить загруженное изображение через пресет.
     *
     * Делает:
     * - проверку файла;
     * - сохранение оригинала, если включено;
     * - resize по принципу cover;
     * - crop по центру;
     * - сохранение результата в WebP.
     *
     * @throws ValidationException
     */
    public function storeUploadedImage(
        UploadedFile $file,
        string $presetKey,
        string $directory,
        string $disk = 'public',
        bool $storeOriginal = false
    ): string {
        $preset = $this->getPreset($presetKey);

        $this->validateFile(
            file: $file,
            preset: $preset
        );

        if ($storeOriginal) {
            $this->storeOriginalIfNeeded(
                file: $file,
                preset: $preset,
                directory: "{$directory}/originals",
                disk: $disk
            );
        }

        return $this->processAndStoreImage(
            file: $file,
            preset: $preset,
            directory: $directory,
            disk: $disk
        );
    }

    /**
     * Обработать изображение и сохранить результат.
     *
     * @throws ValidationException
     */
    public function processAndStoreImage(
        UploadedFile $file,
        ImagePreset $preset,
        string $directory,
        string $disk = 'public'
    ): string {
        $source = $this->makeImageFromUploadedFile($file);

        $source = $this->fixImageOrientation(
            image: $source,
            file: $file,
            preset: $preset
        );

        $processed = $this->resizeAndCropCenter(
            source: $source,
            targetWidth: (int) $preset->width,
            targetHeight: (int) $preset->height
        );

        if ($preset->shape === 'circle') {
            $processed = $this->applyCircleMask($processed);
        }

        $path = trim($directory, '/') . '/' . Str::uuid() . '.webp';

        ob_start();

        imagewebp(
            image: $processed,
            quality: 85
        );

        $contents = ob_get_clean();

        imagedestroy($source);
        imagedestroy($processed);

        Storage::disk($disk)->put(
            $path,
            $contents
        );

        return $path;
    }

    /**
     * Создать GD-изображение из загруженного файла.
     *
     * @throws ValidationException
     */
    protected function makeImageFromUploadedFile(
        UploadedFile $file
    ): GdImage {
        $mime = (string) $file->getMimeType();

        $path = $file->getPathname();

        $image = match ($mime) {
            'image/jpeg', 'image/jpg' => imagecreatefromjpeg($path),
            'image/png' => imagecreatefrompng($path),
            'image/webp' => imagecreatefromwebp($path),

            default => false,
        };

        if (!$image instanceof GdImage) {
            throw ValidationException::withMessages([
                'image' => 'Не удалось прочитать изображение.',
            ]);
        }

        imagepalettetotruecolor($image);
        imagealphablending($image, true);
        imagesavealpha($image, true);

        return $image;
    }

    /**
     * Исправить ориентацию JPEG по EXIF.
     */
    protected function fixImageOrientation(
        GdImage $image,
        UploadedFile $file,
        ImagePreset $preset
    ): GdImage {
        if (!$preset->image_rotation_enabled) {
            return $image;
        }

        if (
            !function_exists('exif_read_data') ||
            !in_array($file->getMimeType(), ['image/jpeg', 'image/jpg'], true)
        ) {
            return $image;
        }

        $exif = @exif_read_data($file->getPathname());

        $orientation = $exif['Orientation'] ?? null;

        return match ($orientation) {
            3 => imagerotate($image, 180, 0),
            6 => imagerotate($image, -90, 0),
            8 => imagerotate($image, 90, 0),

            default => $image,
        };
    }

    /**
     * Resize по принципу cover + crop по центру.
     */
    protected function resizeAndCropCenter(
        GdImage $source,
        int $targetWidth,
        int $targetHeight
    ): GdImage {
        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);

        $scale = max(
            $targetWidth / $sourceWidth,
            $targetHeight / $sourceHeight
        );

        $resizeWidth = (int) ceil($sourceWidth * $scale);
        $resizeHeight = (int) ceil($sourceHeight * $scale);

        $resized = imagecreatetruecolor(
            $resizeWidth,
            $resizeHeight
        );

        imagealphablending($resized, false);
        imagesavealpha($resized, true);

        imagecopyresampled(
            $resized,
            $source,
            0,
            0,
            0,
            0,
            $resizeWidth,
            $resizeHeight,
            $sourceWidth,
            $sourceHeight
        );

        $cropX = (int) floor(($resizeWidth - $targetWidth) / 2);
        $cropY = (int) floor(($resizeHeight - $targetHeight) / 2);

        $cropped = imagecreatetruecolor(
            $targetWidth,
            $targetHeight
        );

        imagealphablending($cropped, false);
        imagesavealpha($cropped, true);

        imagecopy(
            $cropped,
            $resized,
            0,
            0,
            $cropX,
            $cropY,
            $targetWidth,
            $targetHeight
        );

        imagedestroy($resized);

        return $cropped;
    }

    /**
     * Наложить круглую маску.
     */
    protected function applyCircleMask(
        GdImage $image
    ): GdImage {
        $width = imagesx($image);
        $height = imagesy($image);

        $size = min($width, $height);

        $circle = imagecreatetruecolor(
            $size,
            $size
        );

        imagealphablending($circle, false);
        imagesavealpha($circle, true);

        $transparent = imagecolorallocatealpha(
            $circle,
            0,
            0,
            0,
            127
        );

        imagefill($circle, 0, 0, $transparent);

        $maskColor = imagecolorallocate(
            $circle,
            255,
            255,
            255
        );

        imagefilledellipse(
            $circle,
            (int) ($size / 2),
            (int) ($size / 2),
            $size,
            $size,
            $maskColor
        );

        for ($x = 0; $x < $size; $x++) {
            for ($y = 0; $y < $size; $y++) {
                $alpha = imagecolorat($circle, $x, $y);

                if (($alpha & 0xFF) === 0) {
                    imagesetpixel(
                        $image,
                        $x,
                        $y,
                        $transparent
                    );
                }
            }
        }

        imagedestroy($circle);

        return $image;
    }
}

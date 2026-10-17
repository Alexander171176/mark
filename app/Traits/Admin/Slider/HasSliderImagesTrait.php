<?php

namespace App\Traits\Admin\Slider;

use App\Models\Admin\Slider\SliderSlide\SliderSlide;
use App\Models\Admin\Slider\SliderSlide\SliderSlideImage;
use App\Services\Admin\AdminFeatureService;
use App\Services\Admin\ImagePresetService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

/** Управление общими изображениями слайдов и параметрами их размещения. */
trait HasSliderImagesTrait
{
    protected function imageProcessorEnabled(): bool
    {
        return app(AdminFeatureService::class)->imageProcessorEnabled();
    }

    protected function imagePresetKey(): string
    {
        return 'rectangle_large';
    }

    protected function imagePresetDirectory(): string
    {
        return 'slider/images';
    }

    protected function imagePresetPayload(): ?array
    {
        if (! $this->imageProcessorEnabled()) {
            return null;
        }

        try {
            return app(ImagePresetService::class)->editorPayload($this->imagePresetKey());
        } catch (Throwable $exception) {
            Log::error('Ошибка загрузки пресета изображений слайдов.', [
                'exception' => $exception,
            ]);
            return null;
        }
    }

    /**
     * Если images не передан, вызывающий код не должен вызывать этот метод.
     * Переданный пустой массив означает отсоединение всех изображений.
     * Существующие изображения из общего каталога разрешено подключать повторно.
     * @throws ValidationException
     */
    protected function syncSlideImages(
        SliderSlide $slide,
        Request $request,
        array $images,
        array $deletedIds = []
    ): void {
        $deletedIds = array_values(array_unique(array_map('intval', $deletedIds)));
        $currentIds = $slide->images()->pluck('slider_slide_images.id')
            ->map(fn ($id) => (int) $id)->all();
        $sync = [];

        foreach ($images as $index => $item) {
            if (! is_array($item)) {
                throw ValidationException::withMessages(["images.{$index}" => 'Неверный формат изображения.']);
            }

            $id = ! empty($item['id']) ? (int) $item['id'] : null;
            $file = $request->file("images.{$index}.file");

            if ($id !== null) {
                if (in_array($id, $deletedIds, true)) {
                    throw ValidationException::withMessages(["images.{$index}.id" => 'Изображение помечено для удаления.']);
                }
                $image = SliderSlideImage::query()->find($id);
                if (! $image) {
                    throw ValidationException::withMessages(["images.{$index}.id" => 'Изображение не найдено.']);
                }
                if ($file) {
                    throw ValidationException::withMessages(["images.{$index}.file" => 'Для замены общего изображения создайте новую запись.']);
                }
                // Метаданные общей модели нельзя менять из размещения в другом слайде.
                if (in_array($id, $currentIds, true)) {
                    $image->update(Arr::only($item, ['alt', 'caption']));
                }
            } else {
                if (! $file instanceof UploadedFile) {
                    throw ValidationException::withMessages(["images.{$index}.file" => 'Необходимо выбрать файл изображения.']);
                }
                $image = SliderSlideImage::query()->create([
                    'order' => (int) ($item['order'] ?? $index),
                    'alt' => $item['alt'] ?? null,
                    'caption' => $item['caption'] ?? null,
                ]);
                $this->addSlideImageMedia($image, $file);
            }

            $purpose = $item['purpose'] ?? 'desktop';
            if (! in_array($purpose, ['desktop', 'mobile', 'both'], true)) {
                throw ValidationException::withMessages(["images.{$index}.purpose" => 'Недопустимое назначение изображения.']);
            }
            if (isset($sync[$image->id])) {
                throw ValidationException::withMessages(["images.{$index}.id" => 'Изображение указано повторно.']);
            }
            $sync[$image->id] = [
                'purpose' => $purpose,
                'order' => (int) ($item['order'] ?? $index),
            ];
        }

        $slide->images()->sync($sync);
        // Не удаляем физические файлы: записи могут использоваться другими слайдами.
    }

    protected function addSlideImageMedia(SliderSlideImage $image, UploadedFile $file): void
    {
        if (! $this->imageProcessorEnabled() || $file->getMimeType() === 'image/webp') {
            $image->addMedia($file)->toMediaCollection('images');
            return;
        }

        $path = app(ImagePresetService::class)->storeUploadedImage(
            file: $file,
            presetKey: $this->imagePresetKey(),
            directory: $this->imagePresetDirectory()
        );

        try {
            $image->addMediaFromDisk($path, 'public')->toMediaCollection('images');
        } finally {
            Storage::disk('public')->delete($path);
        }
    }

    /** Удаление только неиспользуемых изображений; вызывается отдельно, не из sync. */
    protected function deleteUnusedSlideImages(array $ids): int
    {
        $deleted = 0;
        foreach (SliderSlideImage::query()->whereKey($ids)->get() as $image) {
            if ($image->slides()->exists()) {
                continue;
            }
            $image->delete(); // Spatie InteractsWithMedia удаляет связанные media при удалении модели.
            $deleted++;
        }
        return $deleted;
    }
}

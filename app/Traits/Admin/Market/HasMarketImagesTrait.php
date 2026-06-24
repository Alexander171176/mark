<?php

namespace App\Traits\Admin\Market;

use App\Services\Admin\ImagePresetService;
use App\Services\SiteSettings\AdminSettingsService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait HasMarketImagesTrait
{
    /**
     * Синхронизация изображений:
     * - создание;
     * - обновление;
     * - удаление;
     * - порядок;
     * - обработка через ImagePresetService, если модуль включён.
     */
    protected function syncImages(
        Model $model,
        Request $request,
        array $imagesData,
        array $deletedImageIds = []
    ): void {
        $imageTable = (new $this->imageModelClass)->getTable();

        $currentImageIds = $model->images()
            ->pluck("{$imageTable}.id")
            ->map(fn ($id) => (int) $id)
            ->toArray();

        $incomingImageIds = collect($imagesData)
            ->pluck('id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->toArray();

        $autoDeletedIds = array_values(
            array_diff($currentImageIds, $incomingImageIds)
        );

        $deletedImageIds = array_values(
            array_unique(
                array_map(
                    'intval',
                    array_merge($deletedImageIds, $autoDeletedIds)
                )
            )
        );

        if (!empty($deletedImageIds)) {
            $model->images()->detach($deletedImageIds);
            $this->deleteImages($deletedImageIds);
        }

        $syncData = [];

        foreach ($imagesData as $index => $imageData) {
            $fileKey = "images.{$index}.file";

            if (!empty($imageData['id'])) {
                $image = $this->imageModelClass::find($imageData['id']);

                if (!$image || in_array((int) $image->id, $deletedImageIds, true)) {
                    continue;
                }

                $image->update([
                    'order' => $imageData['order'] ?? $image->order,
                    'alt' => $imageData['alt'] ?? $image->alt,
                    'caption' => $imageData['caption'] ?? $image->caption,
                ]);

                if ($request->hasFile($fileKey)) {
                    $image->clearMediaCollection($this->imageMediaCollection);

                    $this->addImageMedia(
                        image: $image,
                        file: $request->file($fileKey)
                    );
                }

                $syncData[$image->id] = [
                    'order' => $image->order,
                ];

                continue;
            }

            if ($request->hasFile($fileKey)) {
                $image = $this->imageModelClass::create([
                    'order' => $imageData['order'] ?? 0,
                    'alt' => $imageData['alt'] ?? '',
                    'caption' => $imageData['caption'] ?? '',
                ]);

                $this->addImageMedia(
                    image: $image,
                    file: $request->file($fileKey)
                );

                $syncData[$image->id] = [
                    'order' => $image->order,
                ];
            }
        }

        $model->images()->sync($syncData);
    }

    /**
     * Добавить изображение в Spatie.
     *
     * Если imageProcessorEnabled = 1:
     * - файл проходит через ImagePresetService;
     * - сохраняется обработанный webp;
     * - затем добавляется в Spatie.
     *
     * Если imageProcessorEnabled = 0:
     * - файл добавляется напрямую, как раньше.
     */
    protected function addImageMedia(
        Model $image,
        UploadedFile $file
    ): void {
        if (!$this->imageProcessorEnabled()) {
            $image
                ->addMedia($file)
                ->toMediaCollection($this->imageMediaCollection);

            return;
        }

        $processedPath = app(ImagePresetService::class)->storeUploadedImage(
            file: $file,
            presetKey: $this->imagePresetKey(),
            directory: $this->imagePresetDirectory()
        );

        try {
            $image
                ->addMediaFromDisk($processedPath, 'public')
                ->toMediaCollection($this->imageMediaCollection);
        } finally {
            Storage::disk('public')->delete($processedPath);
        }
    }

    /**
     * Включён ли модуль обработки изображений.
     */
    protected function imageProcessorEnabled(): bool
    {
        return app(AdminSettingsService::class)->int(
                'imageProcessorEnabled',
                1
            ) === 1;
    }

    /**
     * Ключ пресета для изображений галереи.
     */
    protected function imagePresetKey(): string
    {
        return property_exists($this, 'imagePresetKey')
            ? $this->imagePresetKey
            : 'rectangle_large';
    }

    /**
     * Директория временного сохранения обработанных изображений.
     */
    protected function imagePresetDirectory(): string
    {
        return property_exists($this, 'imagePresetDirectory')
            ? $this->imagePresetDirectory
            : 'market/images';
    }

    /**
     * Полное удаление изображений:
     * - файл Spatie;
     * - запись изображения.
     */
    protected function deleteImages(array $imageIds): void
    {
        if (empty($imageIds)) {
            return;
        }

        $images = $this->imageModelClass::whereIn('id', $imageIds)->get();

        foreach ($images as $image) {
            $image->clearMediaCollection($this->imageMediaCollection);
            $image->delete();
        }
    }
}

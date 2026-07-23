<?php

namespace App\Traits\Admin\Market;

use App\Services\Admin\AdminFeatureService;
use App\Services\Admin\ImagePresetService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

trait HasMarketImagesTrait
{
    /**
     * Получить класс модели изображения,
     * если он задан в дочернем контроллере.
     */
    protected function getImageModelClass(): ?string
    {
        return property_exists($this, 'imageModelClass')
            ? $this->imageModelClass
            : null;
    }

    /**
     * Получить название media collection.
     */
    protected function getImageMediaCollection(): string
    {
        return property_exists($this, 'imageMediaCollection')
            ? $this->imageMediaCollection
            : 'images';
    }

    /**
     * Получить ключ пресета изображений.
     */
    protected function imagePresetKey(): string
    {
        return property_exists($this, 'imagePresetKey')
            ? $this->imagePresetKey
            : 'rectangle_large';
    }

    /**
     * Получить директорию обработанных изображений.
     */
    protected function imagePresetDirectory(): string
    {
        return property_exists($this, 'imagePresetDirectory')
            ? $this->imagePresetDirectory
            : 'market/images';
    }

    /**
     * Определить, включён ли процессор изображений.
     */
    protected function imageProcessorEnabled(): bool
    {
        return app(AdminFeatureService::class)->imageProcessorEnabled();
    }

    /**
     * Получить данные пресета для фронтового редактора.
     */
    protected function imagePresetPayload(): ?array
    {
        if (!$this->imageProcessorEnabled()) {
            return null;
        }

        try {
            return app(ImagePresetService::class)->editorPayload(
                $this->imagePresetKey()
            );
        } catch (Throwable $e) {
            Log::error('Ошибка загрузки пресета изображений.', [
                'controller' => static::class,
                'preset_key' => $this->imagePresetKey(),
                'exception' => $e,
            ]);

            return null;
        }
    }

    /**
     * Синхронизация изображений:
     * - создание;
     * - обновление;
     * - удаление;
     * - порядок;
     * - обработка через ImagePresetService.
     */
    protected function syncImages(
        Model $model,
        Request $request,
        array $imagesData,
        array $deletedImageIds = []
    ): void {
        $imageModelClass = $this->getImageModelClass();

        if (!$imageModelClass || !method_exists($model, 'images')) {
            return;
        }

        $imageTable = (new $imageModelClass)->getTable();

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
            if (!is_array($imageData)) {
                continue;
            }

            $fileKey = "images.{$index}.file";

            if (!empty($imageData['id'])) {
                $image = $imageModelClass::query()->find(
                    (int) $imageData['id']
                );

                if (
                    !$image
                    || in_array((int) $image->id, $deletedImageIds, true)
                ) {
                    continue;
                }

                $image->update([
                    'order' => (int) ($imageData['order'] ?? $image->order),
                    'alt' => $imageData['alt'] ?? $image->alt,
                    'caption' => $imageData['caption'] ?? $image->caption,
                ]);

                if ($request->hasFile($fileKey)) {
                    $image->clearMediaCollection(
                        $this->getImageMediaCollection()
                    );

                    $this->addImageMedia(
                        image: $image,
                        file: $request->file($fileKey)
                    );
                }

                $syncData[$image->id] = [
                    'order' => (int) $image->order,
                ];

                continue;
            }

            if (!$request->hasFile($fileKey)) {
                continue;
            }

            $image = $imageModelClass::query()->create([
                'order' => (int) ($imageData['order'] ?? $index),
                'alt' => $imageData['alt'] ?? null,
                'caption' => $imageData['caption'] ?? null,
            ]);

            $this->addImageMedia(
                image: $image,
                file: $request->file($fileKey)
            );

            $syncData[$image->id] = [
                'order' => (int) $image->order,
            ];
        }

        $model->images()->sync($syncData);
    }

    /**
     * Добавить изображение в Spatie MediaLibrary.
     */
    protected function addImageMedia(
        Model $image,
        UploadedFile $file
    ): void {
        $mediaCollection = $this->getImageMediaCollection();

        if (
            !$this->imageProcessorEnabled()
            || $file->getMimeType() === 'image/webp'
        ) {
            $image
                ->addMedia($file)
                ->toMediaCollection($mediaCollection);

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
                ->toMediaCollection($mediaCollection);
        } finally {
            Storage::disk('public')->delete($processedPath);
        }
    }

    /**
     * Полное удаление изображений:
     * - связи должны быть удалены вызывающим кодом;
     * - удаляется файл MediaLibrary;
     * - удаляется запись изображения.
     */
    protected function deleteImages(array $imageIds): void
    {
        $imageModelClass = $this->getImageModelClass();

        if (!$imageModelClass || empty($imageIds)) {
            return;
        }

        $imageIds = array_values(
            array_unique(
                array_map('intval', $imageIds)
            )
        );

        $images = $imageModelClass::query()
            ->whereIn('id', $imageIds)
            ->get();

        foreach ($images as $image) {
            $image->clearMediaCollection(
                $this->getImageMediaCollection()
            );

            $image->delete();
        }
    }
}

<?php

namespace App\Http\Controllers\Admin\Blog\Base\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

trait HasBlogImagesTrait
{
    /**
     * Синхронизация изображений (создание, обновление, удаление, порядок)
     */
    protected function syncImages(Model $model, Request $request, array $imagesData, array $deletedImageIds = []): void
    {
        // Удаляем отмеченные изображения
        if (!empty($deletedImageIds)) {
            $model->images()->detach($deletedImageIds);
            $this->deleteImages($deletedImageIds);
        }

        $syncData = [];

        foreach ($imagesData as $index => $imageData) {
            $fileKey = "images.{$index}.file";

            // Обновление существующего изображения
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

                // Обновление файла
                if ($request->hasFile($fileKey)) {
                    $image->clearMediaCollection($this->imageMediaCollection);
                    $image->addMedia($request->file($fileKey))
                        ->toMediaCollection($this->imageMediaCollection);
                }

                $syncData[$image->id] = ['order' => $image->order];
                continue;
            }

            // Создание нового изображения
            if ($request->hasFile($fileKey)) {
                $image = $this->imageModelClass::create([
                    'order' => $imageData['order'] ?? 0,
                    'alt' => $imageData['alt'] ?? '',
                    'caption' => $imageData['caption'] ?? '',
                ]);

                $image->addMedia($request->file($fileKey))
                    ->toMediaCollection($this->imageMediaCollection);

                $syncData[$image->id] = ['order' => $image->order];
            }
        }

        // Получаем существующие изображения (кроме удалённых)
        $imageTable = (new $this->imageModelClass)->getTable();

        $existingIds = $model->images()
            ->whereNotIn("{$imageTable}.id", $deletedImageIds)
            ->pluck("{$imageTable}.id")
            ->toArray();

        // Добавляем отсутствующие в sync (чтобы не потерять их)
        foreach ($existingIds as $existingId) {
            if (!array_key_exists($existingId, $syncData)) {
                $existingImage = $this->imageModelClass::find($existingId);

                if ($existingImage) {
                    $syncData[$existingId] = ['order' => $existingImage->order];
                }
            }
        }

        // Синхронизация pivot-таблицы
        $model->images()->sync($syncData);
    }

    /**
     * Полное удаление изображений (файл + запись)
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

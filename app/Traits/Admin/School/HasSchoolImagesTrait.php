<?php

namespace App\Traits\Admin\School;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

trait HasSchoolImagesTrait
{
    /**
     * Получить класс модели изображения, если он задан в дочернем контроллере.
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
     * Синхронизация изображений: создание, обновление, удаление и порядок.
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
            $fileKey = "images.{$index}.file";

            if (!empty($imageData['id'])) {
                $image = $imageModelClass::find($imageData['id']);

                if (!$image || in_array((int) $image->id, $deletedImageIds, true)) {
                    continue;
                }

                $image->update([
                    'order' => $imageData['order'] ?? $image->order,
                    'alt' => $imageData['alt'] ?? $image->alt,
                    'caption' => $imageData['caption'] ?? $image->caption,
                ]);

                if ($request->hasFile($fileKey)) {
                    $image->clearMediaCollection(
                        $this->getImageMediaCollection()
                    );

                    $image
                        ->addMedia($request->file($fileKey))
                        ->toMediaCollection(
                            $this->getImageMediaCollection()
                        );
                }

                $syncData[$image->id] = [
                    'order' => $image->order,
                ];

                continue;
            }

            if ($request->hasFile($fileKey)) {
                $image = $imageModelClass::create([
                    'order' => $imageData['order'] ?? 0,
                    'alt' => $imageData['alt'] ?? '',
                    'caption' => $imageData['caption'] ?? '',
                ]);

                $image
                    ->addMedia($request->file($fileKey))
                    ->toMediaCollection(
                        $this->getImageMediaCollection()
                    );

                $syncData[$image->id] = [
                    'order' => $image->order,
                ];
            }
        }

        $model->images()->sync($syncData);
    }

    /**
     * Полное удаление изображений: media + запись изображения.
     */
    protected function deleteImages(array $imageIds): void
    {
        $imageModelClass = $this->getImageModelClass();

        if (!$imageModelClass || empty($imageIds)) {
            return;
        }

        $images = $imageModelClass::whereIn('id', $imageIds)->get();

        foreach ($images as $image) {
            $image->clearMediaCollection($this->getImageMediaCollection());
            $image->delete();
        }
    }
}

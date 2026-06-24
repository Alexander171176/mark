<?php

namespace App\Traits\Admin\Blog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

trait HasBlogImagesTrait
{
    /**
     * Синхронизация изображений (создание, обновление, удаление, порядок)
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

                    $image
                        ->addMedia($request->file($fileKey))
                        ->toMediaCollection($this->imageMediaCollection);
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

                $image
                    ->addMedia($request->file($fileKey))
                    ->toMediaCollection($this->imageMediaCollection);

                $syncData[$image->id] = [
                    'order' => $image->order,
                ];
            }
        }

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

<?php

namespace App\Http\Resources\Admin\School\LearningCategory;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class LearningCategorySharedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Получаем первое изображение (если оно было загружено с with('images'))
        // или null, если не было загружено
        $firstImage = $this->whenLoaded('images', fn() => $this->resource->images->first());

        // Проверяем, не является ли $firstImage объектом MissingValue
        $thumbnailUrl = !($firstImage instanceof MissingValue) && $firstImage
            ? $firstImage->thumb_url // Получаем URL, если images загружены и не пусты
            : null;

        // Получаем экземпляр модели
        $learningCategory = $this->resource;

        // Возвращаем только самые необходимые данные для идентификации и отображения в списке
        return [
            'id' => $learningCategory->id,
            'parent_id' => $learningCategory->parent_id,
            'sort' => $learningCategory->sort,
            'activity' => $learningCategory->activity,
            'locale' => $learningCategory->locale,
            'name' => $learningCategory->name,
            'slug' => $learningCategory->slug,
            'children' => LearningCategoryResource::collection($this->whenLoaded('children')),
            // Можно добавить URL первого изображения (превью)
            'thumbnail_url' => $thumbnailUrl, // <--- Используем результат проверки
        ];
    }
}

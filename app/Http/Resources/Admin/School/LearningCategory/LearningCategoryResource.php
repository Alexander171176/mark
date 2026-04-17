<?php

namespace App\Http\Resources\Admin\School\LearningCategory;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LearningCategoryResource extends JsonResource
{
    /**
     * Представление категории.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // Базовые поля
            'id'          => $this->id,
            'parent_id'   => $this->parent_id,
            'locale'      => $this->locale,
            'name'        => $this->name,
            'slug'        => $this->slug,
            'short'       => $this->short,
            'description' => $this->description,
            'activity'    => (bool) $this->activity,
            'sort'        => (int) $this->sort,
            'views'       => (int) ($this->views ?? 0),

            // SEO
            'meta_title'   => $this->meta_title,
            'meta_keywords'=> $this->meta_keywords,
            'meta_desc'    => $this->meta_desc,

            // Даты
            'created_at'  => optional($this->created_at)?->isoFormat('DD.MM.YYYY HH:mm:ss'),
            'updated_at'  => optional($this->updated_at)?->isoFormat('DD.MM.YYYY HH:mm:ss'),

            // Счётчики (если были withCount)
            'children_count' => $this->when(isset($this->children_count), (int) $this->children_count),
            'courses_count'  => $this->when(isset($this->courses_count), (int) $this->courses_count),

            // Короткая инфа о родителе (если parent загружен)
            'parent' => $this->whenLoaded('parent', function () {
                return $this->parent
                    ? [
                        'id'   => $this->parent->id,
                        'name' => $this->parent->name,
                        'slug' => $this->parent->slug,
                    ]
                    : null;
            }),

            'likes_count'    => $this->whenCounted('likes', $this->likes_count), // fallback

            // Изображения (если загружены)
            'images' => LearningCategoryImageResource::collection($this->whenLoaded('images')),
            'images_count' => $this->whenCounted('images'),

            // Дети (если загружены — отдаём тем же ресурсом)
            'children' => $this->whenLoaded('children', function () {
                return self::collection($this->children)->resolve();
            }),
        ];
    }
}

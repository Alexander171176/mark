<?php

namespace App\Http\Resources\Admin\School\Module;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModuleResource extends JsonResource
{
    /**
     * Представление модуля курса (админ/витрина).
     *
     * @return array<string,mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'id'        => $this->id,
            'course_id' => $this->course_id,

            // Локаль
            'locale'    => $this->locale,

            // Основные поля модуля
            'title'       => $this->title,
            'slug'        => $this->slug,
            'subtitle'    => $this->subtitle,
            'short'       => $this->short,
            'description' => $this->description,

            // SEO
            'meta_title'    => $this->meta_title,
            'meta_keywords' => $this->meta_keywords,
            'meta_desc'     => $this->meta_desc,

            // Публикация / видимость
            'status'       => $this->status,          // draft|published|archived
            'availability' => $this->availability,    // unlisted|public|private
            'published_at' => $this->published_at?->format('Y-m-d'),

            // Сложность / длительность
            'difficulty'        => $this->difficulty,
            'duration'          => $this->duration,

            // Метрики
            'popularity'    => (int) $this->popularity,
            'rating_count'  => (int) $this->rating_count,
            'rating_avg'    => (float) $this->rating_avg,
            'views'         => (int) $this->views,
            'likes'         => (int) $this->likes,
            'likes_count'   => (int) ($this->likes_count ?? 0),

            // Управление списком
            'activity' => (bool) $this->activity,
            'sort'     => (int) $this->sort,

            // Таймстампы
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'deleted_at' => $this->deleted_at?->toISOString(),

            // Родительский курс (кратко)
            'course' => $this->whenLoaded('course', function () {
                return [
                    'id'    => $this->course->id,
                    'title' => $this->course->title,
                    'slug'  => $this->course->slug,
                ];
            }),

            // Уроки модуля (если подгружены)
            'lessons' => $this->whenLoaded('lessons', fn () =>
            $this->lessons->map(fn ($l) => [
                'id'            => $l->id,
                'module_id'     => $l->module_id,
                'locale'        => $l->locale,

                'title'         => $l->title,
                'slug'          => $l->slug,
                'subtitle'      => $l->subtitle ?? null,
                'short'         => $l->short ?? null,
                'description'   => $l->description ?? null,

                'sort'          => (int) $l->sort,
                'activity'      => (bool) $l->activity,

                'status'        => $l->status,
                'availability'  => $l->availability,
                'published_at'  => $l->published_at?->format('Y-m-d'),

                'access_type'   => $l->access_type,
                'difficulty'    => $l->difficulty,
                'duration'      => $l->duration,

                'preview_mode'  => $l->preview_mode,
                'preview_value' => $l->preview_value,

                'popularity'    => (int) ($l->popularity ?? 0),
                'rating_count'  => (int) ($l->rating_count ?? 0),
                'rating_avg'    => (float) ($l->rating_avg ?? 0),
                'views'         => (int) ($l->views ?? 0),
                'likes'         => (int) ($l->likes ?? 0),

                'images' => $l->relationLoaded('images')
                    ? $l->images->map(fn ($img) => [
                        'id'        => $img->id,
                        'order'     => $img->pivot->order ?? 0,
                        'alt'       => $img->alt ?? null,
                        'caption'   => $img->caption ?? null,
                        'url'       => $img->url ?? null,
                        'webp_url'  => $img->webp_url ?? null,
                        'image_url' => $img->image_url ?? null,
                        'thumb_url' => $img->thumb_url ?? null,
                    ])->values()
                    : [],
            ])
            ),

            // Счётчик из withCount('lessons'), если будет использоваться
            'lessons_count' => $this->when(
                isset($this->lessons_count),
                (int) $this->lessons_count,
                0
            ),

            // Изображения модуля (через ModuleImageResource)
            'images' => $this->whenLoaded('images', fn () =>
            ModuleImageResource::collection($this->images)
            ),
        ];
    }
}

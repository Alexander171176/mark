<?php

namespace App\Http\Resources\Admin\School\SchoolHashtag;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolHashtagResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'sort' => $this->sort,
            'activity' => (bool) $this->activity,

            'slug' => $this->slug,
            'color' => $this->color,

            // 🔥 Главный перевод (как в блоге)
            'name'        => $this->translation?->name,
            'short'       => $this->translation?->short,
            'description' => $this->translation?->description,

            'meta_title'    => $this->translation?->meta_title,
            'meta_keywords' => $this->translation?->meta_keywords,
            'meta_desc'     => $this->translation?->meta_desc,

            // 🔥 Полный список переводов (если нужен)
            'translations' => SchoolHashtagTranslationResource::collection(
                $this->whenLoaded('translations')
            ),

            // 📊 Статистика
            'views' => (int) $this->views,
            'likes' => (int) $this->likes,

            // 🔗 Counts (если есть withCount)
            'courses_count' => $this->when(
                isset($this->courses_count),
                fn () => (int) $this->courses_count
            ),

            'modules_count' => $this->when(
                isset($this->modules_count),
                fn () => (int) $this->modules_count
            ),

            'lessons_count' => $this->when(
                isset($this->lessons_count),
                fn () => (int) $this->lessons_count
            ),

            // ⏱ Таймстемпы
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),
        ];
    }
}

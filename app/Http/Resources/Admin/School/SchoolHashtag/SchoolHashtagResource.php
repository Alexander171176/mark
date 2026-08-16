<?php

namespace App\Http\Resources\Admin\School\SchoolHashtag;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolHashtagResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /**
         * Первый из фактически загруженных переводов.
         *
         * В публичной части обычно загружается
         * только текущая локаль.
         *
         * В административной части могут быть
         * загружены все переводы.
         */
        $translation = $this->relationLoaded('translations')
            ? $this->translations->first()
            : null;

        return [
            'id' => $this->id,

            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,

            'slug' => $this->slug,
            'color' => $this->color,

            /**
             * Основной перевод.
             */
            'name' => $translation?->name,
            'short' => $translation?->short,
            'description' => $translation?->description,

            'meta_title' => $translation?->meta_title,
            'meta_keywords' => $translation?->meta_keywords,
            'meta_desc' => $translation?->meta_desc,

            /**
             * Все фактически загруженные переводы.
             *
             * Admin:
             * ru / en / kk / zh ...
             *
             * Public:
             * обычно только текущая локаль.
             */
            'translations' => SchoolHashtagTranslationResource::collection(
                $this->whenLoaded('translations')
            ),

            'views' => (int) $this->views,
            'likes' => (int) $this->likes,

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

            'created_at' => optional(
                $this->created_at
            )->toIso8601String(),

            'updated_at' => optional(
                $this->updated_at
            )->toIso8601String(),
        ];
    }
}

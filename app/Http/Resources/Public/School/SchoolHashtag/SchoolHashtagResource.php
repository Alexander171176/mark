<?php

namespace App\Http\Resources\Public\School\SchoolHashtag;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolHashtagResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();
        $fallbackLocale = config('app.fallback_locale', 'ru');

        /**
         * Переводы current + fallback заранее загружены
         * через SchoolHashtag::forPublic().
         *
         * Дополнительных SQL-запросов здесь нет.
         */
        $translation = $this->translationOrFallback(
            $locale,
            $fallbackLocale
        );

        return [
            'id' => (int) $this->id,
            'slug' => $this->slug,
            'color' => $this->color,
            'views' => (int) $this->views,
            'likes' => (int) $this->likes,

            /**
             * Полный публичный перевод для Show.
             */
            'translation' => $translation
                ? [
                    'locale' => $translation->locale,
                    'name' => $translation->name,
                    'short' => $translation->short,
                    'description' => $translation->description,
                    'meta_title' => $translation->meta_title,
                    'meta_keywords' => $translation->meta_keywords,
                    'meta_desc' => $translation->meta_desc,
                ]
                : null,

            /**
             * Публичные counts.
             *
             * Resource ничего не считает самостоятельно,
             * а только отдаёт заранее подготовленные Controller counts.
             */
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
        ];
    }
}

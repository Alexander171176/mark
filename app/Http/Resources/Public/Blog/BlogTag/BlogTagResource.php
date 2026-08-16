<?php

namespace App\Http\Resources\Public\Blog\BlogTag;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogTagResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        /**
         * Public получает только:
         *
         * - текущую локаль;
         * - fallback ru.
         */
        $translation = $this->relationLoaded('translations')
            ? (
            $this->translations->firstWhere(
                'locale',
                $locale
            )
                ?: $this->translations->firstWhere(
                'locale',
                $fallbackLocale
            )
                ?: $this->translations->first()
            )
            : null;

        return [
            'id' => $this->id,

            /**
             * Основные публичные поля.
             */
            'sort' => (int) $this->sort,
            'slug' => $this->slug,
            'icon' => $this->icon,
            'views' => (int) $this->views,

            /**
             * Полный публичный перевод.
             */
            'translation' => $translation
                ? [
                    'locale' => $translation->locale,

                    'name' => $translation->name,
                    'subtitle' => $translation->subtitle,
                    'short' => $translation->short,
                    'description' => $translation->description,

                    'meta_title' => $translation->meta_title,
                    'meta_keywords' => $translation->meta_keywords,
                    'meta_desc' => $translation->meta_desc,
                ]
                : null,

            /**
             * Количество связанных статей.
             */
            'articles_count' => $this->when(
                isset($this->articles_count),
                fn () => (int) $this->articles_count
            ),

            /**
             * Даты.
             */
            'created_at' =>
                $this->created_at?->toISOString(),

            'updated_at' =>
                $this->updated_at?->toISOString(),
        ];
    }
}

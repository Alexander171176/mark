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
         * Public Controller заранее загружает
         * только текущую локаль + fallback.
         *
         * Сам fallback централизован
         * в модели BlogTag.
         */
        $translation = $this->relationLoaded(
            'translations'
        )
            ? $this->translationOrFallback(
                $locale,
                $fallbackLocale
            )
            : null;

        return [
            'id' => $this->id,

            /**
             * Основные публичные поля.
             */
            'slug' => $this->slug,
            'icon' => $this->icon,
            'views' => (int) $this->views,

            /**
             * Полный Public-перевод
             * для страницы Show + SEO.
             */
            'translation' => $translation
                ? [
                    'locale' =>
                        $translation->locale,

                    'name' =>
                        $translation->name,

                    'subtitle' =>
                        $translation->subtitle,

                    'short' =>
                        $translation->short,

                    'description' =>
                        $translation->description,

                    'meta_title' =>
                        $translation->meta_title,

                    'meta_keywords' =>
                        $translation->meta_keywords,

                    'meta_desc' =>
                        $translation->meta_desc,
                ]
                : null,
        ];
    }
}

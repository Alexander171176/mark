<?php

namespace App\Http\Resources\Public\Blog\BlogTag;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogTagSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        /**
         * Public Controller / sidebar query
         * должны заранее загрузить
         * current locale + fallback.
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

            'slug' => $this->slug,
            'icon' => $this->icon,

            /**
             * Пока оставляем.
             *
             * Public BlogArticle Show
             * использует activity
             * при работе со связанными тегами.
             */
            'activity' =>
                (bool) $this->activity,

            /**
             * SharedResource нужен
             * только компактный перевод.
             */
            'translation' => $translation
                ? [
                    'locale' =>
                        $translation->locale,

                    'name' =>
                        $translation->name,
                ]
                : null,
        ];
    }
}

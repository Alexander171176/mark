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

            'slug' => $this->slug,
            'icon' => $this->icon,

            /**
             * Activity оставляем:
             * Public BlogArticle Show сейчас
             * фильтрует tag.activity.
             */
            'activity' => (bool) $this->activity,

            'translation' => $translation
                ? [
                    'locale' => $translation->locale,
                    'name' => $translation->name,
                ]
                : null,
        ];
    }
}

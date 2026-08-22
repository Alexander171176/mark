<?php

namespace App\Http\Resources\Public\School\SchoolHashtag;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolHashtagSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        $translation =
            $this->relationLoaded('translations')
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
            'id' =>
                (int) $this->id,

            'slug' =>
                $this->slug,

            'color' =>
                $this->color,

            'views' =>
                (int) $this->views,

            'likes' =>
                (int) $this->likes,

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

<?php

namespace App\Http\Resources\Public\School\SchoolHashtag;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolHashtagSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();
        $fallbackLocale = config('app.fallback_locale', 'ru');

        $translation = $this->translationOrFallback($locale, $fallbackLocale);

        return [
            'id' => (int) $this->id,
            'slug' => $this->slug,

            'translation' => $translation
                ? [
                    'locale' => $translation->locale,
                    'name' => $translation->name,
                ]
                : null,
        ];
    }
}

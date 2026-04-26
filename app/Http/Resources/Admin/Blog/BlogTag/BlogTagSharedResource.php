<?php

namespace App\Http\Resources\Admin\Blog\BlogTag;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogTagSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $currentLocale = app()->getLocale();

        $translation = $this->whenLoaded('translations', function () use ($currentLocale) {
            return $this->translations->firstWhere('locale', $currentLocale)
                ?: $this->translations->firstWhere('locale', config('app.fallback_locale', 'ru'))
                    ?: $this->translations->first();
        });

        return [
            'id' => $this->id,
            'locale' => $translation?->locale,
            'name' => $translation?->name,
            'slug' => $this->slug,
            'activity' => (bool) $this->activity,
            'sort' => (int) $this->sort,
            'icon' => $this->icon,
        ];
    }
}

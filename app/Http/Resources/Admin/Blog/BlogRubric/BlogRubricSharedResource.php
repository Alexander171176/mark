<?php

namespace App\Http\Resources\Admin\Blog\BlogRubric;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogRubricSharedResource extends JsonResource
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
            'user_id' => $this->user_id,

            'parent_id' => $this->parent_id,
            'level' => $this->level,

            'in_menu' => (bool) $this->in_menu,
            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,

            'url' => $this->url,
            'icon' => $this->icon,

            /**
             * Только нужные translated поля для дерева
             */
            'locale' => $translation?->locale,
            'title' => $translation?->title,
            'subtitle' => $translation?->subtitle,
            'short' => $translation?->short,

            /**
             * Рекурсивные дети
             */
            'children' => BlogRubricSharedResource::collection(
                $this->whenLoaded('children')
            ),
        ];
    }
}

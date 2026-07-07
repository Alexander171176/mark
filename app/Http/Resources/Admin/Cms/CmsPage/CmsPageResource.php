<?php

namespace App\Http\Resources\Admin\Cms\CmsPage;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CmsPageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $currentLocale = app()->getLocale();

        $currentTranslation = $this->whenLoaded('translations', function () use ($currentLocale) {
            return $this->translations->firstWhere('locale', $currentLocale)
                ?: $this->translations->firstWhere('locale', config('app.fallback_locale', 'ru'))
                    ?: $this->translations->first();
        });

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,

            /** Дерево */
            'parent_id' => $this->parent_id,
            'level' => (int) $this->level,
            'is_root' => $this->parent_id === null,

            /** Основные данные */
            'url' => $this->url,
            'icon' => $this->icon,

            /** Отображение */
            'in_menu' => (bool) $this->in_menu,
            'in_footer' => (bool) $this->in_footer,
            'show_content' => (bool) $this->show_content,
            'show_seo' => (bool) $this->show_seo,

            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,

            /** Публикация */
            'status' => $this->status,

            /** Окно показа */
            'published_at' => $this->published_at?->format('Y-m-d'),
            'show_from_at' => $this->show_from_at?->format('Y-m-d\TH:i'),
            'show_to_at' => $this->show_to_at?->format('Y-m-d\TH:i'),

            /** Счётчики */
            'views' => (int) $this->views,

            /** Текущий перевод */
            'translation' => $currentTranslation
                ? new CmsPageTranslationResource($currentTranslation)
                : null,

            /** Все переводы */
            'translations' => CmsPageTranslationResource::collection(
                $this->whenLoaded('translations')
            ),

            /** Relations */
            'parent' => new CmsPageSharedResource(
                $this->whenLoaded('parent')
            ),

            'children' => CmsPageSharedResource::collection(
                $this->whenLoaded('children')
            ),

            'children_recursive' => CmsPageSharedResource::collection(
                $this->whenLoaded('childrenRecursive')
            ),

            'owner' => $this->whenLoaded('owner', function () {
                return [
                    'id' => $this->owner?->id,
                    'name' => $this->owner?->name,
                    'email' => $this->owner?->email,
                    'profile_photo_url' => $this->owner?->profile_photo_url,
                ];
            }),

            /** Counts */
            'children_count' => $this->whenCounted('children'),

            /** Timestamps */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}

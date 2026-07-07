<?php

namespace App\Http\Resources\Admin\Cms\CmsPage;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CmsPageSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $currentLocale = app()->getLocale();

        $translation = $this->whenLoaded('translations', function () use ($currentLocale) {
            return $this->translations->firstWhere('locale', $currentLocale)
                ?: $this->translations->firstWhere(
                    'locale',
                    config('app.fallback_locale', 'ru')
                )
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

            /** Перевод для дерева */
            'locale' => $translation?->locale,
            'title' => $translation?->title,
            'subtitle' => $translation?->subtitle,
            'short' => $translation?->short,

            /** Дети */
            'children' => self::collection(
                $this->whenLoaded('children')
            ),

            /** Counts */
            'children_count' => $this->when(
                isset($this->children_count),
                fn () => (int) $this->children_count
            ),

            /** Timestamps */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}

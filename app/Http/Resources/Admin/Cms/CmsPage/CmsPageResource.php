<?php

namespace App\Http\Resources\Admin\Cms\CmsPage;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CmsPageResource extends JsonResource
{
    /**
     * Полный ресурс CMS страницы.
     *
     * Используется преимущественно для Edit / Details.
     * Ресурс не выполняет дополнительные SQL-запросы.
     *
     * Controller должен заранее загрузить:
     * - translations — все переводы страницы;
     * - parent.translations — при необходимости;
     * - owner;
     * - children / childrenRecursive — при необходимости.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $translation = $this->currentTranslation();

        return [
            /** Основные идентификаторы */
            'id' => (int) $this->id,
            'user_id' => $this->user_id !== null
                ? (int) $this->user_id
                : null,

            /** Дерево */
            'parent_id' => $this->parent_id !== null
                ? (int) $this->parent_id
                : null,

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

            /** Сортировка и активность */
            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,

            /** Публикация */
            'status' => $this->status,

            'published_at' => $this->published_at
                ? $this->published_at->format('Y-m-d')
                : null,

            'show_from_at' => $this->show_from_at
                ? $this->show_from_at->format('Y-m-d\TH:i')
                : null,

            'show_to_at' => $this->show_to_at
                ? $this->show_to_at->format('Y-m-d\TH:i')
                : null,

            /** Счётчики */
            'views' => (int) $this->views,

            'children_count' => $this->whenCounted(
                'children'
            ),

            /** Текущий перевод */
            'translation' => $translation
                ? new CmsPageTranslationResource(
                    $translation
                )
                : null,

            /**
             * Все переводы.
             *
             * Для Edit Controller загружает полную
             * коллекцию translations.
             */
            'translations' =>
                CmsPageTranslationResource::collection(
                    $this->whenLoaded(
                        'translations'
                    )
                ),

            /** Родитель */
            'parent' => $this->whenLoaded(
                'parent',
                fn () => $this->parent
                    ? new CmsPageSharedResource(
                        $this->parent
                    )
                    : null
            ),

            /** Прямые дочерние страницы */
            'children' =>
                CmsPageSharedResource::collection(
                    $this->whenLoaded(
                        'children'
                    )
                ),

            /** Рекурсивное дерево */
            'children_recursive' =>
                CmsPageSharedResource::collection(
                    $this->whenLoaded(
                        'childrenRecursive'
                    )
                ),

            /** Владелец */
            'owner' => $this->whenLoaded(
                'owner',
                fn () => $this->ownerPayload()
            ),

            /** Даты */
            'created_at' =>
                $this->created_at?->toISOString(),

            'updated_at' =>
                $this->updated_at?->toISOString(),
        ];
    }

    /**
     * Текущий перевод из уже загруженной
     * коллекции translations.
     *
     * Для полного ресурса translations обычно
     * содержит все локали.
     *
     * Метод не инициирует SQL.
     */
    protected function currentTranslation(): ?object
    {
        if (! $this->relationLoaded('translations')) {
            return null;
        }

        $locale = app()->getLocale();

        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        return $this->translations
            ->firstWhere(
                'locale',
                $locale
            )
            ?? $this->translations
            ->firstWhere(
                'locale',
                $fallbackLocale
            )
            ?? $this->translations
                ->first();
    }

    /**
     * Компактные данные владельца.
     *
     * @return array<string, mixed>|null
     */
    protected function ownerPayload(): ?array
    {
        if (! $this->owner) {
            return null;
        }

        return [
            'id' => (int) $this->owner->id,
            'name' => $this->owner->name,
            'email' => $this->owner->email,

            'profile_photo_url' =>
                $this->owner->profile_photo_url,
        ];
    }
}

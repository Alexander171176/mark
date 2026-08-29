<?php

namespace App\Http\Resources\Admin\Cms\CmsPage;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CmsPageSharedResource extends JsonResource
{
    /**
     * Компактный ресурс CMS страницы.
     *
     * Используется для:
     * - Index;
     * - дерева;
     * - карточек;
     * - выбора родительской страницы;
     * - связанных CMS страниц.
     *
     * Controller должен заранее загрузить
     * translations только для currentLocale.
     *
     * Ресурс не выполняет SQL-запросов.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $translation = $this->loadedTranslation();

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

            /**
             * Текущий перевод.
             *
             * На Index translations должен содержать
             * только currentLocale.
             */
            'translation' => $translation
                ? new CmsPageTranslationResource(
                    $translation
                )
                : null,

            /** Родитель */
            'parent' => $this->whenLoaded(
                'parent',
                fn () => $this->parent
                    ? new self(
                        $this->parent
                    )
                    : null
            ),

            /** Дочерние страницы */
            'children' => self::collection(
                $this->whenLoaded(
                    'children'
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
     * Получить уже загруженный перевод.
     *
     * Controller загружает translations только
     * для currentLocale, поэтому первый элемент
     * коллекции и является нужным переводом.
     *
     * Метод не инициирует SQL.
     */
    protected function loadedTranslation(): ?object
    {
        if (! $this->relationLoaded('translations')) {
            return null;
        }

        return $this->translations->first();
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

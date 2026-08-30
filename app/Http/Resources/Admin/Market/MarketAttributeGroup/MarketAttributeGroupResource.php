<?php

namespace App\Http\Resources\Admin\Market\MarketAttributeGroup;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketAttributeGroupResource extends JsonResource
{
    /**
     * Полный ресурс группы характеристик.
     *
     * Используется преимущественно
     * для Edit / Details.
     *
     * Controller должен заранее загрузить:
     * - translations — все переводы группы;
     * - owner;
     * - moderator;
     * - attributes_count при необходимости.
     *
     * Ресурс не выполняет SQL-запросов.
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

            /** Основные данные группы */
            'code' => $this->code,
            'icon' => $this->icon,
            'color' => $this->color,

            /** Отображение */
            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,

            /** Публикация */
            'status' => $this->status,

            /** Модерация */
            'moderation_status' => (int) $this->moderation_status,

            'is_approved' =>
                (int) $this->moderation_status === 1,

            'moderated_by' => $this->moderated_by !== null
                ? (int) $this->moderated_by
                : null,

            'moderated_at' =>
                $this->moderated_at?->toISOString(),

            'moderation_note' =>
                $this->moderation_note,

            /** Период публикации */
            'published_at' =>
                $this->published_at?->format('Y-m-d'),

            'show_from_at' =>
                $this->show_from_at?->format('Y-m-d\TH:i'),

            'show_to_at' =>
                $this->show_to_at?->format('Y-m-d\TH:i'),

            /** Текущий перевод */
            'translation' => $translation
                ? new MarketAttributeGroupTranslationResource(
                    $translation
                )
                : null,

            /**
             * Все переводы.
             *
             * Edit Controller загружает
             * полную коллекцию translations.
             */
            'translations' =>
                MarketAttributeGroupTranslationResource::collection(
                    $this->whenLoaded(
                        'translations'
                    )
                ),

            /** Количество характеристик */
            'attributes_count' =>
                $this->whenCounted(
                    'attributes'
                ),

            /** Владелец */
            'owner' => $this->whenLoaded(
                'owner',
                fn () => $this->owner
                    ? [
                        'id' => (int) $this->owner->id,
                        'name' => $this->owner->name,
                        'email' => $this->owner->email,

                        'profile_photo_url' =>
                            $this->owner->profile_photo_url,
                    ]
                    : null
            ),

            /** Модератор */
            'moderator' => $this->whenLoaded(
                'moderator',
                fn () => $this->moderator
                    ? [
                        'id' => (int) $this->moderator->id,
                        'name' => $this->moderator->name,
                    ]
                    : null
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
     * полной коллекции translations.
     *
     * Метод не выполняет SQL.
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
            ?? $this->translations->first();
    }
}

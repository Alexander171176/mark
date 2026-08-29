<?php

namespace App\Http\Resources\Admin\Market\MarketCompany;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketCompanySharedResource extends JsonResource
{
    /**
     * Компактный ресурс компании.
     *
     * Используется для Index, таблиц,
     * карточек и связанных списков.
     *
     * Controller должен заранее загрузить:
     * - translations только currentLocale;
     * - owner;
     * - moderator при необходимости.
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

            /** Основные данные компании */
            'url' => $this->url,
            'company_type' => $this->company_type,
            'bin_iin' => $this->bin_iin,
            'legal_name' => $this->legal_name,
            'director_name' => $this->director_name,

            /** Контакты */
            'email' => $this->email,
            'phone' => $this->phone,
            'website' => $this->website,

            /** Файлы */
            'logo' => $this->logo,
            'signature' => $this->signature,
            'stamp' => $this->stamp,

            /** Адрес */
            'country' => $this->country,
            'region' => $this->region,
            'city' => $this->city,
            'legal_address' => $this->legal_address,
            'actual_address' => $this->actual_address,

            'latitude' => $this->latitude,
            'longitude' => $this->longitude,

            /** Банковские данные */
            'bank_name' => $this->bank_name,
            'bank_account' => $this->bank_account,
            'bank_account_secondary' => $this->bank_account_secondary,
            'bank_bik' => $this->bank_bik,
            'bank_iban' => $this->bank_iban,

            /** Налоги */
            'vat_enabled' => (bool) $this->vat_enabled,
            'vat_rate' => $this->vat_rate,

            /** Дополнительные данные */
            'social_links' => $this->social_links,

            /** Отображение */
            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,
            'left' => (bool) $this->left,
            'main' => (bool) $this->main,
            'right' => (bool) $this->right,

            /** Публикация */
            'status' => $this->status,

            /** Модерация */
            'moderation_status' => (int) $this->moderation_status,

            'is_approved' =>
                (int) $this->moderation_status === 1,

            'moderated_by' => $this->moderated_by,

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

            /** Счётчики */
            'views' => (int) $this->views,

            /**
             * Единственный перевод,
             * заранее ограниченный Controller.
             */
            'translation' => $translation
                ? new MarketCompanyTranslationResource(
                    $translation
                )
                : null,

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
     * Получить перевод только из уже
     * загруженной коллекции.
     *
     * Index Controller гарантирует,
     * что translations содержит только
     * currentLocale.
     *
     * Метод не выполняет SQL.
     */
    protected function loadedTranslation(): ?object
    {
        if (! $this->relationLoaded('translations')) {
            return null;
        }

        return $this->translations->first();
    }
}

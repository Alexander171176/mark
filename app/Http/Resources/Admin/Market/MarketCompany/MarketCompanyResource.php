<?php

namespace App\Http\Resources\Admin\Market\MarketCompany;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketCompanyResource extends JsonResource
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

            /**
             * Основные данные компании
             */
            'url' => $this->url,
            'company_type' => $this->company_type,
            'bin_iin' => $this->bin_iin,
            'legal_name' => $this->legal_name,
            'director_name' => $this->director_name,

            /**
             * Контакты
             */
            'email' => $this->email,
            'phone' => $this->phone,
            'website' => $this->website,

            /**
             * Файлы компании
             */
            'logo' => $this->logo,
            'signature' => $this->signature,
            'stamp' => $this->stamp,

            /**
             * Адрес и геолокация
             */
            'country' => $this->country,
            'region' => $this->region,
            'city' => $this->city,
            'legal_address' => $this->legal_address,
            'actual_address' => $this->actual_address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,

            /**
             * Банковские данные
             */
            'bank_name' => $this->bank_name,
            'bank_account' => $this->bank_account,
            'bank_account_secondary' => $this->bank_account_secondary,
            'bank_bik' => $this->bank_bik,
            'bank_iban' => $this->bank_iban,

            /**
             * Налоги
             */
            'vat_enabled' => (bool) $this->vat_enabled,
            'vat_rate' => $this->vat_rate,

            /**
             * Дополнительные данные
             */
            'social_links' => $this->social_links,

            /**
             * Отображение / сортировка / активность
             */
            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,
            'left' => (bool) $this->left,
            'main' => (bool) $this->main,
            'right' => (bool) $this->right,

            /**
             * Статус публикации
             */
            'status' => $this->status,

            /**
             * Модерация
             */
            'moderation_status' => (int) $this->moderation_status,
            'is_approved' => (int) $this->moderation_status === 1,
            'moderated_by' => $this->moderated_by,
            'moderated_at' => $this->moderated_at?->toISOString(),
            'moderation_note' => $this->moderation_note,

            /**
             * Дата публикации / окно показа
             */
            'published_at' => $this->published_at?->format('Y-m-d'),
            'show_from_at' => $this->show_from_at?->format('Y-m-d\TH:i'),
            'show_to_at' => $this->show_to_at?->format('Y-m-d\TH:i'),

            /**
             * Счётчики
             */
            'views' => (int) $this->views,

            /**
             * Текущий перевод
             */
            'translation' => $currentTranslation
                ? new MarketCompanyTranslationResource($currentTranslation)
                : null,

            /**
             * Все переводы
             */
            'translations' => MarketCompanyTranslationResource::collection(
                $this->whenLoaded('translations')
            ),

            /**
             * Timestamps
             */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            /**
             * Relations
             */
            'owner' => $this->whenLoaded('owner', function () {
                return [
                    'id' => $this->owner?->id,
                    'name' => $this->owner?->name,
                    'email' => $this->owner?->email,
                    'profile_photo_url' => $this->owner?->profile_photo_url,
                ];
            }),

            'moderator' => $this->whenLoaded('moderator', function () {
                return [
                    'id' => $this->moderator?->id,
                    'name' => $this->moderator?->name,
                ];
            }),
        ];
    }
}

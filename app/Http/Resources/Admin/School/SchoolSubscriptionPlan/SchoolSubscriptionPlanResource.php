<?php

namespace App\Http\Resources\Admin\School\SchoolSubscriptionPlan;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolSubscriptionPlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        /**
         * Для Edit Controller загружает
         * все translations.
         *
         * Текущий перевод определяем
         * только из уже загруженной
         * коллекции translations.
         *
         * Никакого обращения
         * к relation translation().
         */
        $translation = $this->relationLoaded('translations')
            ? (
            $this->translations->firstWhere(
                'locale',
                $locale
            )
                ?: $this->translations->firstWhere(
                'locale',
                $fallbackLocale
            )
                ?: $this->translations->first()
            )
            : null;

        /**
         * Связь images() уже отсортирована
         * по school_subscription_plan_has_images.order.
         *
         * Поэтому первый элемент является
         * главным изображением.
         *
         * Controller обязан загрузить:
         * images.media
         */
        $primaryImage = $this->relationLoaded('images')
            ? $this->images->first()
            : null;

        return [
            'id' =>
                $this->id,

            /**
             * Основные поля.
             */
            'sort' =>
                (int) $this->sort,

            'activity' =>
                (bool) $this->activity,

            'slug' =>
                $this->slug,

            /**
             * Текущий перевод.
             *
             * Только из уже загруженной
             * коллекции translations.
             */
            'translation' => $translation
                ? new SchoolSubscriptionPlanTranslationResource(
                    $translation
                )
                : null,

            /**
             * Все переводы нужны Edit.
             */
            'translations' =>
                SchoolSubscriptionPlanTranslationResource::collection(
                    $this->whenLoaded(
                        'translations'
                    )
                ),

            /**
             * Публикация и доступность.
             */
            'published_at' =>
                $this->published_at?->toISOString(),

            'available_from' =>
                $this->available_from?->toISOString(),

            'available_until' =>
                $this->available_until?->toISOString(),

            /**
             * Тариф.
             */
            'billing_period' =>
                $this->billing_period,

            'interval' =>
                (int) $this->interval,

            'currency_id' =>
                $this->currency_id,

            'price' =>
                (string) $this->price,

            'trial_days' =>
                (int) $this->trial_days,

            'auto_renew' =>
                (bool) $this->auto_renew,

            /**
             * Провайдер.
             */
            'provider' =>
                $this->provider,

            'provider_ref' =>
                $this->provider_ref,

            'provider_payload' =>
                $this->provider_payload,

            /**
             * Дополнительная конфигурация.
             */
            'config' =>
                $this->config,

            /**
             * Валюта.
             *
             * Currency не переводимая сущность.
             */
            'currency' => $this->whenLoaded(
                'currency',
                fn () => [
                    'id' =>
                        $this->currency->id,

                    'code' =>
                        $this->currency->code,

                    'name' =>
                        $this->currency->name,

                    'symbol' =>
                        $this->currency->symbol,
                ]
            ),

            /**
             * Главное изображение.
             *
             * Controller обязан загрузить:
             * images.media
             */
            'primary_image' => $primaryImage
                ? new SchoolSubscriptionPlanImageResource(
                    $primaryImage
                )
                : null,

            /**
             * Все изображения.
             */
            'images' =>
                SchoolSubscriptionPlanImageResource::collection(
                    $this->whenLoaded(
                        'images'
                    )
                ),

            /**
             * Counts.
             */
            'images_count' => $this->when(
                isset($this->images_count),
                fn () => (int) $this->images_count
            ),

            /**
             * Системные даты.
             */
            'created_at' =>
                $this->created_at?->toISOString(),

            'updated_at' =>
                $this->updated_at?->toISOString(),
        ];
    }
}

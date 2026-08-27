<?php

namespace App\Http\Resources\Admin\School\SchoolSubscriptionPlan;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolSubscriptionPlanSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /**
         * Admin Controller заранее загружает
         * только выбранную locale:
         *
         * translations(currentLocale)
         *
         * Поэтому первый элемент коллекции
         * является нужным переводом.
         */
        $translation = $this->relationLoaded('translations')
            ? $this->translations->first()
            : null;

        /**
         * Связь images() уже отсортирована
         * по pivot order.
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
            'slug' =>
                $this->slug,

            'sort' =>
                (int) $this->sort,

            'activity' =>
                (bool) $this->activity,

            /**
             * Перевод выбранной locale.
             *
             * Только из уже eager-loaded
             * translations.
             */
            'translation' => $translation
                ? [
                    'locale' =>
                        $translation->locale,

                    'title' =>
                        $translation->title,

                    'subtitle' =>
                        $translation->subtitle,

                    'short' =>
                        $translation->short,

                    'description' =>
                        $translation->description,
                ]
                : null,

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

            /**
             * Валюта.
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
             */
            'primary_image' => $primaryImage
                ? new SchoolSubscriptionPlanImageResource(
                    $primaryImage
                )
                : null,

            /**
             * Для Table/CardGrid изображения
             * оставляем доступными.
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

            'created_at' =>
                $this->created_at?->toISOString(),

            'updated_at' =>
                $this->updated_at?->toISOString(),
        ];
    }
}

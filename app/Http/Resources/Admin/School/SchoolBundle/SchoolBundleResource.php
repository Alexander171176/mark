<?php

namespace App\Http\Resources\Admin\School\SchoolBundle;

use App\Http\Resources\Admin\School\SchoolCourse\SchoolCourseSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolBundleResource extends JsonResource
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
         * Первое изображение уже соответствует
         * pivot order связи images().
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
                ? new SchoolBundleTranslationResource(
                    $translation
                )
                : null,

            /**
             * Все переводы нужны Edit.
             */
            'translations' =>
                SchoolBundleTranslationResource::collection(
                    $this->whenLoaded(
                        'translations'
                    )
                ),

            /**
             * Публикация.
             */
            'published_at' =>
                $this->published_at?->format(
                    'Y-m-d'
                ),

            /**
             * Статистика.
             */
            'views' =>
                (int) $this->views,

            'likes' =>
                (int) $this->likes,

            'meta' =>
                $this->meta,

            /**
             * Изображения.
             *
             * Controller обязан загрузить:
             * images.media
             */
            'primary_image' => $primaryImage
                ? new SchoolBundleImageResource(
                    $primaryImage
                )
                : null,

            'images' =>
                SchoolBundleImageResource::collection(
                    $this->whenLoaded(
                        'images'
                    )
                ),

            /**
             * Курсы внутри набора.
             *
             * Controller загружает
             * courses.translations(currentLocale).
             */
            'courses' =>
                SchoolCourseSharedResource::collection(
                    $this->whenLoaded(
                        'courses'
                    )
                ),

            /**
             * Цены набора.
             */
            'prices' => $this->whenLoaded(
                'prices',
                fn () => $this->prices->map(
                    fn ($price) => [
                        'id' =>
                            $price->id,

                        'school_bundle_id' =>
                            $price->school_bundle_id,

                        'currency_id' =>
                            $price->currency_id,

                        'price' =>
                            (string) $price->price,

                        'sale_price' =>
                            $price->sale_price !== null
                                ? (string) $price->sale_price
                                : null,

                        'compare_at_price' =>
                            $price->compare_at_price !== null
                                ? (string) $price->compare_at_price
                                : null,

                        'effective_price' =>
                            $price->effective_price
                            ?? null,

                        'has_discount' =>
                            (bool) (
                                $price->has_discount
                                ?? false
                            ),

                        'discount_amount' =>
                            $price->discount_amount
                            ?? null,

                        'discount_percent' =>
                            $price->discount_percent
                            ?? null,

                        'starts_at' =>
                            $price->starts_at?->toISOString(),

                        'ends_at' =>
                            $price->ends_at?->toISOString(),

                        'activity' =>
                            (bool) $price->activity,

                        'sort' =>
                            (int) $price->sort,

                        'meta' =>
                            $price->meta,
                    ]
                )
            ),

            /**
             * Позиции заказов.
             *
             * Загружаются только там,
             * где действительно нужны.
             */
            'order_items' => $this->whenLoaded(
                'orderItems',
                fn () => $this->orderItems->map(
                    fn ($item) => [
                        'id' =>
                            $item->id,

                        'school_order_id' =>
                            $item->school_order_id,

                        'title' =>
                            $item->title,

                        'currency' =>
                            $item->currency,

                        'quantity' =>
                            (int) $item->quantity,

                        'unit_price' =>
                            (string) $item->unit_price,

                        'discount' =>
                            (string) $item->discount,

                        'total' =>
                            (string) $item->total,
                    ]
                )
            ),

            /**
             * Counts.
             */
            'courses_count' => $this->when(
                isset($this->courses_count),
                fn () => (int) $this->courses_count
            ),

            'images_count' => $this->when(
                isset($this->images_count),
                fn () => (int) $this->images_count
            ),

            'prices_count' => $this->when(
                isset($this->prices_count),
                fn () => (int) $this->prices_count
            ),

            'order_items_count' => $this->when(
                isset($this->order_items_count),
                fn () => (int) $this->order_items_count
            ),

            'created_at' =>
                $this->created_at?->toISOString(),

            'updated_at' =>
                $this->updated_at?->toISOString(),
        ];
    }
}

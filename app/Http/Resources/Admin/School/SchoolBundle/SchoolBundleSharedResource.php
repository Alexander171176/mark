<?php

namespace App\Http\Resources\Admin\School\SchoolBundle;

use App\Http\Resources\Admin\School\SchoolCourse\SchoolCourseSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolBundleSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /**
         * Admin Index заранее загружает
         * только выбранную locale:
         *
         * translations(currentLocale)
         */
        $translation = $this->relationLoaded(
            'translations'
        )
            ? $this->translations->first()
            : null;

        /**
         * Первое изображение уже соответствует
         * pivot order связи images().
         *
         * Controller обязан загрузить:
         * images.media
         */
        $primaryImage = $this->relationLoaded(
            'images'
        )
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
             * Перевод выбранной locale.
             *
             * description нужен
             * frontend-поиску.
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
             * Курсы.
             *
             * Они нужны:
             * - отображению;
             * - frontend-поиску.
             */
            'courses' =>
                SchoolCourseSharedResource::collection(
                    $this->whenLoaded(
                        'courses'
                    )
                ),

            /**
             * Counts нужны отображению
             * и frontend-сортировке.
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

            /**
             * Даты нужны frontend-сортировке.
             */
            'created_at' =>
                $this->created_at?->toISOString(),

            'updated_at' =>
                $this->updated_at?->toISOString(),
        ];
    }
}

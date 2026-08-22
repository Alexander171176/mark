<?php

namespace App\Http\Resources\Public\School\SchoolCourse;

use App\Http\Resources\Admin\School\SchoolCourse\SchoolCourseImageResource;
use App\Http\Resources\Public\School\SchoolHashtag\SchoolHashtagSharedResource;
use App\Http\Resources\Public\School\SchoolInstructorProfile\SchoolInstructorProfileSharedResource;
use App\Http\Resources\Public\School\SchoolTrack\SchoolTrackSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolCourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        /**
         * Public Show заранее загружает максимум:
         *
         * current locale + fallback locale.
         *
         * Resource ничего не запрашивает из БД.
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

        return [
            'id' =>
                (int) $this->id,

            'slug' =>
                $this->slug,

            'sort' =>
                (int) $this->sort,

            /**
             * Основной Public-перевод.
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

                    'meta_title' =>
                        $translation->meta_title,

                    'meta_keywords' =>
                        $translation->meta_keywords,

                    'meta_desc' =>
                        $translation->meta_desc,
                ]
                : null,

            /**
             * Флаги.
             */
            'is_new' =>
                (bool) $this->is_new,

            'is_hit' =>
                (bool) $this->is_hit,

            'is_sale' =>
                (bool) $this->is_sale,

            /**
             * Состояние курса.
             */
            'level' =>
                $this->level,

            'availability' =>
                $this->availability,

            'difficulty' =>
                $this->difficulty !== null
                    ? (int) $this->difficulty
                    : null,

            'duration' =>
                $this->duration !== null
                    ? (int) $this->duration
                    : null,

            'published_at' =>
                $this->published_at?->format('Y-m-d'),

            /**
             * Статистика.
             */
            'students_count' =>
                (int) $this->students_count,

            'popularity' =>
                (int) $this->popularity,

            'rating_count' =>
                (int) $this->rating_count,

            'rating_avg' =>
                $this->rating_avg !== null
                    ? (float) $this->rating_avg
                    : null,

            'views' =>
                (int) $this->views,

            /**
             * Лайки.
             */
            'likes_count' => $this->when(
                isset($this->likes_count),
                fn () => (int) $this->likes_count
            ),

            'already_liked' =>
                (bool) ($this->already_liked ?? false),

            /**
             * Изображения.
             *
             * Controller загружает images.media.
             */
            'images' =>
                SchoolCourseImageResource::collection(
                    $this->whenLoaded(
                        'images'
                    )
                ),

            /**
             * Инструктор.
             */
            'instructorProfile' =>
                new SchoolInstructorProfileSharedResource(
                    $this->whenLoaded(
                        'instructorProfile'
                    )
                ),

            /**
             * Треки.
             */
            'tracks' =>
                SchoolTrackSharedResource::collection(
                    $this->whenLoaded(
                        'tracks'
                    )
                ),

            /**
             * Хештеги.
             */
            'hashtags' =>
                SchoolHashtagSharedResource::collection(
                    $this->whenLoaded(
                        'hashtags'
                    )
                ),

            /**
             * Цены.
             */
            'prices' => $this->whenLoaded(
                'prices',
                fn () =>
                $this->prices->map(
                    fn ($price) => [
                        'id' =>
                            (int) $price->id,

                        'currency_id' =>
                            (int) $price->currency_id,

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
                            $price->effective_price ?? null,

                        'has_discount' =>
                            (bool) ($price->has_discount ?? false),

                        'discount_amount' =>
                            $price->discount_amount ?? null,

                        'discount_percent' =>
                            $price->discount_percent ?? null,

                        'starts_at' =>
                            $price->starts_at?->toISOString(),

                        'ends_at' =>
                            $price->ends_at?->toISOString(),
                    ]
                )
            ),

            /**
             * Отзывы.
             */
            'reviews' => $this->whenLoaded(
                'reviews',
                fn () =>
                $this->reviews->map(
                    fn ($review) => [
                        'id' =>
                            (int) $review->id,

                        'rating' =>
                            $review->rating !== null
                                ? (int) $review->rating
                                : null,

                        'title' =>
                            $review->title,

                        'body' =>
                            $review->body,

                        'user' =>
                            $review->relationLoaded('user')
                            && $review->user
                                ? [
                                'id' =>
                                    (int) $review->user->id,

                                'name' =>
                                    $review->user->name,
                            ]
                                : null,

                        'created_at' =>
                            $review->created_at?->toISOString(),
                    ]
                )
            ),

            /**
             * Рекомендованные курсы.
             *
             * Для карточек нужен краткий
             * Public Resource.
             */
            'related_courses' =>
                SchoolCourseSharedResource::collection(
                    $this->whenLoaded(
                        'relatedCourses'
                    )
                ),

            /**
             * Counts.
             */
            'modules_count' => $this->when(
                isset($this->modules_count),
                fn () => (int) $this->modules_count
            ),

            'lessons_count' => $this->when(
                isset($this->lessons_count),
                fn () => (int) $this->lessons_count
            ),

            'tracks_count' => $this->when(
                isset($this->tracks_count),
                fn () => (int) $this->tracks_count
            ),

            'hashtags_count' => $this->when(
                isset($this->hashtags_count),
                fn () => (int) $this->hashtags_count
            ),

            'images_count' => $this->when(
                isset($this->images_count),
                fn () => (int) $this->images_count
            ),

            'prices_count' => $this->when(
                isset($this->prices_count),
                fn () => (int) $this->prices_count
            ),

            'reviews_count' => $this->when(
                isset($this->reviews_count),
                fn () => (int) $this->reviews_count
            ),

            'created_at' =>
                $this->created_at?->toISOString(),
        ];
    }
}

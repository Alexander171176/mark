<?php

namespace App\Http\Resources\Public\School\SchoolInstructorProfile;

use App\Http\Resources\Admin\School\SchoolInstructorProfile\SchoolInstructorProfileImageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolInstructorProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /**
         * Controller заранее загружает:
         *
         * current locale
         * + fallback locale.
         *
         * Resource не выполняет
         * самостоятельные запросы переводов.
         *
         * Model выбирает:
         *
         * current
         * → fallback.
         */
        $translation = $this->relationLoaded('translations')
            ? $this->translationOrFallback()
            : null;

        /**
         * Публичное имя инструктора.
         *
         * В первую очередь используем
         * локализованный title.
         */
        $publicName =
            $translation?->title
                ?: $this->user?->name
                ?: 'Инструктор';

        return [
            'id' =>
                (int) $this->id,

            /**
             * Основные публичные поля.
             */
            'slug' =>
                $this->slug,

            'sort' =>
                (int) $this->sort,

            /**
             * Публичный перевод:
             *
             * current locale
             * → fallback locale.
             *
             * Show Resource содержит
             * полный набор данных,
             * необходимый странице инструктора.
             */
            'translation' => $translation
                ? [
                    'locale' =>
                        $translation->locale,

                    'title' =>
                        $translation->title,

                    'short' =>
                        $translation->short,

                    'bio' =>
                        $translation->bio,

                    'meta_title' =>
                        $translation->meta_title,

                    'meta_keywords' =>
                        $translation->meta_keywords,

                    'meta_desc' =>
                        $translation->meta_desc,
                ]
                : null,

            /**
             * Готовое публичное имя.
             *
             * Может использоваться:
             * - breadcrumb;
             * - alt;
             * - title;
             * - других компактных местах.
             */
            'public_name' =>
                $publicName,

            /**
             * Данные инструктора.
             */
            'experience_years' =>
                $this->experience_years !== null
                    ? (int) $this->experience_years
                    : null,

            'hourly_rate' =>
                $this->hourly_rate !== null
                    ? (string) $this->hourly_rate
                    : null,

            /**
             * Рейтинг.
             */
            'rating' => [
                'avg' =>
                    $this->rating_avg !== null
                        ? (float) $this->rating_avg
                        : null,

                'count' =>
                    (int) $this->rating_count,
            ],

            'views' =>
                (int) $this->views,

            /**
             * Социальные ссылки.
             */
            'social_links' =>
                $this->social_links ?? [],

            /**
             * Пользователь.
             *
             * Только публично необходимые поля.
             * Email и другие внутренние данные
             * не передаём.
             */
            'user' => $this->whenLoaded(
                'user',
                fn () => $this->user
                    ? [
                        'id' =>
                            (int) $this->user->id,

                        'name' =>
                            $this->user->name,
                    ]
                    : null
            ),

            /**
             * Изображения.
             *
             * Используем общий Resource изображения,
             * так как обработка media централизована
             * через Spatie Media Library.
             *
             * Controller должен заранее загрузить:
             * images.media.
             */
            'images' =>
                SchoolInstructorProfileImageResource::collection(
                    $this->whenLoaded(
                        'images'
                    )
                ),

            /**
             * Количество публичных курсов.
             *
             * Сам Resource ничего не считает.
             * Значение заранее формирует Controller.
             */
            'courses_count' => $this->when(
                isset($this->courses_count),
                fn () =>
                (int) $this->courses_count
            ),

            /**
             * Даты.
             */
            'created_at' =>
                $this->created_at?->toISOString(),

            'updated_at' =>
                $this->updated_at?->toISOString(),
        ];
    }
}

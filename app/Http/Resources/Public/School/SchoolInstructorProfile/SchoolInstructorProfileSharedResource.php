<?php

namespace App\Http\Resources\Public\School\SchoolInstructorProfile;

use App\Http\Resources\Admin\School\SchoolInstructorProfile\SchoolInstructorProfileImageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolInstructorProfileSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        /**
         * Публичный перевод:
         *
         * 1. current locale;
         * 2. fallback locale;
         * 3. иначе null.
         *
         * Controller заранее загружает
         * только current + fallback.
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
            )
            : null;

        return [
            'id' =>
                $this->id,

            /**
             * Основные публичные поля.
             */
            'slug' =>
                $this->slug,

            'sort' =>
                (int) $this->sort,

            /**
             * Текущий перевод
             * или fallback locale.
             */
            'translation' => $translation
                ? [
                    'locale' =>
                        $translation->locale,

                    'title' =>
                        $translation->title,

                    'short' =>
                        $translation->short,
                ]
                : null,

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

            'rating_avg' =>
                $this->rating_avg !== null
                    ? (float) $this->rating_avg
                    : null,

            'rating_count' =>
                (int) $this->rating_count,

            'views' =>
                (int) $this->views,

            /**
             * Пользователь.
             *
             * Email публично не отдаём.
             */
            'user' => $this->whenLoaded(
                'user',
                fn () => $this->user
                    ? [
                        'id' =>
                            $this->user->id,

                        'name' =>
                            $this->user->name,
                    ]
                    : null
            ),

            /**
             * Изображения.
             *
             * Controller загружает images.media.
             */
            'images' =>
                SchoolInstructorProfileImageResource::collection(
                    $this->whenLoaded(
                        'images'
                    )
                ),

            /**
             * Counts.
             */
            'courses_count' => $this->when(
                isset($this->courses_count),
                fn () => (int) $this->courses_count
            ),

            /**
             * Нужен frontend-сортировке dateAsc/dateDesc.
             */
            'created_at' =>
                $this->created_at?->toISOString(),
        ];
    }
}

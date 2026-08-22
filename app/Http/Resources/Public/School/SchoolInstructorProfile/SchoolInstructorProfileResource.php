<?php

namespace App\Http\Resources\Public\School\SchoolInstructorProfile;

use App\Http\Resources\Admin\School\SchoolInstructorProfile\SchoolInstructorProfileImageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolInstructorProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        /**
         * Controller заранее загружает:
         *
         * current locale
         * + fallback locale.
         *
         * Приоритет:
         *
         * current
         * → fallback
         * → первый фактически загруженный.
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
         * Публичное имя.
         *
         * Не используем здесь accessor public_name,
         * потому что Resource уже точно определил
         * правильный current/fallback перевод.
         */
        $publicName = $translation?->title
            ?: $this->user?->name
                ?: 'Инструктор';

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
             * Полный Public-перевод.
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
             * Удобно для breadcrumb,
             * alt изображения и других мест,
             * где не нужен доступ к translation.
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
             *
             * Полный Resource сохраняет
             * удобный вложенный контракт.
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
             * Email в Public не отдаём.
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
             * Controller обязан загрузить
             * images.media.
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
             * Даты.
             */
            'created_at' =>
                $this->created_at?->toISOString(),

            'updated_at' =>
                $this->updated_at?->toISOString(),
        ];
    }
}

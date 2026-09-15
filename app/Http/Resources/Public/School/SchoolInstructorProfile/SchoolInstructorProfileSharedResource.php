<?php

namespace App\Http\Resources\Public\School\SchoolInstructorProfile;

use App\Http\Resources\Admin\School\SchoolInstructorProfile\SchoolInstructorProfileImageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolInstructorProfileSharedResource extends JsonResource
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
             * Минимальный перевод,
             * необходимый списку/карточке.
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
             * Данные инструктора,
             * используемые карточкой
             * и frontend-сортировкой.
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
             * Только публичное имя.
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
             * Количество только
             * публичных курсов.
             *
             * Рассчитывается Controller.
             */
            'courses_count' => $this->when(
                isset($this->courses_count),
                fn () =>
                (int) $this->courses_count
            ),

            /**
             * Нужен frontend-сортировке:
             *
             * dateAsc
             * dateDesc.
             */
            'created_at' =>
                $this->created_at?->toISOString(),
        ];
    }
}

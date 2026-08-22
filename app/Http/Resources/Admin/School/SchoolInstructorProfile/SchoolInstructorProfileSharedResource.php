<?php

namespace App\Http\Resources\Admin\School\SchoolInstructorProfile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolInstructorProfileSharedResource extends JsonResource
{
    public function toArray(
        Request $request
    ): array {
        /**
         * Для Admin Index Controller
         * заранее загружает только
         * перевод выбранной локали.
         */
        $translation = $this->relationLoaded(
            'translations'
        )
            ? $this->translations->first()
            : null;

        /**
         * Первое изображение уже
         * отсортировано relation по pivot order.
         */
        $primaryImage = $this->relationLoaded(
            'images'
        )
            ? $this->images->first()
            : null;

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,

            /**
             * Основные поля.
             */
            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,

            'slug' => $this->slug,

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
             * Перевод выбранной локали.
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
                ]
                : null,

            /**
             * Для существующего Admin frontend
             * временно оставляем плоские поля.
             *
             * Позже при желании можно полностью
             * перевести компоненты на translation.*.
             */
            'title' =>
                $translation?->title,

            'short' =>
                $translation?->short,

            'bio' =>
                $translation?->bio,

            /**
             * Пользователь.
             */
            'user' => $this->whenLoaded(
                'user',
                fn () => $this->user
                    ? [
                        'id' =>
                            $this->user->id,

                        'name' =>
                            $this->user->name,

                        'email' =>
                            $this->user->email,
                    ]
                    : null
            ),

            /**
             * Минимальный набор курсов.
             *
             * Нужен только frontend-поиску.
             */
            'courses' => $this->whenLoaded(
                'courses',
                fn () => $this->courses
                    ->map(
                        function ($course) {
                            $courseTranslation =
                                $course->relationLoaded(
                                    'translations'
                                )
                                    ? $course
                                    ->translations
                                    ->first()
                                    : null;

                            return [
                                'id' =>
                                    $course->id,

                                'slug' =>
                                    $course->slug,

                                'title' =>
                                    $courseTranslation?->title,
                            ];
                        }
                    )
                    ->values()
            ),

            /**
             * Изображения.
             */
            'images' =>
                SchoolInstructorProfileImageResource::collection(
                    $this->whenLoaded(
                        'images'
                    )
                ),

            /**
             * Главное изображение.
             */
            'primary_image' =>
                $primaryImage
                    ? new SchoolInstructorProfileImageResource(
                    $primaryImage
                )
                    : null,

            /**
             * Counts.
             */
            'courses_count' =>
                $this->when(
                    isset($this->courses_count),
                    fn () =>
                    (int) $this->courses_count
                ),

            'payouts_count' =>
                $this->when(
                    isset($this->payouts_count),
                    fn () =>
                    (int) $this->payouts_count
                ),

            'images_count' =>
                $this->when(
                    isset($this->images_count),
                    fn () =>
                    (int) $this->images_count
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

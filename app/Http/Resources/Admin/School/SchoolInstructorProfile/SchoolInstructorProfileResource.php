<?php

namespace App\Http\Resources\Admin\School\SchoolInstructorProfile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolInstructorProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
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

            'user_id' =>
                $this->user_id,

            'slug' =>
                $this->slug,

            /**
             * Профессиональные данные.
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
             * Оставляем плоский контракт,
             * совпадающий с моделью
             * и Admin Index.
             */
            'rating_count' =>
                (int) $this->rating_count,

            'rating_avg' =>
                $this->rating_avg !== null
                    ? (float) $this->rating_avg
                    : null,

            /**
             * Статистика.
             */
            'views' =>
                (int) $this->views,

            /**
             * Социальные ссылки.
             */
            'social_links' =>
                $this->social_links ?? [],

            /**
             * Все переводы.
             *
             * Для Edit контроллер
             * загружает все локали.
             */
            'translations' =>
                SchoolInstructorProfileTranslationResource::collection(
                    $this->whenLoaded(
                        'translations'
                    )
                ),

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
             * Изображения.
             *
             * Контроллер обязан загрузить
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
             *
             * Появляются только там,
             * где контроллер их запросил.
             */
            'courses_count' => $this->when(
                isset($this->courses_count),
                fn () => (int) $this->courses_count
            ),

            'payouts_count' => $this->when(
                isset($this->payouts_count),
                fn () => (int) $this->payouts_count
            ),

            'images_count' => $this->when(
                isset($this->images_count),
                fn () => (int) $this->images_count
            ),

            /**
             * Timestamps.
             */
            'created_at' =>
                $this->created_at?->toISOString(),

            'updated_at' =>
                $this->updated_at?->toISOString(),
        ];
    }
}

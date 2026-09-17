<?php

namespace App\Http\Resources\Public\School\SchoolCourse;

use App\Http\Resources\Admin\School\SchoolCourse\SchoolCourseImageResource;
use App\Http\Resources\Public\School\SchoolInstructorProfile\SchoolInstructorProfileSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolCourseSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /**
         * Model работает только
         * с уже загруженными translations.
         *
         * Public-контракт:
         *
         * current locale
         * → fallback locale.
         */
        $translation =
            $this->translationOrFallback();

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
                ]
                : null,

            /**
             * Публичные флаги.
             */
            'is_new' =>
                (bool) $this->is_new,

            'is_hit' =>
                (bool) $this->is_hit,

            'is_sale' =>
                (bool) $this->is_sale,

            /**
             * Характеристики курса.
             */
            'level' =>
                $this->level,

            'difficulty' =>
                $this->difficulty !== null
                    ? (int) $this->difficulty
                    : null,

            'duration' =>
                $this->duration !== null
                    ? (int) $this->duration
                    : null,

            /**
             * Статистика.
             */
            'students_count' =>
                (int) $this->students_count,

            'popularity' =>
                (int) $this->popularity,

            'rating_avg' =>
                $this->rating_avg !== null
                    ? (float) $this->rating_avg
                    : null,

            'rating_count' =>
                (int) $this->rating_count,

            'views' =>
                (int) $this->views,

            /**
             * Лайки.
             *
             * likes_count приходит
             * из withCount().
             */
            'likes_count' => $this->when(
                isset($this->likes_count),
                fn () =>
                (int) $this->likes_count
            ),

            /**
             * Лайк текущего пользователя.
             *
             * Приходит из withUserLike().
             */
            'already_liked' =>
                (bool) (
                    $this->already_liked
                    ?? false
                ),

            /**
             * Изображения курса.
             *
             * Ресурс изображения общий,
             * так как изображения управляются
             * через Spatie Media Library.
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
             * Counts для карточек
             * и Public-сортировки.
             */
            'modules_count' => $this->when(
                isset($this->modules_count),
                fn () =>
                (int) $this->modules_count
            ),

            'lessons_count' => $this->when(
                isset($this->lessons_count),
                fn () =>
                (int) $this->lessons_count
            ),

            'tracks_count' => $this->when(
                isset($this->tracks_count),
                fn () =>
                (int) $this->tracks_count
            ),

            'hashtags_count' => $this->when(
                isset($this->hashtags_count),
                fn () =>
                (int) $this->hashtags_count
            ),

            'reviews_count' => $this->when(
                isset($this->reviews_count),
                fn () =>
                (int) $this->reviews_count
            ),

            /**
             * Публичная дата публикации.
             *
             * Нужна frontend-сортировке.
             */
            'published_at' =>
                $this->published_at?->format(
                    'Y-m-d'
                ),
        ];
    }
}

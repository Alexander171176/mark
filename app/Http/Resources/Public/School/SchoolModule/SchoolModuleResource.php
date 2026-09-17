<?php

namespace App\Http\Resources\Public\School\SchoolModule;

use App\Http\Resources\Admin\School\SchoolModule\SchoolModuleImageResource;
use App\Http\Resources\Public\School\SchoolCourse\SchoolCourseSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolModuleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();
        $fallbackLocale = config('app.fallback_locale', 'ru');

        /**
         * Resolved-перевод:
         *
         * current locale → fallback locale.
         *
         * Только из уже загруженной relation translations,
         * без дополнительных SQL-запросов.
         */
        $translation = $this->translationOrFallback(
            $locale,
            $fallbackLocale
        );

        return [
            'id' => $this->id,
            'school_course_id' => $this->school_course_id,

            /**
             * Основные публичные поля.
             */
            'slug' => $this->slug,
            'sort' => (int) $this->sort,

            /**
             * Полный resolved-перевод для Show + SEO.
             */
            'translation' => $translation
                ? [
                    'locale' => $translation->locale,
                    'title' => $translation->title,
                    'subtitle' => $translation->subtitle,
                    'short' => $translation->short,
                    'description' => $translation->description,
                    'meta_title' => $translation->meta_title,
                    'meta_keywords' => $translation->meta_keywords,
                    'meta_desc' => $translation->meta_desc,
                ]
                : null,

            /**
             * Публичные характеристики.
             */
            'difficulty' => $this->difficulty !== null
                ? (int) $this->difficulty
                : null,

            'duration' => $this->duration !== null
                ? (int) $this->duration
                : null,

            /**
             * Статистика.
             */
            'lessons_count' => $this->when(
                isset($this->lessons_count),
                fn () => (int) $this->lessons_count
            ),

            'popularity' => (int) $this->popularity,

            'rating_count' => (int) $this->rating_count,

            'rating_avg' => $this->rating_avg !== null
                ? (float) $this->rating_avg
                : null,

            'views' => (int) $this->views,

            'likes_count' => $this->when(
                isset($this->likes_count),
                fn () => (int) $this->likes_count
            ),

            'already_liked' => $this->when(
                isset($this->already_liked),
                fn () => (bool) $this->already_liked
            ),

            /**
             * Изображения.
             *
             * Общий Resource Spatie Media Library.
             */
            'images' => SchoolModuleImageResource::collection(
                $this->whenLoaded('images')
            ),

            /**
             * Родительский публичный курс.
             */
            'course' => new SchoolCourseSharedResource(
                $this->whenLoaded('course')
            ),

            /**
             * Публичная дата публикации.
             */
            'published_at' => $this->published_at?->toISOString(),
        ];
    }
}

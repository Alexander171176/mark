<?php

namespace App\Http\Resources\Public\School\SchoolLesson;

use App\Http\Resources\Admin\School\SchoolLesson\SchoolLessonImageResource;
use App\Http\Resources\Public\School\SchoolHashtag\SchoolHashtagSharedResource;
use App\Http\Resources\Public\School\SchoolModule\SchoolModuleSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolLessonResource extends JsonResource
{
    /**
     * Полный Public-ресурс урока.
     *
     * Используется преимущественно
     * на странице Show.
     *
     * @return array<string, mixed>
     */
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

            'school_module_id' =>
                (int) $this->school_module_id,

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
             * Публичные характеристики.
             */
            'availability' =>
                $this->availability,

            'access_type' =>
                $this->access_type,

            'difficulty' =>
                $this->difficulty !== null
                    ? (int) $this->difficulty
                    : null,

            'duration' =>
                $this->duration !== null
                    ? (int) $this->duration
                    : null,

            /**
             * Настройки публичного preview.
             */
            'preview_mode' =>
                $this->preview_mode,

            'preview_value' =>
                $this->preview_value !== null
                    ? (int) $this->preview_value
                    : null,

            'published_at' =>
                $this->published_at?->format(
                    'Y-m-d'
                ),

            /**
             * Статистика.
             */
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
                fn () =>
                (int) $this->likes_count
            ),

            'already_liked' =>
                (bool) (
                    $this->already_liked
                    ?? false
                ),

            /**
             * Изображения.
             *
             * Controller должен
             * загрузить images.media.
             */
            'images' =>
                SchoolLessonImageResource::collection(
                    $this->whenLoaded(
                        'images'
                    )
                ),

            'images_count' => $this->when(
                isset($this->images_count),
                fn () =>
                (int) $this->images_count
            ),

            /**
             * Родительский модуль.
             *
             * Controller должен загрузить
             * публичный module вместе
             * с необходимыми relations.
             */
            'module' =>
                $this->whenLoaded(
                    'module',
                    fn () =>
                    $this->module
                        ? new SchoolModuleSharedResource(
                        $this->module
                    )
                        : null
                ),

            /**
             * Публичные хештеги.
             */
            'hashtags' =>
                SchoolHashtagSharedResource::collection(
                    $this->whenLoaded(
                        'hashtags'
                    )
                ),

            'hashtags_count' => $this->when(
                isset($this->hashtags_count),
                fn () =>
                (int) $this->hashtags_count
            ),
        ];
    }
}

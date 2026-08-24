<?php

namespace App\Http\Resources\Admin\School\SchoolLesson;

use App\Http\Resources\Admin\School\SchoolHashtag\SchoolHashtagSharedResource;
use App\Http\Resources\Admin\School\SchoolModule\SchoolModuleSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolLessonSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /**
         * Admin Index заранее загружает
         * только выбранную локаль.
         */
        $translation = $this->relationLoaded('translations')
            ? $this->translations->first()
            : null;

        /**
         * Первое изображение уже соответствует
         * pivot order связи images().
         */
        $primaryImage = $this->relationLoaded('images')
            ? $this->images->first()
            : null;

        return [
            'id' =>
                $this->id,

            'school_module_id' =>
                $this->school_module_id,

            /**
             * Основные поля.
             */
            'sort' =>
                (int) $this->sort,

            'activity' =>
                (bool) $this->activity,

            'slug' =>
                $this->slug,

            /**
             * Перевод выбранной локали.
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

                    /**
                     * Нужен frontend-поиску.
                     */
                    'description' =>
                        $translation->description,
                ]
                : null,

            /**
             * Привязанный контент.
             *
             * Для Admin Index достаточно
             * типа и ID.
             */
            'content_type' =>
                $this->content_type,

            'content_id' =>
                $this->content_id !== null
                    ? (int) $this->content_id
                    : null,

            /**
             * Публикация / состояние.
             */
            'published_at' =>
                $this->published_at?->format(
                    'Y-m-d'
                ),

            'status' =>
                $this->status,

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
             * Preview.
             */
            'preview_mode' =>
                $this->preview_mode,

            'preview_value' =>
                $this->preview_value !== null
                    ? (int) $this->preview_value
                    : null,

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

            'likes' =>
                (int) $this->likes,

            /**
             * Изображения.
             *
             * Controller обязан загрузить
             * images.media.
             */
            'primary_image' => $primaryImage
                ? new SchoolLessonImageResource(
                    $primaryImage
                )
                : null,

            'images' =>
                SchoolLessonImageResource::collection(
                    $this->whenLoaded(
                        'images'
                    )
                ),

            /**
             * Родительский модуль.
             *
             * Внутри него также доступен
             * родительский курс.
             */
            'module' =>
                new SchoolModuleSharedResource(
                    $this->whenLoaded(
                        'module'
                    )
                ),

            /**
             * Хештеги нужны
             * frontend-поиску.
             */
            'hashtags' =>
                SchoolHashtagSharedResource::collection(
                    $this->whenLoaded(
                        'hashtags'
                    )
                ),

            /**
             * Counts.
             */
            'likes_count' => $this->when(
                isset($this->likes_count),
                fn () => (int) $this->likes_count
            ),

            'hashtags_count' => $this->when(
                isset($this->hashtags_count),
                fn () => (int) $this->hashtags_count
            ),

            'images_count' => $this->when(
                isset($this->images_count),
                fn () => (int) $this->images_count
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

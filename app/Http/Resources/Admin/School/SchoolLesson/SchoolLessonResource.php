<?php

namespace App\Http\Resources\Admin\School\SchoolLesson;

use App\Http\Resources\Admin\School\SchoolHashtag\SchoolHashtagSharedResource;
use App\Http\Resources\Admin\School\SchoolModule\SchoolModuleSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolLessonResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

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
             * Привязанный контент.
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
             * Текущий перевод.
             *
             * Только из уже загруженной
             * коллекции translations.
             */
            'translation' => $translation
                ? new SchoolLessonTranslationResource(
                    $translation
                )
                : null,

            /**
             * Все переводы нужны Edit.
             */
            'translations' =>
                SchoolLessonTranslationResource::collection(
                    $this->whenLoaded(
                        'translations'
                    )
                ),

            /**
             * Изображения нужны Edit.
             *
             * Controller загружает images.media.
             */
            'images' =>
                SchoolLessonImageResource::collection(
                    $this->whenLoaded(
                        'images'
                    )
                ),

            /**
             * Родительский модуль.
             *
             * Внутри Shared Resource
             * доступен его курс.
             */
            'module' =>
                new SchoolModuleSharedResource(
                    $this->whenLoaded(
                        'module'
                    )
                ),

            /**
             * Хештеги.
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

            'created_at' =>
                $this->created_at?->toISOString(),

            'updated_at' =>
                $this->updated_at?->toISOString(),
        ];
    }
}

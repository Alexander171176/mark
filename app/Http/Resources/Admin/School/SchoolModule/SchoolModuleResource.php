<?php

namespace App\Http\Resources\Admin\School\SchoolModule;

use App\Http\Resources\Admin\School\SchoolCourse\SchoolCourseSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolModuleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        /**
         * Edit заранее загружает
         * все translations.
         *
         * Resource выбирает текущий перевод
         * только из памяти:
         *
         * current locale
         * → fallback locale
         * → первый доступный.
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
         * Первое изображение уже соответствует
         * pivot order связи images().
         */
        $primaryImage = $this->relationLoaded('images')
            ? $this->images->first()
            : null;

        return [
            'id' =>
                $this->id,

            'school_course_id' =>
                $this->school_course_id,

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
             * Публикация / состояние.
             */
            'published_at' =>
                $this->published_at?->format('Y-m-d'),

            'status' =>
                $this->status,

            'availability' =>
                $this->availability,

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
            'lessons_count' =>
                (int) $this->lessons_count,

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
             * Текущий resolved-перевод.
             *
             * Только из уже загруженной
             * коллекции translations.
             */
            'translation' => $translation
                ? new SchoolModuleTranslationResource(
                    $translation
                )
                : null,

            /**
             * Все переводы нужны Edit
             * для TranslationTabs.
             */
            'translations' =>
                SchoolModuleTranslationResource::collection(
                    $this->whenLoaded(
                        'translations'
                    )
                ),

            /**
             * Изображения.
             *
             * Controller обязан загрузить
             * images.media.
             */
            'primary_image' => $primaryImage
                ? new SchoolModuleImageResource(
                    $primaryImage
                )
                : null,

            'images' =>
                SchoolModuleImageResource::collection(
                    $this->whenLoaded(
                        'images'
                    )
                ),

            /**
             * Родительский курс.
             */
            'course' =>
                new SchoolCourseSharedResource(
                    $this->whenLoaded(
                        'course'
                    )
                ),

            /**
             * Counts.
             */
            'images_count' => $this->when(
                isset($this->images_count),
                fn () => (int) $this->images_count
            ),

            'likes_count' => $this->when(
                isset($this->likes_count),
                fn () => (int) $this->likes_count
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

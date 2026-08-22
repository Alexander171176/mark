<?php

namespace App\Http\Resources\Admin\School\SchoolTrack;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolTrackResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $currentLocale =
            app()->getLocale();

        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        /**
         * Текущий перевод нужен только
         * как удобный административный
         * resolved-перевод.
         *
         * Все переводы всё равно
         * передаются отдельно ниже.
         */
        $translation =
            $this->relationLoaded('translations')
                ? (
            $this->translations->firstWhere(
                'locale',
                $currentLocale
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

            /**
             * Основные поля.
             */
            'parent_id' =>
                $this->parent_id,

            'sort' =>
                (int) $this->sort,

            'activity' =>
                (bool) $this->activity,

            'slug' =>
                $this->slug,

            'views' =>
                (int) $this->views,

            /**
             * Текущий перевод.
             */
            'translation' => $translation
                ? new SchoolTrackTranslationResource(
                    $translation
                )
                : null,

            /**
             * Все переводы.
             *
             * Они нужны Create/Edit
             * TranslationTabs.
             */
            'translations' =>
                SchoolTrackTranslationResource::collection(
                    $this->whenLoaded(
                        'translations'
                    )
                ),

            /**
             * Изображения.
             *
             * Controller обязан загрузить:
             * images.media.
             */
            'images' =>
                SchoolTrackImageResource::collection(
                    $this->whenLoaded(
                        'images'
                    )
                ),

            /**
             * Служебные поля.
             */
            'is_root' =>
                $this->parent_id === null,

            'created_at' =>
                $this->created_at?->toISOString(),

            'updated_at' =>
                $this->updated_at?->toISOString(),
        ];
    }
}

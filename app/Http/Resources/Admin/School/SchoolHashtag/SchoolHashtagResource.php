<?php

namespace App\Http\Resources\Admin\School\SchoolHashtag;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolHashtagResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $currentLocale = app()->getLocale();

        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        /**
         * Текущий перевод.
         *
         * Все переводы уже должны быть
         * загружены контроллером одним запросом.
         *
         * Дополнительных SQL-запросов
         * Resource не выполняет.
         */
        $currentTranslation =
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
                (int) $this->id,

            /**
             * Основные поля.
             */
            'sort' =>
                (int) $this->sort,

            'activity' =>
                (bool) $this->activity,

            'slug' =>
                $this->slug,

            'color' =>
                $this->color,

            'views' =>
                (int) $this->views,

            'likes' =>
                (int) $this->likes,

            /**
             * Текущий перевод:
             *
             * current locale
             * → fallback locale
             * → первый доступный.
             */
            'translation' => $currentTranslation
                ? new SchoolHashtagTranslationResource(
                    $currentTranslation
                )
                : null,

            /**
             * Все переводы.
             *
             * Нужны Create/Edit-форме
             * для TranslationTabs.
             */
            'translations' =>
                SchoolHashtagTranslationResource::collection(
                    $this->whenLoaded(
                        'translations'
                    )
                ),

            /**
             * Counts.
             *
             * Возвращаются только если
             * контроллер их действительно
             * предварительно загрузил.
             */
            'courses_count' => $this->when(
                isset($this->courses_count),
                fn () => (int) $this->courses_count
            ),

            'modules_count' => $this->when(
                isset($this->modules_count),
                fn () => (int) $this->modules_count
            ),

            'lessons_count' => $this->when(
                isset($this->lessons_count),
                fn () => (int) $this->lessons_count
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

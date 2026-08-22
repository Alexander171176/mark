<?php

namespace App\Http\Resources\Admin\School\SchoolHashtag;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolHashtagSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $currentLocale = app()->getLocale();

        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        /**
         * Admin Index обычно заранее загружает
         * только перевод выбранной локали.
         *
         * В других Admin-сценариях relation
         * может содержать несколько переводов.
         *
         * Никаких дополнительных SQL-запросов
         * Resource не выполняет.
         */
        $translation = $this->relationLoaded('translations')
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

            /**
             * Перевод.
             *
             * Единый контракт переводимых
             * сущностей Admin:
             *
             * entity.translation.*
             */
            'translation' => $translation
                ? [
                    'locale' =>
                        $translation->locale,

                    'name' =>
                        $translation->name,

                    'short' =>
                        $translation->short,

                    'description' =>
                        $translation->description,
                ]
                : null,

            /**
             * Статистика.
             */
            'views' =>
                (int) $this->views,

            'likes' =>
                (int) $this->likes,

            /**
             * Counts.
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
             * Даты нужны frontend-сортировке.
             */
            'created_at' =>
                $this->created_at?->toISOString(),

            'updated_at' =>
                $this->updated_at?->toISOString(),
        ];
    }
}

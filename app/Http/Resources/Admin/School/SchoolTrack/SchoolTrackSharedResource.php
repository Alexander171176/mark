<?php

namespace App\Http\Resources\Admin\School\SchoolTrack;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolTrackSharedResource extends JsonResource
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
         * Никаких SQL-запросов Resource
         * самостоятельно не выполняет.
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

        /**
         * Первое изображение
         * по pivot order.
         */
        $cover = $this->relationLoaded('images')
            ? $this->images->first()
            : null;

        return [
            'id' =>
                (int) $this->id,

            'parent_id' =>
                $this->parent_id !== null
                    ? (int) $this->parent_id
                    : null,

            /**
             * Основные поля.
             */
            'sort' =>
                (int) $this->sort,

            'activity' =>
                (bool) $this->activity,

            'slug' =>
                $this->slug,

            'views' =>
                (int) $this->views,

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
             * Родитель.
             *
             * Использует тот же
             * translation-контракт.
             */
            'parent' => $this->whenLoaded(
                'parent',
                function () use (
                    $currentLocale,
                    $fallbackLocale
                ) {
                    if (!$this->parent) {
                        return null;
                    }

                    $parentTranslation =
                        $this->parent
                            ->relationLoaded('translations')
                            ? (
                        $this->parent
                            ->translations
                            ->firstWhere(
                                'locale',
                                $currentLocale
                            )
                            ?: $this->parent
                            ->translations
                            ->firstWhere(
                                'locale',
                                $fallbackLocale
                            )
                            ?: $this->parent
                                ->translations
                                ->first()
                        )
                            : null;

                    return [
                        'id' =>
                            (int) $this->parent->id,

                        'parent_id' =>
                            $this->parent->parent_id !== null
                                ? (int) $this->parent->parent_id
                                : null,

                        'slug' =>
                            $this->parent->slug,

                        'translation' => $parentTranslation
                            ? [
                                'locale' =>
                                    $parentTranslation->locale,

                                'name' =>
                                    $parentTranslation->name,
                            ]
                            : null,
                    ];
                }
            ),

            /**
             * Изображения.
             *
             * Controller обязан загрузить
             * images.media.
             */
            'images' =>
                SchoolTrackImageResource::collection(
                    $this->whenLoaded(
                        'images'
                    )
                ),

            /**
             * Cover.
             */
            'thumbnail_url' =>
                $cover?->thumb_url
                    ?: $cover?->webp_url
                    ?: $cover?->image_url,

            /**
             * Рекурсивные дочерние элементы.
             *
             * Благодаря self::collection()
             * весь tree автоматически получает
             * тот же translation-контракт.
             */
            'children' =>
                self::collection(
                    $this->whenLoaded(
                        'children'
                    )
                ),

            /**
             * Counts.
             */
            'likes_count' => $this->when(
                isset($this->likes_count),
                fn () => (int) $this->likes_count
            ),

            'children_count' => $this->when(
                isset($this->children_count),
                fn () => (int) $this->children_count
            ),

            'courses_count' => $this->when(
                isset($this->courses_count),
                fn () => (int) $this->courses_count
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

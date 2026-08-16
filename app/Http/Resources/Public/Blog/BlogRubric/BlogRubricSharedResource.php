<?php

namespace App\Http\Resources\Public\Blog\BlogRubric;

use App\Http\Resources\Admin\Blog\BlogRubric\BlogRubricImageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogRubricSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        /**
         * В Public-запросе translations
         * содержит максимум:
         *
         * - текущую локаль;
         * - fallback ru.
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

        return [
            'id' => $this->id,

            /**
             * Иерархия.
             */
            'parent_id' => $this->parent_id,
            'level' => (int) $this->level,

            /**
             * Основные публичные поля.
             */
            'sort' => (int) $this->sort,
            'icon' => $this->icon,
            'url' => $this->url,
            'views' => (int) $this->views,

            /**
             * Перевод текущей локали
             * или fallback ru.
             */
            'translation' => $translation
                ? [
                    'locale' => $translation->locale,
                    'title' => $translation->title,
                    'subtitle' => $translation->subtitle,
                    'short' => $translation->short,
                ]
                : null,

            /**
             * Автор.
             *
             * Email нужен frontend-поиску.
             */
            'owner' => $this->whenLoaded(
                'owner',
                function () {
                    return [
                        'id' => $this->owner?->id,
                        'name' => $this->owner?->name,
                        'email' => $this->owner?->email,
                        'profile_photo_url' =>
                            $this->owner?->profile_photo_url,
                    ];
                }
            ),

            /**
             * Изображения.
             *
             * Контроллер должен заранее
             * загрузить images.media.
             */
            'images' => BlogRubricImageResource::collection(
                $this->whenLoaded('images')
            ),

            /**
             * Количество статей.
             */
            'articles_count' => $this->when(
                isset($this->articles_count),
                fn () => (int) $this->articles_count
            ),

            /**
             * Дети, если relation загружена.
             */
            'children' => self::collection(
                $this->whenLoaded('children')
            ),

            /**
             * Используется frontend-сортировкой
             * по дате.
             */
            'created_at' =>
                $this->created_at?->toISOString(),
        ];
    }
}

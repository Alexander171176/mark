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
         * - fallback locale.
         *
         * Resource сам запросы в БД не выполняет.
         */
        $translation = $this->relationLoaded('translations')
            ? $this->translationOrFallback(
                $locale,
                $fallbackLocale
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
             * Лёгкий перевод текущей локали
             * или fallback.
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
             * Только поля, реально используемые
             * публичным интерфейсом.
             */
            'owner' => $this->whenLoaded(
                'owner',
                function () {
                    return [
                        'id' => $this->owner?->id,
                        'name' => $this->owner?->name,
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
             * Количество только Public-статей.
             */
            'articles_count' => $this->when(
                isset($this->articles_count),
                fn () => (int) $this->articles_count
            ),

            /**
             * Дочерние рубрики,
             * если relation загружена.
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

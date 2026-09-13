<?php

namespace App\Http\Resources\Public\Blog\BlogRubric;

use App\Http\Resources\Admin\Blog\BlogRubric\BlogRubricImageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogRubricResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        /**
         * Контроллер заранее загружает:
         *
         * current locale + fallback locale.
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
             * Полный перевод текущей локали
             * или fallback.
             */
            'translation' => $translation
                ? [
                    'locale' => $translation->locale,

                    'title' => $translation->title,
                    'subtitle' => $translation->subtitle,
                    'short' => $translation->short,
                    'description' => $translation->description,

                    'meta_title' => $translation->meta_title,
                    'meta_keywords' => $translation->meta_keywords,
                    'meta_desc' => $translation->meta_desc,
                ]
                : null,

            /**
             * Автор.
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
             */
            'images' => BlogRubricImageResource::collection(
                $this->whenLoaded('images')
            ),

            /**
             * Дочерние рубрики уже
             * в лёгком публичном формате.
             */
            'children' => BlogRubricSharedResource::collection(
                $this->whenLoaded('children')
            ),

            /**
             * Количество только Public-статей.
             *
             * Контроллер должен предварительно
             * рассчитать articles_count.
             */
            'articles_count' => $this->when(
                isset($this->articles_count),
                fn () => (int) $this->articles_count
            ),

            /**
             * Нужен frontend-сортировке
             * и отображению даты.
             */
            'created_at' =>
                $this->created_at?->toISOString(),
        ];
    }
}

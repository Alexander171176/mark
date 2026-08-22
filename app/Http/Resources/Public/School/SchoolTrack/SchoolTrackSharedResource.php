<?php

namespace App\Http\Resources\Public\School\SchoolTrack;

use App\Http\Resources\Admin\School\SchoolTrack\SchoolTrackImageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolTrackSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        /**
         * Controller / scopeForPublic()
         * заранее загружает максимум:
         *
         * current locale + fallback locale.
         */
        $translation =
            $this->relationLoaded('translations')
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

            'parent_id' =>
                $this->parent_id,

            'sort' =>
                (int) $this->sort,

            'slug' =>
                $this->slug,

            'views' =>
                (int) $this->views,

            /**
             * Единый Public-контракт перевода.
             */
            'translation' => $translation
                ? [
                    'locale' =>
                        $translation->locale,

                    'name' =>
                        $translation->name,

                    'short' =>
                        $translation->short,
                ]
                : null,

            /**
             * Изображения.
             *
             * Query обязан загрузить:
             * images.media.
             */
            'images' =>
                SchoolTrackImageResource::collection(
                    $this->whenLoaded(
                        'images'
                    )
                ),

            /**
             * Counts.
             */
            'children_count' => $this->when(
                isset($this->children_count),
                fn () =>
                (int) $this->children_count
            ),

            'courses_count' => $this->when(
                isset($this->courses_count),
                fn () =>
                (int) $this->courses_count
            ),

            'likes_count' => $this->when(
                isset($this->likes_count),
                fn () =>
                (int) $this->likes_count
            ),

            'images_count' => $this->when(
                isset($this->images_count),
                fn () =>
                (int) $this->images_count
            ),

            /**
             * Текущий пользователь.
             */
            'already_liked' =>
                (bool) ($this->already_liked ?? false),

            /**
             * Нужен frontend dateAsc/dateDesc.
             */
            'created_at' =>
                $this->created_at?->toISOString(),
        ];
    }
}

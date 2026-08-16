<?php

namespace App\Http\Resources\Public\Blog\BlogBanner;

use App\Http\Resources\Admin\Blog\BlogBanner\BlogBannerImageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogBannerSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        /**
         * Public-запрос заранее загружает
         * максимум две локали:
         *
         * - current locale;
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
             * Основные публичные поля.
             */
            'sort' => (int) $this->sort,

            'left' => (bool) $this->left,
            'main' => (bool) $this->main,
            'right' => (bool) $this->right,

            /**
             * Перевод текущей локали
             * или fallback ru.
             */
            'translation' => $translation
                ? [
                    'locale' => $translation->locale,
                    'title' => $translation->title,
                    'link' => $translation->link,
                    'short' => $translation->short,
                ]
                : null,

            /**
             * Изображения.
             *
             * Контроллер / Service должен
             * заранее загрузить images.media.
             */
            'images' => BlogBannerImageResource::collection(
                $this->whenLoaded('images')
            ),
        ];
    }
}

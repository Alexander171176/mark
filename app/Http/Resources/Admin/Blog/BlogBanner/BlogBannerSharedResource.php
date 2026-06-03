<?php

namespace App\Http\Resources\Admin\Blog\BlogBanner;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogBannerSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $currentLocale = app()->getLocale();

        $translation = $this->whenLoaded('translations', function () use ($currentLocale) {
            return $this->translations->firstWhere('locale', $currentLocale)
                ?: $this->translations->firstWhere('locale', config('app.fallback_locale', 'ru'))
                    ?: $this->translations->first();
        });

        $images = $this->whenLoaded('images', function () {
            return $this->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'order' => (int) ($image->pivot->order ?? $image->order ?? 0),
                    'image_url' => $image->image_url,
                    'webp_url' => $image->webp_url,
                    'thumb_url' => $image->thumb_url,
                    'alt' => $image->alt,
                    'caption' => $image->caption,
                ];
            })->values();
        });

        $thumbnailUrl = null;

        if ($this->relationLoaded('images') && $this->images?->count()) {
            $img = $this->images->first();

            $thumbnailUrl = $img->thumb_url
                ?: $img->webp_url
                    ?: $img->image_url;
        }

        return [
            'id' => $this->id,

            'title' => $translation?->title,
            'link' => $translation?->link,
            'short' => $translation?->short,
            'locale' => $translation?->locale,

            'activity' => (bool) $this->activity,

            'left' => (bool) $this->left,
            'main' => (bool) $this->main,
            'right' => (bool) $this->right,

            'thumbnail_url' => $thumbnailUrl,
            'images' => $images,
        ];
    }
}

<?php

namespace App\Http\Resources\Admin\Blog\BlogArticle;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogArticleSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $currentLocale = app()->getLocale();

        $translation = $this->whenLoaded('translations', function () use ($currentLocale) {
            return $this->translations->firstWhere('locale', $currentLocale)
                ?: $this->translations->firstWhere('locale', config('app.fallback_locale', 'ru'))
                    ?: $this->translations->first();
        });

        $thumbnailUrl = null;

        if ($this->relationLoaded('images')) {
            $firstImage = $this->images->first();
            $thumbnailUrl = $firstImage?->thumb_url ?? null;
        }

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,

            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,
            'left' => (bool) $this->left,
            'main' => (bool) $this->main,
            'right' => (bool) $this->right,

            'is_approved' => (int) $this->moderation_status === 1,

            'url' => $this->url,
            'img' => $this->img,

            'published_at' => $this->published_at?->format('Y-m-d'),
            'views' => (int) $this->views,

            'locale' => $translation?->locale,
            'title' => $translation?->title,
            'subtitle' => $translation?->subtitle,
            'short' => $translation?->short,
            'pseudonym' => $translation?->pseudonym,

            'thumbnail_url' => $thumbnailUrl,
        ];
    }
}

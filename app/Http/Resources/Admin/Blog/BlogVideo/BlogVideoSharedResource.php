<?php

namespace App\Http\Resources\Admin\Blog\BlogVideo;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogVideoSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $currentLocale = app()->getLocale();

        $translation = $this->whenLoaded('translations', function () use ($currentLocale) {
            return $this->translations->firstWhere('locale', $currentLocale)
                ?: $this->translations->firstWhere('locale', config('app.fallback_locale', 'ru'))
                    ?: $this->translations->first();
        });

        $cover = null;

        if ($this->relationLoaded('images') && $this->images?->count()) {
            $cover = $this->images->first();
        }

        return [
            'id' => $this->id,
            'locale' => $translation?->locale,
            'title' => $translation?->title,
            'url' => $this->url,

            'activity' => (bool) $this->activity,
            'is_private' => (bool) $this->is_private,
            'is_public' => (bool) $this->is_public,

            'moderation_status' => (int) $this->moderation_status,
            'is_approved' => (int) $this->moderation_status === 1,

            'published_at' => $this->published_at?->format('Y-m-d'),
            'source_type' => $this->source_type,

            'display_source' => match ($this->source_type) {
                'local'   => $this->video_url,
                'youtube' => $this->embed_url,
                'vimeo'   => $this->embed_url,
                'code'    => $this->embed_code,
                default   => null,
            },

            'cover_thumb_url' => $cover?->thumb_url,
            'cover_webp_url' => $cover?->webp_url,
            'cover_image_url' => $cover?->image_url,
        ];
    }
}

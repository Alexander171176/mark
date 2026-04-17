<?php

namespace App\Http\Resources\Admin\Blog\Article;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $thumbnailUrl = null;

        if ($this->relationLoaded('images')) {
            $firstImage = $this->images->first();
            $thumbnailUrl = $firstImage?->thumb_url ?? null;
        }

        return [
            'id'       => $this->id,
            'user_id'  => $this->user_id,

            'locale'   => $this->locale,
            'title'    => $this->title,
            'url'      => $this->url,
            'img'      => $this->img,

            'activity' => $this->activity,
            'is_approved' => (int) $this->moderation_status === 1,

            'published_at' => $this->published_at?->format('Y-m-d'),
            'views'        => $this->views,

            'thumbnail_url' => $thumbnailUrl,
        ];
    }
}

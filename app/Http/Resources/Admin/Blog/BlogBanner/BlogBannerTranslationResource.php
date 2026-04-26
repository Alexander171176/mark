<?php

namespace App\Http\Resources\Admin\Blog\BlogBanner;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogBannerTranslationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'banner_id' => $this->banner_id,
            'locale' => $this->locale,

            'title' => $this->title,
            'link' => $this->link,
            'short' => $this->short,

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}

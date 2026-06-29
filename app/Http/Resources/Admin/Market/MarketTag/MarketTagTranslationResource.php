<?php

namespace App\Http\Resources\Admin\Market\MarketTag;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketTagTranslationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'market_tag_id' => $this->market_tag_id,
            'locale' => $this->locale,

            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'short' => $this->short,
            'description' => $this->description,

            'meta_title' => $this->meta_title,
            'meta_keywords' => $this->meta_keywords,
            'meta_desc' => $this->meta_desc,

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}

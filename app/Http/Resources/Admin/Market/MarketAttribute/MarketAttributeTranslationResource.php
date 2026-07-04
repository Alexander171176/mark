<?php

namespace App\Http\Resources\Admin\Market\MarketAttribute;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketAttributeTranslationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'market_attribute_id' => $this->market_attribute_id,
            'locale' => $this->locale,

            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'short' => $this->short,
            'description' => $this->description,

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}

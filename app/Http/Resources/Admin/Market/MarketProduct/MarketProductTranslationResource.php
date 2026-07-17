<?php

namespace App\Http\Resources\Admin\Market\MarketProduct;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketProductTranslationResource extends JsonResource
{
    /**
     * Преобразование перевода товара в массив.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (int) $this->id,
            'market_product_id' => (int) $this->market_product_id,

            'locale' => $this->locale,

            /** Локализуемые поля */
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'short' => $this->short,
            'description' => $this->description,

            /** SEO */
            'meta_title' => $this->meta_title,
            'meta_keywords' => $this->meta_keywords,
            'meta_desc' => $this->meta_desc,

            /** Timestamps */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}

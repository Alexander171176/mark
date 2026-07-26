<?php

namespace App\Http\Resources\Admin\Market\MarketProductBundle;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketProductBundleTranslationResource extends JsonResource
{
    /**
     * Преобразование перевода комплекта товаров в массив.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            /** Основные идентификаторы */
            'id' => (int) $this->id,

            'market_product_bundle_id' =>
                (int) $this->market_product_bundle_id,

            /** Локаль перевода */
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

            /** Даты */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}

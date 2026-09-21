<?php

namespace App\Http\Resources\Public\Market\MarketBrand;

use App\Http\Resources\Admin\Market\MarketBrand\MarketBrandImageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketBrandResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $translation = $this->translationOrFallback();

        return [
            'id' => (int) $this->id,

            /** Основные публичные данные */
            'url' => $this->url,
            'website' => $this->website,
            'logo' => $this->logo,
            'icon' => $this->icon,
            'social_links' => $this->social_links,

            /** Статистика */
            'views' => (int) $this->views,

            /** Счётчики */
            'products_count' => $this->whenCounted(
                'products'
            ),

            'images_count' => $this->whenCounted(
                'images'
            ),

            /** Разрешённый публичный перевод */
            'translation' => $translation
                ? new MarketBrandTranslationResource(
                    $translation
                )
                : null,

            /** Изображения */
            'images' => MarketBrandImageResource::collection(
                $this->whenLoaded('images')
            ),
        ];
    }
}

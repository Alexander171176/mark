<?php

namespace App\Http\Resources\Public\Market\MarketBrand;

use App\Http\Resources\Admin\Market\MarketBrand\MarketBrandImageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketBrandSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $translation = $this->translationOrFallback();

        return [
            'id' => (int) $this->id,
            'sort' => (int) $this->sort,

            'url' => $this->url,
            'website' => $this->website,
            'logo' => $this->logo,
            'icon' => $this->icon,

            'views' => (int) $this->views,

            'products_count' => $this->whenCounted(
                'products'
            ),

            'translation' => $translation
                ? new MarketBrandTranslationResource(
                    $translation
                )
                : null,

            'images' => MarketBrandImageResource::collection(
                $this->whenLoaded('images')
            ),
        ];
    }
}

<?php

namespace App\Http\Resources\Public\Market\MarketTag;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketTagResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $translation = $this->translationOrFallback();

        return [
            'id' => (int) $this->id,

            'url' => $this->url,
            'icon' => $this->icon,
            'color' => $this->color,

            'views' => (int) $this->views,

            'products_count' => $this->whenCounted(
                'products'
            ),

            'translation' => $translation
                ? new MarketTagTranslationResource(
                    $translation
                )
                : null,
        ];
    }
}

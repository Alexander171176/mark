<?php

namespace App\Http\Resources\Admin\Market\MarketCompany;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketCompanyTranslationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'market_company_id' => $this->market_company_id,
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

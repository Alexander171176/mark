<?php

namespace App\Http\Resources\Admin\Market\MarketStorefront;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketStorefrontResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'company_id' => $this->company_id,

            'sort' => $this->sort,
            'activity' => (bool) $this->activity,
            'is_main' => (bool) $this->is_main,

            'slug' => $this->slug,

            'domain' => $this->domain,
            'subdomain' => $this->subdomain,
            'primary_host' => $this->primary_host,

            'default_locale' => $this->default_locale,
            'default_currency_id' => $this->default_currency_id,

            'note' => $this->note,

            'company' => $this->whenLoaded('company', function () {
                return [
                    'id' => $this->company?->id,
                    'name' => $this->company?->name,
                    'brand_name' => $this->company?->brand_name,
                    'slug' => $this->company?->slug,
                ];
            }),

            'default_currency' => $this->whenLoaded('defaultCurrency', function () {
                return [
                    'id' => $this->defaultCurrency?->id,
                    'title' => $this->defaultCurrency?->title ?? null,
                    'code' => $this->defaultCurrency?->code ?? null,
                    'symbol' => $this->defaultCurrency?->symbol ?? null,
                ];
            }),

            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

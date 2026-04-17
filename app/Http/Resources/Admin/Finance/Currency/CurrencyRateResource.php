<?php

namespace App\Http\Resources\Admin\Finance\Currency;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrencyRateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // если base/quote подгружены — показываем code, иначе id
        $base  = $this->relationLoaded('base')  ? ($this->base?->code  ?? 'BASE')  : (int) $this->base_currency_id;
        $quote = $this->relationLoaded('quote') ? ($this->quote?->code ?? 'QUOTE') : (int) $this->quote_currency_id;

        return [
            'id'                => (int) $this->id,

            'base_currency_id'  => (int) $this->base_currency_id,
            'quote_currency_id' => (int) $this->quote_currency_id,

            // история: rate храним decimal => отдаём строкой (без потери точности)
            'rate'              => (string) $this->rate,

            'provider'          => ($this->provider !== null && trim((string) $this->provider) !== '')
                ? (string) $this->provider
                : null,

            'is_manual'         => (bool) $this->is_manual,
            'fetched_at'        => optional($this->fetched_at)?->toISOString(),

            'created_at'        => optional($this->created_at)?->toISOString(),
            'updated_at'        => optional($this->updated_at)?->toISOString(),

            // вложенные валюты (для UI)
            'base_currency'     => $this->whenLoaded('base', fn () => new CurrencySelectResource($this->base)),
            'quote_currency'    => $this->whenLoaded('quote', fn () => new CurrencySelectResource($this->quote)),

            // для таблиц
            'pair'              => "{$base}/{$quote}",
        ];
    }
}

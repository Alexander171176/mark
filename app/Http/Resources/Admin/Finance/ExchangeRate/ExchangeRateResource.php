<?php

namespace App\Http\Resources\Admin\Finance\ExchangeRate;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExchangeRateResource extends JsonResource
{
    /**
     * Представление курса валют.
     *
     * @return array<string,mixed>
     */
    public function toArray(Request $request): array
    {
        $now = now();

        $isCurrent = (is_null($this->valid_from) || $this->valid_from->lte($now))
            && (is_null($this->valid_to) || $this->valid_to->gte($now))
            && (bool)$this->activity;

        return [
            'id'             => $this->id,
            'base_currency'  => $this->base_currency,
            'quote_currency' => $this->quote_currency,
            'pair'           => "{$this->base_currency}/{$this->quote_currency}",

            // Курс как строка (без потери точности) и как float для удобства UI
            'rate'           => (string) $this->rate,
            'rate_float'     => (float) $this->rate,

            'provider'       => $this->provider,
            'fetched_at'     => optional($this->fetched_at)?->toISOString(),
            'valid_from'     => optional($this->valid_from)?->toISOString(),
            'valid_to'       => optional($this->valid_to)?->toISOString(),
            'activity'       => (bool) $this->activity,
            'is_current'     => $isCurrent,

            'meta'           => $this->meta,

            'created_at'     => optional($this->created_at)?->toISOString(),
            'updated_at'     => optional($this->updated_at)?->toISOString(),
            'deleted_at'     => optional($this->deleted_at)?->toISOString(),
        ];
    }
}

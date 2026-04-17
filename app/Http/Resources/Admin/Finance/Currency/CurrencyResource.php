<?php

namespace App\Http\Resources\Admin\Finance\Currency;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrencyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => (int) $this->id,
            'sort'          => (int) $this->sort,

            // справочник: ISO-4217
            'code'          => (string) $this->code,
            'name'          => (string) $this->name,

            'symbol'        => $this->symbol !== null ? (string) $this->symbol : null,
            'precision'     => (int) $this->precision,
            'symbol_first'  => (bool) $this->symbol_first,
            'thousands_sep' => (string) $this->thousands_sep,
            'decimal_sep'   => (string) $this->decimal_sep,

            'activity'      => (bool) $this->activity,
            'is_default'    => (bool) $this->is_default,
            'set_default_at'=> optional($this->set_default_at)?->toISOString(),

            'created_at'    => optional($this->created_at)?->toISOString(),
            'updated_at'    => optional($this->updated_at)?->toISOString(),
        ];
    }
}

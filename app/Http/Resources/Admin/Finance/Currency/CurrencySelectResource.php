<?php

namespace App\Http\Resources\Admin\Finance\Currency;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrencySelectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $code = (string) ($this->code ?? '');
        $name = (string) ($this->name ?? '');

        return [
            'id'    => (int) $this->id,
            'code'  => $code,
            'name'  => $name,
            'label' => trim($code . ' — ' . $name),
        ];
    }
}

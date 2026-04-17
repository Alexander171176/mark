<?php

namespace App\Http\Resources\Admin\Finance\ProviderAccount;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderAccountResource extends JsonResource
{
    /**
     * Представление учётной записи платёжного провайдера.
     *
     * @return array<string,mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'provider'   => $this->provider,         // stripe|paypal|yookassa|...
            'title'      => $this->title,
            'mode'       => $this->mode,             // test|live
            'account_id' => $this->account_id,
            'public_key' => $this->public_key,

            // Не раскрываем секреты целиком
            'has_secret_key'    => !empty($this->secret_key),
            'has_webhook_secret'=> !empty($this->webhook_secret),

            'supported_currencies' => $this->supported_currencies, // ["USD","EUR",...]
            'supported_countries'  => $this->supported_countries,  // ["US","RU",...]
            'config'               => $this->config,               // произвольные настройки

            'activity'   => (bool) $this->activity,
            'is_default' => (bool) $this->is_default,

            'display_name'=> $this->when(isset($this->display_name), $this->display_name),

            'created_by' => $this->whenLoaded('creator', fn () => [
                'id'   => $this->creator->id,
                'name' => $this->creator->name,
                'email'=> $this->creator->email,
            ]),
            'updated_by' => $this->whenLoaded('updater', fn () => [
                'id'   => $this->updater->id,
                'name' => $this->updater->name,
                'email'=> $this->updater->email,
            ]),

            'created_at' => optional($this->created_at)?->toISOString(),
            'updated_at' => optional($this->updated_at)?->toISOString(),
            'deleted_at' => optional($this->deleted_at)?->toISOString(),
        ];
    }
}

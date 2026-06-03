<?php

namespace App\Http\Resources\Admin\School\SchoolProviderAccount;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolProviderAccountResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'provider' => $this->provider,
            'title' => $this->title,
            'mode' => $this->mode,
            'account_id' => $this->account_id,
            'public_key' => $this->public_key,

            'has_secret_key' => !empty($this->secret_key),
            'has_webhook_secret' => !empty($this->webhook_secret),

            'supported_currencies' => $this->supported_currencies,
            'supported_countries' => $this->supported_countries,
            'config' => $this->config,

            'activity' => (bool) $this->activity,
            'is_default' => (bool) $this->is_default,
            'display_name' => $this->display_name,

            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,

            'creator' => $this->whenLoaded('creator', fn () => [
                'id' => $this->creator->id,
                'name' => $this->creator->name,
                'email' => $this->creator->email,
            ]),

            'updater' => $this->whenLoaded('updater', fn () => [
                'id' => $this->updater->id,
                'name' => $this->updater->name,
                'email' => $this->updater->email,
            ]),

            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}

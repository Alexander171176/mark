<?php

namespace App\Http\Resources\Admin\School\SchoolProviderAccount;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolProviderAccountSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'provider' => $this->provider,
            'title' => $this->title,
            'mode' => $this->mode,
            'account_id' => $this->account_id,

            'activity' => (bool) $this->activity,
            'is_default' => (bool) $this->is_default,
            'display_name' => $this->display_name,
        ];
    }
}

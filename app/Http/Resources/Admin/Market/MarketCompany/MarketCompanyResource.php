<?php

namespace App\Http\Resources\Admin\Market\MarketCompany;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketCompanyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'owner_user_id' => $this->owner_user_id,

            'sort' => $this->sort,
            'activity' => (bool) $this->activity,

            'name' => $this->name,
            'brand_name' => $this->brand_name,
            'legal_name' => $this->legal_name,

            'slug' => $this->slug,
            'external_id' => $this->external_id,

            'company_type' => $this->company_type,
            'tax_regime' => $this->tax_regime,
            'bin_iin' => $this->bin_iin,

            'email' => $this->email,
            'phone' => $this->phone,

            'messenger_type' => $this->messenger_type,
            'messenger_contact' => $this->messenger_contact,

            'country' => $this->country,
            'city' => $this->city,
            'legal_address' => $this->legal_address,
            'actual_address' => $this->actual_address,

            'owner' => $this->whenLoaded('owner', function () {
                return [
                    'id' => $this->owner?->id,
                    'name' => $this->owner?->name,
                    'email' => $this->owner?->email,
                ];
            }),

            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

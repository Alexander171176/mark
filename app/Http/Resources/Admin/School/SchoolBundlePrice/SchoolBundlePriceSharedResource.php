<?php

namespace App\Http\Resources\Admin\School\SchoolBundlePrice;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolBundlePriceSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'school_bundle_id' => $this->school_bundle_id,
            'currency_id' => $this->currency_id,

            'price' => (string) $this->price,
            'sale_price' => $this->sale_price !== null ? (string) $this->sale_price : null,
            'compare_at_price' => $this->compare_at_price !== null ? (string) $this->compare_at_price : null,

            'effective_price' => (string) $this->effective_price,
            'has_discount' => (bool) $this->has_discount,
            'discount_amount' => $this->discount_amount !== null ? (string) $this->discount_amount : null,
            'discount_percent' => $this->discount_percent !== null ? (float) $this->discount_percent : null,

            'activity' => (bool) $this->activity,
            'sort' => (int) $this->sort,
        ];
    }
}

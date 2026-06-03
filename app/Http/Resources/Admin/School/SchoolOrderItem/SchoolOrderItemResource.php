<?php

namespace App\Http\Resources\Admin\School\SchoolOrderItem;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolOrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'school_order_id' => $this->school_order_id,

            'type' => $this->type,

            'purchasable_type' => $this->purchasable_type,
            'purchasable_id' => $this->purchasable_id,

            'title' => $this->title,
            'sku' => $this->sku,
            'unit_name' => $this->unit_name,

            'currency' => $this->currency,
            'quantity' => (int) $this->quantity,
            'unit_price' => (string) $this->unit_price,
            'discount' => (string) $this->discount,
            'total' => (string) $this->total,

            'attributes' => $this->attributes,
            'meta' => $this->meta,

            'purchasable' => $this->whenLoaded('purchasable', function () {
                $model = $this->purchasable;

                if (!$model) {
                    return null;
                }

                return [
                    'id' => $model->id,
                    'type' => $this->purchasable_type,
                    'title' => $model->translation?->title
                        ?? $model->title
                            ?? $model->name
                            ?? null,
                    'slug' => $model->slug ?? null,
                ];
            }),

            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}

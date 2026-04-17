<?php

namespace App\Http\Resources\Admin\Finance\OrderItem;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Представление позиции заказа для админ-API.
     *
     * @return array<string,mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->id,
            'order_id' => $this->order_id,

            // Тип покупаемой сущности (короткий алиас из morphMap)
            'type' => $this->type, // accessor -> purchasable_type (course|bundle|subscription_plan|product и т.п.)

            // Полиморфный ID
            'purchasable_type' => $this->purchasable_type,
            'purchasable_id'   => $this->purchasable_id,

            // Снапшот данных позиции
            'title'     => $this->title,
            'sku'       => $this->sku,
            'unit_name' => $this->unit_name,

            // Деньги и количество
            'currency'   => $this->currency,
            'quantity'   => (int) $this->quantity,
            'unit_price' => (string) $this->unit_price,
            'discount'   => (string) $this->discount,
            'total'      => (string) $this->total,

            // Атрибуты и мета
            'attributes' => $this->attributes,
            'meta'       => $this->meta,

            // Связанная сущность (если загружена через ->with('items.purchasable'))
            'purchasable' => $this->whenLoaded('purchasable', function () {
                $model = $this->purchasable;

                if (! $model) {
                    return null;
                }

                return [
                    'id'    => $model->id,
                    'type'  => $this->purchasable_type, // course|bundle|subscription_plan|product и т.п.
                    // Пытаемся взять title/name/slug, не завязываясь жёстко на конкретный класс
                    'title' => $model->title ?? $model->name ?? null,
                    'slug'  => $model->slug  ?? null,
                ];
            }),

            'created_at' => optional($this->created_at)?->toISOString(),
            'updated_at' => optional($this->updated_at)?->toISOString(),
        ];
    }
}

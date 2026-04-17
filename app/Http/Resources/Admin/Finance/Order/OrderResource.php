<?php

namespace App\Http\Resources\Admin\Finance\Order;

use App\Http\Resources\Admin\Finance\OrderItem\OrderItemResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Представление заказа для админ-API.
     *
     * @return array<string,mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->id,
            'user_id'  => $this->user_id,
            'number'   => $this->number,

            /* ============================
             * Модуль 2 — покупатель (физ/юр)
             * ============================ */
            'buyer_name'      => $this->buyer_name,
            'buyer_email'     => $this->buyer_email,
            'buyer_phone'     => $this->buyer_phone,
            'billing_company' => $this->billing_company,
            'billing_tax_id'  => $this->billing_tax_id,
            'billing_address' => $this->billing_address,

            /* ============================
             * Модуль 3 — адрес и доставка
             * ============================ */
            'shipping_address'        => $this->shipping_address,
            'shipping_address_parts'  => $this->shipping_address_parts,
            'delivery_method_id'      => $this->delivery_method_id,
            'delivery_cost'           => $this->delivery_cost !== null ? (string) $this->delivery_cost : null,
            'delivery_options'        => $this->delivery_options,
            'delivery_interval'       => $this->delivery_interval,
            'warehouse'               => $this->warehouse,
            'delivery_date'           => optional($this->delivery_date)?->toISOString(),

            /* ============================
             * Модуль 4 — оплата
             * ============================ */
            'is_paid'            => (bool) $this->is_paid || $this->payment_status === 'paid',
            'paid_at'            => optional($this->paid_at)?->toISOString(),
            'payment_method_id'  => $this->payment_method_id,
            'payment_method'     => $this->payment_method,
            'payment_provider'   => $this->payment_provider,
            'payment_reference'  => $this->payment_reference,
            'confirmation_code'  => $this->confirmation_code,
            'confirmation_status'=> $this->confirmation_status,
            'failure_reason'     => $this->failure_reason,

            /* ============================
             * Модуль 5 — мультивалютность
             * ============================ */
            'currency'              => $this->currency,
            'subtotal'              => (string) $this->subtotal,
            'discount_total'        => (string) $this->discount_total,
            'tax_total'             => (string) $this->tax_total,
            'total'                 => (string) $this->total,
            'total_shop_currency'   => $this->total_shop_currency !== null ? (string) $this->total_shop_currency : null,
            'delivery_shop_currency'=> $this->delivery_shop_currency !== null ? (string) $this->delivery_shop_currency : null,

            /* ============================
             * Модуль 6 — статусы
             * ============================ */
            'status'         => $this->status,          // new|processing|delivered|cancelled|refunded|completed
            'payment_status' => $this->payment_status,  // pending|paid|failed|refunded|partial

            // Удобные вычисляемые флаги
            'is_cancelled'   => $this->status === 'cancelled',
            'is_refunded'    => in_array($this->payment_status, ['refunded', 'partial'], true),

            /* ============================
             * Модуль 7 — контент заказа (JSON-снапшот, если используешь)
             * ============================ */
            'items_snapshot' => $this->items,
            'meta'           => $this->meta,

            /* ====================================
             * Модуль 8 — комментарии
             * ===================================*/
            'user_comment'    => $this->user_comment,
            'manager_comment' => $this->manager_comment,

            /* ============================
             * Модуль 9 — интеграции
             * ============================ */
            'external_id' => $this->external_id,
            'exported_at' => optional($this->exported_at)?->toISOString(),

            /* ============================
             * Модуль 10 — тех.данные
             * ============================ */
            'client_ip'   => $this->client_ip,
            'user_agent'  => $this->user_agent,
            'public_hash' => $this->public_hash,

            /* ============================
             * Таймстемпы
             * ============================ */
            'created_at'  => optional($this->created_at)?->toISOString(),
            'updated_at'  => optional($this->updated_at)?->toISOString(),
            'deleted_at'  => optional($this->deleted_at)?->toISOString(),

            /* ============================
             * Связи (если загружены)
             * ============================ */

            // Краткая инфа о пользователе
            'user' => $this->whenLoaded('user', fn () => [
                'id'    => $this->user->id,
                'name'  => $this->user->name,
                'email' => $this->user->email,
            ]),

            // Позиции заказа (нормализованные)
            'items' => $this->whenLoaded('items', fn () =>
            OrderItemResource::collection($this->items)
            ),

            // Платежи
            'payments' => $this->whenLoaded('payments', fn () =>
            $this->payments->map(fn ($p) => [
                'id'         => $p->id,
                'status'     => $p->status, // pending|succeeded|failed|refunded ...
                'amount'     => (string) $p->amount,
                'currency'   => $p->currency,
                'provider'   => $p->provider,
                'created_at' => optional($p->created_at)?->toISOString(),
            ])
            ),

            // Возвраты
            'refunds' => $this->whenLoaded('refunds', fn () =>
            $this->refunds->map(fn ($r) => [
                'id'         => $r->id,
                'amount'     => (string) $r->amount,
                'currency'   => $r->currency,
                'reason'     => $r->reason,
                'created_at' => optional($r->created_at)?->toISOString(),
            ])
            ),
        ];
    }
}

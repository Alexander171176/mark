<?php

namespace App\Http\Resources\Admin\Finance\Payout;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PayoutResource extends JsonResource
{
    /**
     * Представление выплаты преподавателю.
     *
     * @return array<string,mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'number'               => $this->number,
            'status'               => $this->status,         // pending|processing|paid|failed|cancelled
            'method'               => $this->method,         // manual|bank_wire|...
            'currency'             => $this->currency,

            'period_start'         => optional($this->period_start)?->toDateString(),
            'period_end'           => optional($this->period_end)?->toDateString(),

            'amount_gross'         => (string) $this->amount_gross,
            'fee_total'            => (string) $this->fee_total,
            'tax_total'            => (string) $this->tax_total,
            'amount_net'           => (string) $this->amount_net,

            // Полезные вычисляемые/удобные поля
            'computed_net'         => $this->when(isset($this->computed_net), $this->computed_net),
            'paid_at'              => optional($this->paid_at)?->toISOString(),

            // Связи (подгружаем по необходимости)
            'instructor' => $this->whenLoaded('instructor', fn () => [
                'id'    => $this->instructor->id,
                'user_id' => $this->instructor->user_id,
                'title' => $this->instructor->title,
                'slug'  => $this->instructor->slug,
            ]),
            'provider_account' => $this->whenLoaded('providerAccount', fn () => [
                'id'       => $this->providerAccount->id,
                'provider' => $this->providerAccount->provider,
                'mode'     => $this->providerAccount->mode,
                'title'    => $this->providerAccount->title,
            ]),
            'items' => $this->whenLoaded('items', fn () =>
            $this->items->map(fn ($it) => [
                'id'          => $it->id,
                'payout_id'   => $it->payout_id,
                'source_type' => $it->source_type ?? null,
                'source_id'   => $it->source_id ?? null,
                'amount'      => (string) $it->amount,
                'currency'    => $it->currency ?? $this->currency,
                'meta'        => $it->meta,
                'created_at'  => optional($it->created_at)?->toISOString(),
            ])
            ),

            'notes'                => $this->notes,
            'meta'                 => $this->meta,

            'created_by' => $this->whenLoaded('creator', fn () => [
                'id' => $this->creator->id, 'name' => $this->creator->name,
            ]),
            'updated_by' => $this->whenLoaded('updater', fn () => [
                'id' => $this->updater->id, 'name' => $this->updater->name,
            ]),

            'created_at'           => optional($this->created_at)?->toISOString(),
            'updated_at'           => optional($this->updated_at)?->toISOString(),
            'deleted_at'           => optional($this->deleted_at)?->toISOString(),
        ];
    }
}

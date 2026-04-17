<?php

namespace App\Http\Resources\Admin\Finance\PaymentMethod;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentMethodResource extends JsonResource
{
    /**
     * Представление способа оплаты для админ-API.
     *
     * @return array<string,mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'code'                => $this->code,          // системный код
            'name'                => $this->name,          // отображаемое имя
            'provider'            => $this->provider,      // stripe|yookassa|paypal|manual|...
            'type'                => $this->type,          // card|bank_transfer|ewallet|cash|invoice|other

            'supports_refund'     => (bool) $this->supports_refund,
            'supports_recurring'  => (bool) $this->supports_recurring,

            'activity'            => (bool) $this->activity,
            'sort'                => (int) $this->sort,
            'meta'                => $this->meta,

            // агрегаты, если подгружены ->withCount()
            'payments_count'           => $this->whenCounted('payments'),
            'user_payment_methods_count' => $this->whenCounted('userPaymentMethods'),

            'created_at'          => optional($this->created_at)?->toISOString(),
            'updated_at'          => optional($this->updated_at)?->toISOString(),
            'deleted_at'          => optional($this->deleted_at)?->toISOString(),
        ];
    }
}

<?php

namespace App\Http\Resources\Admin\Finance\UserPaymentMethod;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserPaymentMethodResource extends JsonResource
{
    /**
     * Представление сохранённого способа оплаты пользователя (для админ-API).
     *
     * @return array<string,mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                        => $this->id,
            'user_id'                   => $this->user_id,
            'payment_method_id'         => $this->payment_method_id,

            'provider'                  => $this->provider,                   // stripe|yookassa|paypal|...
            'provider_customer_id'      => $this->provider_customer_id,
            'provider_payment_method_id'=> $this->provider_payment_method_id,

            // Карточные реквизиты (нечувствительные)
            'brand'        => $this->brand,     // Visa/Mastercard
            'last4'        => $this->last4,     // последние 4
            'exp_month'    => $this->exp_month,
            'exp_year'     => $this->exp_year,
            'country'      => $this->country,   // ISO-2

            // Биллинг-данные
            'billing_name'    => $this->billing_name,
            'billing_email'   => $this->billing_email,
            'billing_phone'   => $this->billing_phone,
            'billing_address' => $this->billing_address, // JSON {line1,line2,city,region,zip,country}

            'is_default'   => (bool) $this->is_default,
            'activity'     => (bool) $this->activity,
            'meta'         => $this->meta,

            // когда подгружается через ->with('paymentMethod')
            'payment_method' => $this->whenLoaded('paymentMethod', fn () => [
                'id'       => $this->paymentMethod->id,
                'code'     => $this->paymentMethod->code,
                'name'     => $this->paymentMethod->name,
                'provider' => $this->paymentMethod->provider,
                'type'     => $this->paymentMethod->type,
            ]),

            'created_at'   => optional($this->created_at)?->toISOString(),
            'updated_at'   => optional($this->updated_at)?->toISOString(),
            'deleted_at'   => optional($this->deleted_at)?->toISOString(),
        ];
    }
}

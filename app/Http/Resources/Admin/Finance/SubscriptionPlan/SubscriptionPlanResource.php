<?php

namespace App\Http\Resources\Admin\Finance\SubscriptionPlan;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionPlanResource extends JsonResource
{
    /**
     * Представление тарифного плана подписки для админ-API.
     *
     * @return array<string,mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            /* ===================== PK ===================== */
            'id' => $this->id,

            /* ===================== Управление ===================== */
            'sort'     => (int) $this->sort,
            'activity' => (bool) $this->activity,

            /* ===================== Витрина / локаль ===================== */
            'locale'      => $this->locale,
            'title'       => $this->title,
            'slug'        => $this->slug,
            'subtitle'    => $this->subtitle,
            'short'       => $this->short,
            'description' => $this->description,

            /* ===================== SEO ===================== */
            'meta_title'    => $this->meta_title,
            'meta_keywords' => $this->meta_keywords,
            'meta_desc'     => $this->meta_desc,

            /* ===================== Публикация / окно доступности ===================== */
            'published_at'    => optional($this->published_at)?->toISOString(),
            'available_from'  => optional($this->available_from)?->toISOString(),
            'available_until' => optional($this->available_until)?->toISOString(),

            /* ===================== Биллинг ===================== */
            'billing_period' => $this->billing_period,        // day|week|month|year
            'interval'       => (int) $this->interval,
            'currency_id'    => (int) $this->currency_id,
            'price'          => (string) $this->price,
            'trial_days'     => (int) $this->trial_days,
            'auto_renew'     => (bool) $this->auto_renew,

            /* ===================== Провайдер оплаты ===================== */
            'provider'         => $this->provider,
            'provider_ref'     => $this->provider_ref,
            'provider_payload' => $this->provider_payload,    // cast array

            /* ===================== Конфиг тарифа ===================== */
            'config' => $this->config, // cast array

            /* ===================== Изображения ===================== */
            // Коллекция изображений (если relation загружен)
            'images' => $this->whenLoaded('images', fn () =>
            SubscriptionPlanImageResource::collection($this->images)
            ),

            // Главное изображение (если нужно отдавать отдельно в админке)
            'primary_image' => $this->whenLoaded('images', fn () =>
            $this->primary_image
                ? new SubscriptionPlanImageResource($this->primary_image)
                : null
            ),

            /* ===================== Service columns ===================== */
            'created_at' => optional($this->created_at)?->toISOString(),
            'updated_at' => optional($this->updated_at)?->toISOString(),
            'deleted_at' => optional($this->deleted_at)?->toISOString(),
        ];
    }
}

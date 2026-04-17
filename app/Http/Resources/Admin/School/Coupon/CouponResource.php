<?php

namespace App\Http\Resources\Admin\School\Coupon;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    /**
     * Представление купона для админ-API.
     *
     * @return array<string,mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'code'               => $this->code,
            'name'               => $this->name,
            'description'        => $this->description,

            'type'               => $this->type,          // percent|fixed|free
            'value'              => $this->value,         // % или сумма
            'currency'           => $this->currency,

            'min_order_total'    => $this->min_order_total,
            'max_uses'           => $this->max_uses,
            'max_uses_per_user'  => $this->max_uses_per_user,
            'used_count'         => $this->used_count,

            'applies_to'         => $this->applies_to,    // any|courses|bundles
            'starts_at'          => optional($this->starts_at)?->toISOString(),
            'ends_at'            => optional($this->ends_at)?->toISOString(),
            'activity'           => (bool) $this->activity,
            'stackable'          => (bool) $this->stackable,

            'meta'               => $this->meta,
            'created_at'         => optional($this->created_at)?->toISOString(),
            'updated_at'         => optional($this->updated_at)?->toISOString(),
            'deleted_at'         => optional($this->deleted_at)?->toISOString(),

            // Удобный флаг актуальности (см. аксессор в модели)
            'is_currently_valid' => $this->when(isset($this->is_currently_valid), $this->is_currently_valid, $this->getAttribute('is_currently_valid')),

            // Короткие сведения о связях (если подгружены)
            'courses' => $this->whenLoaded('courses', fn () => $this->courses->map(fn ($c) => [
                'id'    => $c->id,
                'title' => $c->title,
                'slug'  => $c->slug,
            ])),
            'bundles' => $this->whenLoaded('bundles', fn () => $this->bundles->map(fn ($b) => [
                'id'    => $b->id,
                'title' => $b->title,
                'slug'  => $b->slug,
            ])),
        ];
    }
}

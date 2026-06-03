<?php

namespace App\Http\Resources\Admin\School\SchoolSubscriptionPlan;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolSubscriptionPlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,

            'slug' => $this->slug,

            'title' => $this->translation?->title,
            'subtitle' => $this->translation?->subtitle,
            'short' => $this->translation?->short,
            'description' => $this->translation?->description,

            'meta_title' => $this->translation?->meta_title,
            'meta_keywords' => $this->translation?->meta_keywords,
            'meta_desc' => $this->translation?->meta_desc,

            'published_at' => optional($this->published_at)->toIso8601String(),
            'available_from' => optional($this->available_from)->toIso8601String(),
            'available_until' => optional($this->available_until)->toIso8601String(),

            'billing_period' => $this->billing_period,
            'interval' => (int) $this->interval,
            'currency_id' => $this->currency_id,
            'price' => (string) $this->price,
            'trial_days' => (int) $this->trial_days,
            'auto_renew' => (bool) $this->auto_renew,

            'provider' => $this->provider,
            'provider_ref' => $this->provider_ref,
            'provider_payload' => $this->provider_payload,

            'config' => $this->config,

            'currency' => $this->whenLoaded('currency', fn () => [
                'id' => $this->currency->id,
                'code' => $this->currency->code,
                'name' => $this->currency->name,
                'symbol' => $this->currency->symbol,
            ]),

            'primary_image' => $this->whenLoaded(
                'images',
                fn () => $this->primary_image
                    ? new SchoolSubscriptionPlanImageResource($this->primary_image)
                    : null
            ),

            'images' => SchoolSubscriptionPlanImageResource::collection(
                $this->whenLoaded('images')
            ),

            'translations' => SchoolSubscriptionPlanTranslationResource::collection(
                $this->whenLoaded('translations')
            ),

            'images_count' => $this->when(
                isset($this->images_count),
                fn () => (int) $this->images_count
            ),

            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}

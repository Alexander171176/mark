<?php

namespace App\Http\Resources\Admin\School\SubscriptionPlan;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class SchoolSubscriptionPlanSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $firstImage = $this->whenLoaded('images', fn () => $this->images->first());

        $thumbnailUrl = !($firstImage instanceof MissingValue) && $firstImage
            ? ($firstImage->thumb_url ?? $firstImage->image_url ?? $firstImage->url ?? null)
            : null;

        return [
            'id' => $this->id,

            'slug' => $this->slug,

            'title' => $this->translation?->title,
            'subtitle' => $this->translation?->subtitle,
            'short' => $this->translation?->short,

            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,

            'published_at' => optional($this->published_at)->format('Y-m-d'),
            'available_from' => optional($this->available_from)->format('Y-m-d'),
            'available_until' => optional($this->available_until)->format('Y-m-d'),

            'billing_period' => $this->billing_period,
            'interval' => (int) $this->interval,
            'currency_id' => $this->currency_id,
            'price' => (string) $this->price,
            'trial_days' => (int) $this->trial_days,
            'auto_renew' => (bool) $this->auto_renew,

            'thumbnail_url' => $thumbnailUrl,
        ];
    }
}

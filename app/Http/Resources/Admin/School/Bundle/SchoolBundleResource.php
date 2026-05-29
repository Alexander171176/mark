<?php

namespace App\Http\Resources\Admin\School\Bundle;

use App\Http\Resources\Admin\School\Course\SchoolCourseSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolBundleResource extends JsonResource
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

            'published_at' => $this->published_at?->format('Y-m-d'), // YYYY-MM-DD

            'views' => (int) $this->views,
            'likes' => (int) $this->likes,
            'meta' => $this->meta,

            'primary_image' => $this->whenLoaded(
                'images',
                fn () => $this->primary_image
                    ? new SchoolBundleImageResource($this->primary_image)
                    : null
            ),

            'images' => SchoolBundleImageResource::collection(
                $this->whenLoaded('images')
            ),

            'translations' => SchoolBundleTranslationResource::collection(
                $this->whenLoaded('translations')
            ),

            'courses' => SchoolCourseSharedResource::collection(
                $this->whenLoaded('courses')
            ),

            'prices' => $this->whenLoaded('prices', fn () => $this->prices->map(fn ($price) => [
                'id' => $price->id,
                'school_bundle_id' => $price->school_bundle_id,
                'currency_id' => $price->currency_id,

                'price' => (string) $price->price,
                'sale_price' => $price->sale_price !== null ? (string) $price->sale_price : null,
                'compare_at_price' => $price->compare_at_price !== null ? (string) $price->compare_at_price : null,

                'effective_price' => $price->effective_price ?? null,
                'has_discount' => (bool) ($price->has_discount ?? false),
                'discount_amount' => $price->discount_amount ?? null,
                'discount_percent' => $price->discount_percent ?? null,

                'starts_at' => optional($price->starts_at)->toIso8601String(),
                'ends_at' => optional($price->ends_at)->toIso8601String(),

                'activity' => (bool) $price->activity,
                'sort' => (int) $price->sort,
                'meta' => $price->meta,
            ])),

            'order_items' => $this->whenLoaded('orderItems', fn () => $this->orderItems->map(fn ($item) => [
                'id' => $item->id,
                'school_order_id' => $item->school_order_id,
                'title' => $item->title,
                'currency' => $item->currency,
                'quantity' => (int) $item->quantity,
                'unit_price' => (string) $item->unit_price,
                'discount' => (string) $item->discount,
                'total' => (string) $item->total,
            ])),

            'courses_count' => $this->when(isset($this->courses_count), fn () => (int) $this->courses_count),
            'images_count' => $this->when(isset($this->images_count), fn () => (int) $this->images_count),
            'prices_count' => $this->when(isset($this->prices_count), fn () => (int) $this->prices_count),
            'order_items_count' => $this->when(isset($this->order_items_count), fn () => (int) $this->order_items_count),

            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}

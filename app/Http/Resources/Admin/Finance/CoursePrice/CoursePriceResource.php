<?php

namespace App\Http\Resources\Admin\Finance\CoursePrice;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CoursePriceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'        => (int) $this->id,
            'course_id' => (int) $this->course_id,

            'currency_id' => (int) $this->currency_id,

            // ✅ plain currency (как instructorProfile / categories в CourseResource)
            'currency' => $this->whenLoaded('currency', function () {
                return [
                    'id'     => (int) $this->currency->id,
                    'code'   => $this->currency->code,
                    'name'   => $this->currency->name,
                    'symbol' => $this->currency->symbol,
                ];
            }),

            // ✅ plain course (для общего списка)
            'course' => $this->whenLoaded('course', function () {
                return [
                    'id'     => (int) $this->course->id,
                    'title'  => $this->course->title,
                    'slug'   => $this->course->slug,
                    'locale' => $this->course->locale,
                ];
            }),

            // Цены
            'price'            => (string) $this->price,
            'sale_price'       => $this->sale_price !== null ? (string) $this->sale_price : null,
            'compare_at_price' => $this->compare_at_price !== null ? (string) $this->compare_at_price : null,

            // Главная цена (если это accessor в модели — ок)
            'effective_price'  => (string) $this->effective_price,

            'starts_at' => $this->starts_at?->toISOString(),
            'ends_at'   => $this->ends_at?->toISOString(),

            // Управление
            'activity' => (bool) $this->activity,
            'sort'     => (int) $this->sort,

            // meta
            'meta' => $this->meta,

            // Таймстампы
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'deleted_at' => $this->deleted_at?->toISOString(),

            // Вычисляемые (если accessor’ы — оставляем как есть)
            'has_discount'     => (bool) $this->has_discount,
            'discount_amount'  => $this->discount_amount !== null ? (string) $this->discount_amount : null,
            'discount_percent' => $this->discount_percent !== null ? (float) $this->discount_percent : null,
        ];
    }
}

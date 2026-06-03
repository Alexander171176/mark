<?php

namespace App\Http\Resources\Admin\School\SchoolCoursePrice;

use App\Http\Resources\Admin\School\SchoolCourse\SchoolCourseSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolCoursePriceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'school_course_id' => $this->school_course_id,
            'currency_id' => $this->currency_id,

            'currency' => $this->whenLoaded('currency', fn () => [
                'id' => $this->currency->id,
                'code' => $this->currency->code,
                'name' => $this->currency->name,
                'symbol' => $this->currency->symbol,
            ]),

            'course' => new SchoolCourseSharedResource(
                $this->whenLoaded('course')
            ),

            'price' => (string) $this->price,
            'sale_price' => $this->sale_price !== null ? (string) $this->sale_price : null,
            'compare_at_price' => $this->compare_at_price !== null ? (string) $this->compare_at_price : null,

            'effective_price' => (string) $this->effective_price,
            'has_discount' => (bool) $this->has_discount,
            'discount_amount' => $this->discount_amount !== null ? (string) $this->discount_amount : null,
            'discount_percent' => $this->discount_percent !== null ? (float) $this->discount_percent : null,

            'starts_at' => optional($this->starts_at)->toIso8601String(),
            'ends_at' => optional($this->ends_at)->toIso8601String(),

            'activity' => (bool) $this->activity,
            'sort' => (int) $this->sort,
            'meta' => $this->meta,

            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}

<?php

namespace App\Http\Resources\Admin\School\SchoolFaqCategory;

use App\Http\Resources\Admin\School\SchoolFaq\SchoolFaqResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolFaqCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,
            'slug' => $this->slug,

            'title' => $this->translation?->title,
            'description' => $this->translation?->description,

            'translations' => SchoolFaqCategoryTranslationResource::collection(
                $this->whenLoaded('translations')
            ),

            'faqs' => SchoolFaqResource::collection(
                $this->whenLoaded('faqs')
            ),

            'faqs_count' => $this->when(
                isset($this->faqs_count),
                fn () => (int) $this->faqs_count
            ),

            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}

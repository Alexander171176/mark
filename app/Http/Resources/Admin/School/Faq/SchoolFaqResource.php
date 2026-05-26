<?php

namespace App\Http\Resources\Admin\School\Faq;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolFaqResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'school_faq_category_id' => $this->school_faq_category_id,

            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,

            'question' => $this->translation?->question,
            'answer' => $this->translation?->answer,

            'meta_title' => $this->translation?->meta_title,
            'meta_description' => $this->translation?->meta_description,

            'category' => new SchoolFaqCategoryResource(
                $this->whenLoaded('category')
            ),

            'translations' => SchoolFaqTranslationResource::collection(
                $this->whenLoaded('translations')
            ),

            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}

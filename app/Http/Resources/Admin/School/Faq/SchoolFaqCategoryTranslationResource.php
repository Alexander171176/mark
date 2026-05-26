<?php

namespace App\Http\Resources\Admin\School\Faq;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolFaqCategoryTranslationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'school_faq_category_id' => $this->school_faq_category_id,
            'locale' => $this->locale,
            'title' => $this->title,
            'description' => $this->description,
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
